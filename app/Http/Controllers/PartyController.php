<?php

namespace App\Http\Controllers;

use App\Models\PartyAddon;
use App\Models\PartyAvailabilitySlot;
use App\Models\PartyPainting;
use App\Models\PartyPricingConfig;
use Illuminate\Http\Request;

class PartyController extends Controller
{
    /**
     * Display the party booking landing page.
     */
    public function index()
    {
        $paintings = PartyPainting::active()->ordered()->limit(6)->get();
        $addons = PartyAddon::active()->ordered()->get();
        $defaultPricing = PartyPricingConfig::active()->default()->first()
            ?? PartyPricingConfig::active()->first();

        return view('parties.index', compact('paintings', 'addons', 'defaultPricing'));
    }

    /**
     * Display the painting gallery.
     */
    public function paintings(Request $request)
    {
        $query = PartyPainting::active()->ordered();

        if ($request->filled('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        $paintings = $query->paginate(12);

        return view('parties.paintings', compact('paintings'));
    }

    /**
     * Get available dates for calendar (AJAX).
     */
    public function availableDates(Request $request)
    {
        $startDate = $request->get('start', now()->toDateString());
        $endDate = $request->get('end', now()->addMonths(3)->toDateString());

        $slots = PartyAvailabilitySlot::available()
            ->inDateRange($startDate, $endDate)
            ->ordered()
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'date' => $slot->date->format('Y-m-d'),
                    'start_time' => $slot->start_time->format('H:i'),
                    'end_time' => $slot->end_time->format('H:i'),
                    'formatted_time' => $slot->formatted_time_range,
                    'formatted_date' => $slot->formatted_date,
                ];
            });

        return response()->json($slots);
    }

    /**
     * Get pricing estimate (AJAX).
     */
    public function getPricingEstimate(Request $request)
    {
        $validated = $request->validate([
            'guest_count' => 'required|integer|min:1',
            'location_type' => 'required|in:lila_hosts,customer_location',
            'wants_custom_painting' => 'nullable|boolean',
            'addon_ids' => 'nullable|array',
        ]);

        $pricingConfig = PartyPricingConfig::active()->default()->first()
            ?? PartyPricingConfig::active()->first();

        if (!$pricingConfig) {
            return response()->json(['error' => 'No pricing available'], 400);
        }

        $guestCount = $validated['guest_count'];
        $locationType = $validated['location_type'];

        // Check Lila venue capacity
        if ($locationType === 'lila_hosts' && $guestCount > $pricingConfig->lila_venue_max_capacity) {
            return response()->json([
                'error' => "Lila's studio can only accommodate up to {$pricingConfig->lila_venue_max_capacity} guests. Please choose 'Your Location' for larger parties.",
                'max_capacity' => $pricingConfig->lila_venue_max_capacity,
            ], 400);
        }

        // Calculate base price
        $subtotal = $pricingConfig->calculateBasePrice($guestCount);

        // Calculate venue fee
        $venueFee = $pricingConfig->calculateVenueFee($locationType, $guestCount);

        // Calculate custom painting fee
        $customPaintingFee = 0;
        if ($request->boolean('wants_custom_painting') && $pricingConfig->custom_painting_fee_cents) {
            $customPaintingFee = $pricingConfig->custom_painting_fee_cents;
        }

        // Calculate addons
        $addonsTotal = 0;
        if (!empty($validated['addon_ids'])) {
            $addons = PartyAddon::whereIn('id', $validated['addon_ids'])->active()->get();
            foreach ($addons as $addon) {
                $addonsTotal += $addon->calculatePrice($guestCount);
            }
        }

        $total = $subtotal + $venueFee + $customPaintingFee + $addonsTotal;

        return response()->json([
            'pricing_type' => $pricingConfig->pricing_type,
            'subtotal_cents' => $subtotal,
            'venue_fee_cents' => $venueFee,
            'custom_painting_fee_cents' => $customPaintingFee,
            'addons_cents' => $addonsTotal,
            'total_cents' => $total,
            'formatted' => [
                'subtotal' => '$' . number_format($subtotal / 100, 2),
                'venue_fee' => '$' . number_format($venueFee / 100, 2),
                'custom_painting_fee' => '$' . number_format($customPaintingFee / 100, 2),
                'addons' => '$' . number_format($addonsTotal / 100, 2),
                'total' => '$' . number_format($total / 100, 2),
            ],
            'is_estimate' => $pricingConfig->pricing_type === 'custom_quote',
            'note' => $pricingConfig->pricing_type === 'custom_quote'
                ? 'This is an estimate. Final pricing will be provided in your quote.'
                : null,
        ]);
    }
}
