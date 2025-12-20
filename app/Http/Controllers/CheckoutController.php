<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
use App\Models\Booking;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->middleware('auth');
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
