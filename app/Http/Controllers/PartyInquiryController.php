<?php

namespace App\Http\Controllers;

use App\Models\PartyAddon;
use App\Models\PartyAvailabilitySlot;
use App\Models\PartyBooking;
use App\Models\PartyPainting;
use App\Models\PartyPricingConfig;
use App\Mail\PartyInquiryReceived;
use App\Mail\PartyInquiryAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PartyInquiryController extends Controller
{
    /**
     * Display the inquiry form.
     */
    public function create()
    {
        $paintings = PartyPainting::active()->ordered()->get();
        $addons = PartyAddon::active()->ordered()->get();
        $pricingConfig = PartyPricingConfig::active()->default()->first()
            ?? PartyPricingConfig::active()->first();

        // Get available dates for the next 3 months
        $availableSlots = PartyAvailabilitySlot::available()
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addMonths(3)->toDateString())
            ->ordered()
            ->get()
            ->groupBy(function ($slot) {
                return $slot->date->format('Y-m-d');
            });

        return view('parties.inquire', compact('paintings', 'addons', 'pricingConfig', 'availableSlots'));
    }

    /**
     * Store a new party inquiry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Event details
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|date_format:H:i',
            'alternate_date' => 'nullable|date|after_or_equal:today',
            'alternate_time' => 'nullable|date_format:H:i',
            'event_type' => 'required|in:birthday,corporate,bridal_shower,bachelorette,team_building,other',
            'event_details' => 'nullable|string|max:1000',
            'honoree_name' => 'nullable|string|max:255',
            'honoree_age' => 'nullable|integer|min:1|max:120',

            // Party details
            'guest_count' => 'required|integer|min:1|max:100',
            'location_type' => 'required|in:lila_hosts,customer_location',
            'customer_address' => 'required_if:location_type,customer_location|nullable|string|max:500',
            'customer_city' => 'required_if:location_type,customer_location|nullable|string|max:255',
            'customer_state' => 'required_if:location_type,customer_location|nullable|string|max:255',
            'customer_zip' => 'required_if:location_type,customer_location|nullable|string|max:20',

            // Painting selection
            'party_painting_id' => 'nullable|exists:party_paintings,id',
            'wants_custom_painting' => 'nullable|boolean',
            'custom_painting_description' => 'nullable|string|max:500',

            // Add-ons
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:party_addons,id',

            // Contact info
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);

        // Check Lila venue capacity
        $pricingConfig = PartyPricingConfig::active()->default()->first();
        if ($validated['location_type'] === 'lila_hosts' && $pricingConfig) {
            if ($validated['guest_count'] > $pricingConfig->lila_venue_max_capacity) {
                return back()->withErrors([
                    'guest_count' => "Lila's studio can only accommodate up to {$pricingConfig->lila_venue_max_capacity} guests for hosted parties.",
                ])->withInput();
            }
        }

        // Create the booking inquiry
        $booking = PartyBooking::create([
            'user_id' => auth()->id(),
            'status' => PartyBooking::STATUS_INQUIRY,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'] ?? null,
            'alternate_date' => $validated['alternate_date'] ?? null,
            'alternate_time' => $validated['alternate_time'] ?? null,
            'event_type' => $validated['event_type'],
            'event_details' => $validated['event_details'] ?? null,
            'honoree_name' => $validated['honoree_name'] ?? null,
            'honoree_age' => $validated['honoree_age'] ?? null,
            'guest_count' => $validated['guest_count'],
            'location_type' => $validated['location_type'],
            'customer_address' => $validated['customer_address'] ?? null,
            'customer_city' => $validated['customer_city'] ?? null,
            'customer_state' => $validated['customer_state'] ?? null,
            'customer_zip' => $validated['customer_zip'] ?? null,
            'party_painting_id' => $validated['party_painting_id'] ?? null,
            'wants_custom_painting' => $request->boolean('wants_custom_painting'),
            'custom_painting_description' => $validated['custom_painting_description'] ?? null,
            'selected_addon_ids' => $validated['addon_ids'] ?? [],
            'contact_name' => $validated['contact_name'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'] ?? null,
        ]);

        // Send confirmation email to customer
        Mail::to($booking->contact_email)->send(new PartyInquiryReceived($booking));

        // Send notification to admin
        $adminEmail = config('mail.admin_address', config('mail.from.address'));
        if ($adminEmail) {
            Mail::to($adminEmail)->send(new PartyInquiryAdmin($booking));
        }

        return redirect()->route('parties.booking.show', $booking)
            ->with('success', 'Your party inquiry has been submitted! We\'ll send you a quote within 24-48 hours.');
    }

    /**
     * Display the user's party inquiries/bookings.
     */
    public function index()
    {
        $bookings = PartyBooking::where('user_id', auth()->id())
            ->with(['partyPainting', 'pricingConfig'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('parties.my-inquiries', compact('bookings'));
    }

    /**
     * Display a specific booking.
     */
    public function show(PartyBooking $partyBooking)
    {
        // Ensure user owns this booking
        if ($partyBooking->user_id !== auth()->id()) {
            abort(403, 'You do not have access to this booking.');
        }

        $partyBooking->load(['partyPainting', 'pricingConfig', 'payments']);
        $selectedAddons = $partyBooking->getSelectedAddons();

        return view('parties.booking-show', compact('partyBooking', 'selectedAddons'));
    }
}
