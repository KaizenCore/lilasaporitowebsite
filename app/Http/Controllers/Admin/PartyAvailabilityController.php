<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyAvailabilitySlot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PartyAvailabilityController extends Controller
{
    /**
     * Display the availability calendar.
     */
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $slots = PartyAvailabilitySlot::with('partyBooking')
            ->inDateRange($startDate, $endDate)
            ->ordered()
            ->get()
            ->groupBy(function ($slot) {
                return $slot->date->format('Y-m-d');
            });

        return view('admin.parties.availability.index', compact('slots', 'month', 'startDate', 'endDate'));
    }

    /**
     * Store a new availability slot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for overlapping slots
        $existing = PartyAvailabilitySlot::where('date', $validated['date'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                    });
            })
            ->exists();

        if ($existing) {
            return back()->with('error', 'A slot already exists for this time range.');
        }

        PartyAvailabilitySlot::create([
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => PartyAvailabilitySlot::STATUS_AVAILABLE,
        ]);

        return back()->with('success', 'Availability slot added successfully.');
    }

    /**
     * Add multiple availability slots (bulk add).
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $daysOfWeek = $validated['days_of_week'];
        $created = 0;

        while ($startDate->lte($endDate)) {
            if (in_array($startDate->dayOfWeek, $daysOfWeek)) {
                // Check if slot already exists
                $exists = PartyAvailabilitySlot::where('date', $startDate->toDateString())
                    ->where('start_time', $validated['start_time'])
                    ->exists();

                if (!$exists) {
                    PartyAvailabilitySlot::create([
                        'date' => $startDate->toDateString(),
                        'start_time' => $validated['start_time'],
                        'end_time' => $validated['end_time'],
                        'status' => PartyAvailabilitySlot::STATUS_AVAILABLE,
                    ]);
                    $created++;
                }
            }
            $startDate->addDay();
        }

        return back()->with('success', "{$created} availability slots created successfully.");
    }

    /**
     * Delete an availability slot.
     */
    public function destroy(PartyAvailabilitySlot $slot)
    {
        if ($slot->isBooked()) {
            return back()->with('error', 'Cannot delete a booked slot.');
        }

        $slot->delete();

        return back()->with('success', 'Availability slot deleted.');
    }

    /**
     * Block a date range (blackout).
     */
    public function blackout(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $updated = 0;

        // Update existing available slots to blocked
        $updated = PartyAvailabilitySlot::inDateRange($startDate, $endDate)
            ->where('status', PartyAvailabilitySlot::STATUS_AVAILABLE)
            ->update([
                'status' => PartyAvailabilitySlot::STATUS_BLOCKED,
                'block_reason' => $validated['reason'],
            ]);

        return back()->with('success', "{$updated} slots blocked successfully.");
    }

    /**
     * Unblock slots in a date range.
     */
    public function unblock(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $updated = PartyAvailabilitySlot::inDateRange($startDate, $endDate)
            ->where('status', PartyAvailabilitySlot::STATUS_BLOCKED)
            ->update([
                'status' => PartyAvailabilitySlot::STATUS_AVAILABLE,
                'block_reason' => null,
            ]);

        return back()->with('success', "{$updated} slots unblocked successfully.");
    }

    /**
     * Get slots data for calendar (AJAX).
     */
    public function getData(Request $request)
    {
        $startDate = Carbon::parse($request->get('start'));
        $endDate = Carbon::parse($request->get('end'));

        $slots = PartyAvailabilitySlot::with('partyBooking.user')
            ->inDateRange($startDate, $endDate)
            ->get()
            ->map(function ($slot) {
                $color = match ($slot->status) {
                    'available' => '#10B981',
                    'booked' => '#3B82F6',
                    'blocked' => '#EF4444',
                    default => '#6B7280',
                };

                $title = match ($slot->status) {
                    'available' => 'Available',
                    'booked' => 'Booked: ' . ($slot->partyBooking->contact_name ?? 'Unknown'),
                    'blocked' => 'Blocked' . ($slot->block_reason ? ": {$slot->block_reason}" : ''),
                    default => 'Unknown',
                };

                return [
                    'id' => $slot->id,
                    'title' => $title,
                    'start' => $slot->date->format('Y-m-d') . 'T' . $slot->start_time->format('H:i:s'),
                    'end' => $slot->date->format('Y-m-d') . 'T' . $slot->end_time->format('H:i:s'),
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'status' => $slot->status,
                        'booking_id' => $slot->party_booking_id,
                    ],
                ];
            });

        return response()->json($slots);
    }
}
