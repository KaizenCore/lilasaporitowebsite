<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyAddon;
use App\Models\PartyAvailabilitySlot;
use App\Models\PartyBooking;
use App\Models\PartyPricingConfig;
use App\Mail\PartyQuoteSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PartyBookingController extends Controller
{
    /**
     * Display a listing of party bookings/inquiries.
     */
    public function index(Request $request)
    {
        $query = PartyBooking::with(['user', 'partyPainting', 'pricingConfig']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('preferred_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('preferred_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('contact_name', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get counts for quick filters
        $counts = [
            'inquiries' => PartyBooking::where('status', 'inquiry')->count(),
            'quoted' => PartyBooking::where('status', 'quoted')->count(),
            'confirmed' => PartyBooking::whereIn('status', ['confirmed', 'deposit_paid'])->count(),
            'upcoming' => PartyBooking::confirmed()->upcoming()->count(),
        ];

        return view('admin.parties.bookings.index', compact('bookings', 'counts'));
    }

    /**
     * Display the specified booking with quote builder.
     */
    public function show(PartyBooking $partyBooking)
    {
        $partyBooking->load(['user', 'partyPainting', 'pricingConfig', 'payments']);

        $pricingConfigs = PartyPricingConfig::active()->orderBy('name')->get();
        $addons = PartyAddon::active()->ordered()->get();
        $availableSlots = PartyAvailabilitySlot::available()
            ->where('date', '>=', now()->toDateString())
            ->ordered()
            ->limit(30)
            ->get();

        return view('admin.parties.bookings.show', compact(
            'partyBooking',
            'pricingConfigs',
            'addons',
            'availableSlots'
        ));
    }

    /**
     * Send a quote to the customer.
     */
    public function sendQuote(Request $request, PartyBooking $partyBooking)
    {
        $validated = $request->validate([
            'party_pricing_config_id' => 'required|exists:party_pricing_configs,id',
            'guest_count' => 'required|integer|min:1',
            'selected_addon_ids' => 'nullable|array',
            'selected_addon_ids.*' => 'exists:party_addons,id',
            'quoted_subtotal_cents' => 'required|integer|min:0',
            'quoted_addons_cents' => 'required|integer|min:0',
            'quoted_venue_fee_cents' => 'required|integer|min:0',
            'quoted_custom_painting_fee_cents' => 'required|integer|min:0',
            'quoted_adjustment_cents' => 'nullable|integer',
            'quoted_total_cents' => 'required|integer|min:0',
            'deposit_required_cents' => 'nullable|integer|min:0',
            'quote_notes' => 'nullable|string',
            'quote_expires_days' => 'nullable|integer|min:1|max:30',
            'confirmed_date' => 'nullable|date',
            'confirmed_time' => 'nullable|date_format:H:i',
        ]);

        $partyBooking->update([
            'party_pricing_config_id' => $validated['party_pricing_config_id'],
            'guest_count' => $validated['guest_count'],
            'selected_addon_ids' => $validated['selected_addon_ids'] ?? [],
            'quoted_subtotal_cents' => $validated['quoted_subtotal_cents'],
            'quoted_addons_cents' => $validated['quoted_addons_cents'],
            'quoted_venue_fee_cents' => $validated['quoted_venue_fee_cents'],
            'quoted_custom_painting_fee_cents' => $validated['quoted_custom_painting_fee_cents'],
            'quoted_adjustment_cents' => $validated['quoted_adjustment_cents'] ?? 0,
            'quoted_total_cents' => $validated['quoted_total_cents'],
            'deposit_required_cents' => $validated['deposit_required_cents'] ?? 0,
            'quote_notes' => $validated['quote_notes'],
            'quote_sent_at' => now(),
            'quote_expires_at' => now()->addDays($validated['quote_expires_days'] ?? 7),
            'confirmed_date' => $validated['confirmed_date'],
            'confirmed_time' => $validated['confirmed_time'],
            'status' => PartyBooking::STATUS_QUOTED,
        ]);

        // Send email to customer
        Mail::to($partyBooking->contact_email)->send(new PartyQuoteSent($partyBooking));

        return redirect()->route('admin.parties.bookings.show', $partyBooking)
            ->with('success', 'Quote sent to customer successfully.');
    }

    /**
     * Manually confirm a booking (bypass payment).
     */
    public function confirm(Request $request, PartyBooking $partyBooking)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string',
            'confirmed_date' => 'required|date',
            'confirmed_time' => 'required|date_format:H:i',
        ]);

        $partyBooking->update([
            'status' => PartyBooking::STATUS_CONFIRMED,
            'payment_status' => PartyBooking::PAYMENT_PAID,
            'total_paid_cents' => $partyBooking->quoted_total_cents,
            'confirmed_date' => $validated['confirmed_date'],
            'confirmed_time' => $validated['confirmed_time'],
            'admin_notes' => $validated['admin_notes'],
        ]);

        // Try to book an availability slot
        $slot = PartyAvailabilitySlot::where('date', $validated['confirmed_date'])
            ->where('status', PartyAvailabilitySlot::STATUS_AVAILABLE)
            ->first();

        if ($slot) {
            $slot->markAsBooked($partyBooking);
        }

        return redirect()->route('admin.parties.bookings.show', $partyBooking)
            ->with('success', 'Booking confirmed manually.');
    }

    /**
     * Decline a booking inquiry.
     */
    public function decline(Request $request, PartyBooking $partyBooking)
    {
        $validated = $request->validate([
            'decline_reason' => 'nullable|string',
        ]);

        $partyBooking->update([
            'status' => PartyBooking::STATUS_DECLINED,
            'admin_notes' => $validated['decline_reason'] ?? $partyBooking->admin_notes,
        ]);

        return redirect()->route('admin.parties.bookings.index')
            ->with('success', 'Booking declined.');
    }

    /**
     * Mark booking as completed.
     */
    public function complete(PartyBooking $partyBooking)
    {
        $partyBooking->update([
            'status' => PartyBooking::STATUS_COMPLETED,
        ]);

        return redirect()->route('admin.parties.bookings.show', $partyBooking)
            ->with('success', 'Booking marked as completed.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, PartyBooking $partyBooking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string',
        ]);

        $partyBooking->update([
            'status' => PartyBooking::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        // Release the availability slot if booked
        if ($partyBooking->availabilitySlot) {
            $partyBooking->availabilitySlot->markAsAvailable();
        }

        return redirect()->route('admin.parties.bookings.index')
            ->with('success', 'Booking cancelled.');
    }

    /**
     * Add admin notes.
     */
    public function addNotes(Request $request, PartyBooking $partyBooking)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $partyBooking->update([
            'admin_notes' => $validated['admin_notes'],
        ]);

        return back()->with('success', 'Notes saved.');
    }
}
