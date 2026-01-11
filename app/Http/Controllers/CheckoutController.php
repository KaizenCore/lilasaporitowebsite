<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\ArtClass;
use App\Models\Booking;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Show checkout page for a class
     */
    public function show($classSlug)
    {
        $class = ArtClass::where('slug', $classSlug)
            ->where('status', 'published')
            ->firstOrFail();

        // Check if class is in the future
        if ($class->is_past) {
            return redirect()->route('classes.show', $class->slug)
                ->with('error', 'This class has already occurred.');
        }

        // Check if class is full
        if ($class->is_full) {
            return redirect()->route('classes.show', $class->slug)
                ->with('error', 'This class is currently full.');
        }

        // Check if user already has a booking for this class
        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('art_class_id', $class->id)
            ->whereIn('payment_status', ['pending', 'completed'])
            ->whereIn('attendance_status', ['booked', 'attended'])
            ->first();

        if ($existingBooking) {
            return redirect()->route('bookings.index')
                ->with('error', 'You already have a booking for this class.');
        }

        return view('checkout.show', [
            'class' => $class,
            'user' => Auth::user(),
        ]);
    }

    /**
     * Create payment intent (AJAX)
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'art_class_id' => 'required|exists:art_classes,id',
        ]);

        try {
            $class = ArtClass::findOrFail($request->art_class_id);

            // Double-check availability
            if ($class->is_full || $class->is_past) {
                return response()->json([
                    'error' => 'This class is no longer available.'
                ], 400);
            }

            // Check for existing booking
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('art_class_id', $class->id)
                ->whereIn('payment_status', ['pending', 'completed'])
                ->whereIn('attendance_status', ['booked', 'attended'])
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'error' => 'You already have a booking for this class.'
                ], 400);
            }

            // Create payment intent
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $class->price_cents,
                "FrizzBoss - {$class->title}",
                [
                    'art_class_id' => $class->id,
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email,
                    'class_title' => $class->title,
                    'class_date' => $class->class_date->toDateTimeString(),
                ]
            );

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Intent Creation Error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'art_class_id' => $request->art_class_id,
            ]);

            return response()->json([
                'error' => 'Failed to initialize payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Confirm payment and create booking (called after Stripe payment succeeds)
     */
    public function confirmPayment(Request $request)
    {
        Log::info('confirmPayment called', [
            'payment_intent_id' => $request->payment_intent_id,
            'art_class_id' => $request->art_class_id,
            'user_id' => Auth::id(),
        ]);

        $request->validate([
            'payment_intent_id' => 'required|string',
            'art_class_id' => 'required|exists:art_classes,id',
        ]);

        try {
            // Retrieve the payment intent from Stripe to verify it succeeded
            $paymentIntent = $this->stripeService->retrievePaymentIntent($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'Payment has not been completed.'
                ], 400);
            }

            // Check the metadata matches
            if ($paymentIntent->metadata->art_class_id != $request->art_class_id ||
                $paymentIntent->metadata->user_id != Auth::id()) {
                return response()->json([
                    'error' => 'Payment verification failed.'
                ], 400);
            }

            $class = ArtClass::findOrFail($request->art_class_id);

            // Check if booking already exists (prevent duplicates, but allow rebooking cancelled ones)
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('art_class_id', $class->id)
                ->where('payment_status', 'completed')
                ->where('attendance_status', '!=', 'cancelled')
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => true,
                    'booking_id' => $existingBooking->id,
                    'message' => 'Booking already exists.'
                ]);
            }

            // Create the booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'art_class_id' => $class->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ]);

            // Create the payment record
            $payment = \App\Models\Payment::create([
                'booking_id' => $booking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                'stripe_customer_id' => $paymentIntent->customer ?? null,
                'amount_cents' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                'status' => 'succeeded',
                'metadata' => [
                    'class_title' => $class->title,
                    'class_date' => $class->class_date->toDateTimeString(),
                    'user_email' => Auth::user()->email,
                ],
            ]);

            // Calculate Stripe fees
            $payment->calculateStripeFee();
            $payment->save();

            Log::info('Booking created via confirm endpoint', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'ticket_code' => $booking->ticket_code,
            ]);

            // Send confirmation email
            try {
                $booking->load('artClass', 'user');
                if ($booking->user?->email) {
                    Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
                    Log::info('Booking confirmation email sent', ['booking_id' => $booking->id]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send booking confirmation email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the booking if email fails
            }

            return response()->json([
                'success' => true,
                'booking_id' => $booking->id,
                'ticket_code' => $booking->ticket_code,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'payment_intent_id' => $request->payment_intent_id,
            ]);

            return response()->json([
                'error' => 'Failed to confirm payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment success page
     */
    public function success(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        // Load relationships
        $booking->load('artClass', 'payment');

        return view('checkout.success', [
            'booking' => $booking,
        ]);
    }
}
