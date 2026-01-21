<?php

namespace App\Http\Controllers;

use App\Models\PartyBooking;
use App\Models\PartyPayment;
use App\Services\StripeService;
use App\Mail\PartyBookingConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PartyCheckoutController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display the checkout page to accept quote and pay.
     */
    public function show(PartyBooking $partyBooking)
    {
        // Ensure user owns this booking
        if ($partyBooking->user_id !== auth()->id()) {
            abort(403, 'You do not have access to this booking.');
        }

        // Check if quote can be accepted
        if (!$partyBooking->canAcceptQuote() && !$partyBooking->canPayDeposit() && !$partyBooking->canPayFinal()) {
            return redirect()->route('parties.booking.show', $partyBooking)
                ->with('error', 'This quote cannot be paid at this time.');
        }

        if ($partyBooking->isQuoteExpired()) {
            return redirect()->route('parties.booking.show', $partyBooking)
                ->with('error', 'This quote has expired. Please contact us for a new quote.');
        }

        $partyBooking->load(['partyPainting', 'pricingConfig']);
        $selectedAddons = $partyBooking->getSelectedAddons();

        // Determine payment amount
        $paymentType = 'full';
        $paymentAmount = $partyBooking->quoted_total_cents;

        if ($partyBooking->deposit_required_cents > 0 && $partyBooking->deposit_paid_cents < $partyBooking->deposit_required_cents) {
            $paymentType = 'deposit';
            $paymentAmount = $partyBooking->deposit_required_cents;
        } elseif ($partyBooking->total_paid_cents > 0 && $partyBooking->total_paid_cents < $partyBooking->quoted_total_cents) {
            $paymentType = 'final';
            $paymentAmount = $partyBooking->remaining_balance;
        }

        return view('parties.checkout', compact('partyBooking', 'selectedAddons', 'paymentType', 'paymentAmount'));
    }

    /**
     * Accept the quote (mark as accepted).
     */
    public function acceptQuote(PartyBooking $partyBooking)
    {
        // Ensure user owns this booking
        if ($partyBooking->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$partyBooking->canAcceptQuote()) {
            return back()->with('error', 'This quote cannot be accepted.');
        }

        $partyBooking->update([
            'status' => PartyBooking::STATUS_ACCEPTED,
        ]);

        return redirect()->route('parties.checkout', $partyBooking)
            ->with('success', 'Quote accepted! Please complete your payment.');
    }

    /**
     * Create a payment intent.
     */
    public function createPaymentIntent(Request $request, PartyBooking $partyBooking)
    {
        // Ensure user owns this booking
        if ($partyBooking->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'payment_type' => 'required|in:deposit,final,full',
        ]);

        // Determine payment amount
        $paymentAmount = match ($validated['payment_type']) {
            'deposit' => $partyBooking->deposit_required_cents,
            'final' => $partyBooking->remaining_balance,
            'full' => $partyBooking->quoted_total_cents,
        };

        if ($paymentAmount <= 0) {
            return response()->json(['error' => 'Invalid payment amount'], 400);
        }

        $description = "FrizzBoss Party Booking {$partyBooking->booking_number} - " . ucfirst($validated['payment_type']);

        $metadata = [
            'order_type' => 'party_booking',
            'booking_number' => $partyBooking->booking_number,
            'party_booking_id' => $partyBooking->id,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'payment_type' => $validated['payment_type'],
        ];

        try {
            $paymentIntent = $this->stripeService->createPaymentIntent(
                $paymentAmount,
                $description,
                $metadata
            );

            // Create pending payment record
            PartyPayment::create([
                'party_booking_id' => $partyBooking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount_cents' => $paymentAmount,
                'payment_type' => $validated['payment_type'],
                'status' => PartyPayment::STATUS_PENDING,
                'is_test' => config('services.stripe.test_mode', false),
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json(['error' => 'Unable to create payment. Please try again.'], 500);
        }
    }

    /**
     * Display success page after payment.
     */
    public function success(Request $request, PartyBooking $partyBooking)
    {
        // Ensure user owns this booking
        if ($partyBooking->user_id !== auth()->id()) {
            abort(403);
        }

        $partyBooking->load(['partyPainting', 'pricingConfig', 'payments']);
        $selectedAddons = $partyBooking->getSelectedAddons();

        return view('parties.checkout-success', compact('partyBooking', 'selectedAddons'));
    }

    /**
     * Check payment status (AJAX polling).
     */
    public function checkPaymentStatus(Request $request, string $paymentIntentId)
    {
        $payment = PartyPayment::where('stripe_payment_intent_id', $paymentIntentId)
            ->where('status', PartyPayment::STATUS_SUCCEEDED)
            ->first();

        if ($payment) {
            return response()->json([
                'success' => true,
                'booking_id' => $payment->party_booking_id,
            ]);
        }

        return response()->json([
            'success' => false,
        ]);
    }
}
