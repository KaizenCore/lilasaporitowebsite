<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyPricingConfig;
use Illuminate\Http\Request;

class PartyPricingController extends Controller
{
    /**
     * Display a listing of pricing configs.
     */
    public function index()
    {
        $configs = PartyPricingConfig::withCount('partyBookings')
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.parties.pricing.index', compact('configs'));
    }

    /**
     * Show the form for creating a new pricing config.
     */
    public function create()
    {
        return view('admin.parties.pricing.create');
    }

    /**
     * Store a newly created pricing config in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pricing_type' => 'required|in:flat_per_person,tiered,custom_quote',
            'base_price_cents' => 'nullable|integer|min:0',
            'minimum_guests' => 'required|integer|min:1',
            'maximum_guests' => 'nullable|integer|min:1',
            'tier_pricing' => 'nullable|array',
            'tier_pricing.*.min' => 'required_with:tier_pricing|integer|min:1',
            'tier_pricing.*.max' => 'required_with:tier_pricing|integer|min:1',
            'tier_pricing.*.price_cents' => 'required_with:tier_pricing|integer|min:0',
            'lila_venue_fee_cents' => 'nullable|integer|min:0',
            'lila_venue_per_person_cents' => 'nullable|integer|min:0',
            'lila_venue_max_capacity' => 'required|integer|min:1|max:50',
            'custom_painting_fee_cents' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');

        // If this is set as default, unset other defaults
        if ($validated['is_default']) {
            PartyPricingConfig::where('is_default', true)->update(['is_default' => false]);
        }

        // Clean up tier pricing if not tiered type
        if ($validated['pricing_type'] !== 'tiered') {
            $validated['tier_pricing'] = null;
        }

        PartyPricingConfig::create($validated);

        return redirect()->route('admin.parties.pricing.index')
            ->with('success', 'Pricing configuration created successfully.');
    }

    /**
     * Show the form for editing the specified pricing config.
     */
    public function edit(PartyPricingConfig $pricing)
    {
        return view('admin.parties.pricing.edit', compact('pricing'));
    }

    /**
     * Update the specified pricing config in storage.
     */
    public function update(Request $request, PartyPricingConfig $pricing)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'pricing_type' => 'required|in:flat_per_person,tiered,custom_quote',
            'base_price_cents' => 'nullable|integer|min:0',
            'minimum_guests' => 'required|integer|min:1',
            'maximum_guests' => 'nullable|integer|min:1',
            'tier_pricing' => 'nullable|array',
            'tier_pricing.*.min' => 'required_with:tier_pricing|integer|min:1',
            'tier_pricing.*.max' => 'required_with:tier_pricing|integer|min:1',
            'tier_pricing.*.price_cents' => 'required_with:tier_pricing|integer|min:0',
            'lila_venue_fee_cents' => 'nullable|integer|min:0',
            'lila_venue_per_person_cents' => 'nullable|integer|min:0',
            'lila_venue_max_capacity' => 'required|integer|min:1|max:50',
            'custom_painting_fee_cents' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');

        // If this is set as default, unset other defaults
        if ($validated['is_default']) {
            PartyPricingConfig::where('is_default', true)
                ->where('id', '!=', $pricing->id)
                ->update(['is_default' => false]);
        }

        // Clean up tier pricing if not tiered type
        if ($validated['pricing_type'] !== 'tiered') {
            $validated['tier_pricing'] = null;
        }

        $pricing->update($validated);

        return redirect()->route('admin.parties.pricing.index')
            ->with('success', 'Pricing configuration updated successfully.');
    }

    /**
     * Remove the specified pricing config from storage.
     */
    public function destroy(PartyPricingConfig $pricing)
    {
        // Check if pricing is used in active bookings
        $activeBookings = $pricing->partyBookings()
            ->whereNotIn('status', ['completed', 'cancelled', 'declined'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->route('admin.parties.pricing.index')
                ->with('error', 'Cannot delete pricing that is used in active bookings.');
        }

        $pricing->delete();

        return redirect()->route('admin.parties.pricing.index')
            ->with('success', 'Pricing configuration deleted successfully.');
    }
}
