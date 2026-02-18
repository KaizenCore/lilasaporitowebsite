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
    public function show(Request $request, $classSlug)
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

        // Handle quantity (for group bookings)
        $quantity = max(1, min(10, (int) $request->query('quantity', 1)));
        $quantity = min($quantity, $class->spots_available);

        // Handle party pricing
        $partyPackage = null;
        $partyGuests = null;
        $selectedAddons = [];
        $totalPriceCents = $class->price_cents * $quantity;

        if ($class->is_party_event) {
            $quantity = 1; // Party events are always 1 booking
            $partyPackage = $request->query('package', 'small');
            $partyGuests = (int) $request->query('guests', $class->small_party_size ?? 6);

            // Validate package
            if (!in_array($partyPackage, ['small', 'large'])) {
                $partyPackage = 'small';
            }

            // Validate guest count
            $minGuests = 1;
            $maxGuests = $class->max_party_size ?? 20;
            $partyGuests = max($minGuests, min($maxGuests, $partyGuests));

            // Parse selected add-ons (comma-separated indexes)
            $addonsParam = $request->query('addons', '');
            if (!empty($addonsParam)) {
                $selectedAddons = array_map('intval', explode(',', $addonsParam));
                // Filter to valid indexes
                $availableAddons = $class->party_addons ?? [];
                $selectedAddons = array_filter($selectedAddons, fn($idx) => isset($availableAddons[$idx]));
                $selectedAddons = array_values($selectedAddons);
            }

            // Calculate total price including add-ons
            $totalPriceCents = $class->calculateFullPartyPrice($partyPackage, $partyGuests, $selectedAddons);
        }

        return view('checkout.show', [
            'class' => $class,
            'user' => Auth::user(),
            'quantity' => $quantity,
            'partyPackage' => $partyPackage,
            'partyGuests' => $partyGuests,
            'selectedAddons' => $selectedAddons,
            'totalPriceCents' => $totalPriceCents,
        ]);
    }

    /**
     * Create payment intent (AJAX)
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'art_class_id' => 'required|exists:art_classes,id',
            'quantity' => 'nullable|integer|min:1|max:10',
            'party_package' => 'nullable|in:small,large',
            'party_guests' => 'nullable|integer|min:1|max:50',
            'selected_addons' => 'nullable|array',
            'selected_addons.*' => 'integer|min:0',
        ]);

        try {
            $class = ArtClass::findOrFail($request->art_class_id);
            $quantity = max(1, min(10, (int) ($request->quantity ?? 1)));

            // Double-check availability
            if ($class->is_full || $class->is_past) {
                return response()->json([
                    'error' => 'This class is no longer available.'
                ], 400);
            }

            // Check enough spots for requested quantity
            if ($class->spots_available < $quantity) {
                return response()->json([
                    'error' => "Only {$class->spots_available} spots remaining."
                ], 400);
            }

            // Calculate price (handle party events)
            $priceCents = $class->price_cents * $quantity;
            $metadata = [
                'art_class_id' => $class->id,
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'class_title' => $class->title,
                'class_date' => $class->class_date->toDateTimeString(),
                'quantity' => $quantity,
            ];

            if ($class->is_party_event && $request->party_package) {
                $quantity = 1;
                $package = $request->party_package;
                $guests = (int) $request->party_guests;
                $selectedAddons = $request->selected_addons ?? [];

                // Validate addon indexes
                $availableAddons = $class->party_addons ?? [];
                $selectedAddons = array_filter($selectedAddons, fn($idx) => isset($availableAddons[$idx]));
                $selectedAddons = array_values($selectedAddons);

                $priceCents = $class->calculateFullPartyPrice($package, $guests, $selectedAddons);

                $metadata['party_package'] = $package;
                $metadata['party_guests'] = $guests;
                $metadata['quantity'] = 1;
                if (!empty($selectedAddons)) {
                    $metadata['selected_addons'] = implode(',', $selectedAddons);
                }
            }

            // Create payment intent
            $description = $quantity > 1
                ? "FrizzBoss - {$class->title} x{$quantity}"
                : "FrizzBoss - {$class->title}";

            $paymentIntent = $this->stripeService->createPaymentIntent(
                $priceCents,
                $description,
                $metadata
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
     * Confirm payment and create booking(s) (called after Stripe payment succeeds)
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
            $quantity = max(1, (int) ($paymentIntent->metadata->quantity ?? 1));

            // Prevent duplicate processing of same payment intent
            $existingPayment = \App\Models\Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();
            if ($existingPayment) {
                $firstBookingId = $existingPayment->booking_id;
                return response()->json([
                    'success' => true,
                    'booking_id' => $firstBookingId,
                    'message' => 'Payment already processed.'
                ]);
            }

            // Create bookings (one per ticket)
            $bookings = [];
            for ($i = 0; $i < $quantity; $i++) {
                $bookings[] = Booking::create([
                    'user_id' => Auth::id(),
                    'art_class_id' => $class->id,
                    'payment_status' => 'completed',
                    'attendance_status' => 'booked',
                    'booking_notes' => $quantity > 1 ? "Group booking: ticket " . ($i + 1) . " of {$quantity} (PI: {$paymentIntent->id})" : null,
                ]);
            }

            $firstBooking = $bookings[0];

            // Create one payment record linked to first booking
            $payment = \App\Models\Payment::create([
                'booking_id' => $firstBooking->id,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                'stripe_customer_id' => $paymentIntent->customer ?? null,
                'amount_cents' => $paymentIntent->amount,
                'currency' => $paymentIntent->currency,
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
                'status' => 'succeeded',
                'is_test' => !$paymentIntent->livemode,
                'metadata' => [
                    'class_title' => $class->title,
                    'class_date' => $class->class_date->toDateTimeString(),
                    'user_email' => Auth::user()->email,
                    'quantity' => $quantity,
                    'booking_ids' => collect($bookings)->pluck('id')->toArray(),
                ],
            ]);

            // Calculate Stripe fees
            $payment->calculateStripeFee();
            $payment->save();

            $ticketCodes = collect($bookings)->pluck('ticket_code')->toArray();

            Log::info('Booking(s) created via confirm endpoint', [
                'booking_ids' => collect($bookings)->pluck('id')->toArray(),
                'payment_id' => $payment->id,
                'ticket_codes' => $ticketCodes,
                'quantity' => $quantity,
            ]);

            // Send confirmation email for each booking
            foreach ($bookings as $booking) {
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
                }
            }

            // Flash all booking IDs for the success page
            session()->flash('purchased_booking_ids', collect($bookings)->pluck('id')->toArray());

            return response()->json([
                'success' => true,
                'booking_id' => $firstBooking->id,
                'ticket_codes' => $ticketCodes,
                'quantity' => $quantity,
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

        // Check for group booking (multiple tickets purchased together)
        $purchasedIds = session('purchased_booking_ids');
        $allBookings = collect([$booking]);

        if ($purchasedIds && count($purchasedIds) > 1) {
            $allBookings = Booking::whereIn('id', $purchasedIds)
                ->where('user_id', Auth::id())
                ->with('artClass')
                ->get();
        } elseif ($booking->payment && $booking->payment->metadata) {
            // Fallback: check payment metadata for booking_ids
            $metaBookingIds = $booking->payment->metadata['booking_ids'] ?? null;
            if ($metaBookingIds && count($metaBookingIds) > 1) {
                $allBookings = Booking::whereIn('id', $metaBookingIds)
                    ->where('user_id', Auth::id())
                    ->with('artClass')
                    ->get();
            }
        }

        return view('checkout.success', [
            'booking' => $booking,
            'allBookings' => $allBookings,
        ]);
    }
}
