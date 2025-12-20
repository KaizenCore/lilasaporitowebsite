<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            // Verify webhook signature
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled webhook event type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'metadata' => $paymentIntent->metadata,
        ]);

        DB::beginTransaction();

        try {
            $metadata = $paymentIntent->metadata;

            // Get the art class
            $artClass = ArtClass::findOrFail($metadata->art_class_id);

            // Check if booking already exists (prevent duplicates)
            $existingBooking = Booking::whereHas('payment', function ($query) use ($paymentIntent) {
                $query->where('stripe_payment_intent_id', $paymentIntent->id);
            })->first();

            if ($existingBooking) {
                Log::info('Booking already exists for payment intent', [
                    'payment_intent_id' => $paymentIntent->id,
                    'booking_id' => $existingBooking->id,
                ]);
                DB::commit();
                return;
            }

            // Create the booking
            $booking = Booking::create([
                'user_id' => $metadata->user_id,
                'art_class_id' => $metadata->art_class_id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ]);

            // Create the payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                'stripe_customer_id' => $paymentIntent->customer ?? null,
                'amount_cents' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                'status' => 'succeeded',
                'metadata' => [
                    'class_title' => $metadata->class_title,
                    'class_date' => $metadata->class_date,
                    'user_email' => $metadata->user_email,
                ],
            ]);

            // Calculate Stripe fees
            $payment->calculateStripeFee();
            $payment->save();

            Log::info('Booking and payment created successfully', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'ticket_code' => $booking->ticket_code,
            ]);

            DB::commit();

            // Here you could dispatch an event to send confirmation email
            // event(new BookingConfirmed($booking));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create booking from webhook', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            'metadata' => $paymentIntent->metadata,
        ]);

        // Find existing payment record if any and mark as failed
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->markAsFailed($paymentIntent->last_payment_error->message ?? 'Payment failed');
        }
    }
}
