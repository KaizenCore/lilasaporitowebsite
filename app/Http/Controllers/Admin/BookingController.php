<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ArtClass;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings with search and filter.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'artClass', 'payment']);

        // Search by ticket code, user name, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by attendance status
        if ($request->filled('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('art_class_id', $request->class_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->where('class_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->where('class_date', '<=', $request->date_to);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get all classes for filter dropdown
        $classes = ArtClass::orderBy('class_date', 'desc')->get();

        return view('admin.bookings.index', compact('bookings', 'classes'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'artClass', 'payment', 'emailLogs']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Check in a customer using ticket code.
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $booking = Booking::where('ticket_code', $request->ticket_code)
            ->with(['user', 'artClass'])
            ->first();

        if (!$booking) {
            return back()->with('error', 'Ticket code not found.');
        }

        if ($booking->payment_status !== 'completed') {
            return back()->with('error', 'Booking payment is not completed.');
        }

        if ($booking->attendance_status === 'cancelled') {
            return back()->with('error', 'This booking has been cancelled.');
        }

        if ($booking->attendance_status === 'attended') {
            return back()->with('warning', 'Customer already checked in at ' . $booking->checked_in_at->format('M d, Y g:i A'));
        }

        // Check in the customer
        $booking->checkIn();

        return back()->with('success', 'Customer checked in successfully! Welcome, ' . $booking->user->name);
    }

    /**
     * Show check-in form for a specific class.
     */
    public function checkInForm(ArtClass $class)
    {
        $class->load(['bookings' => function ($query) {
            $query->where('payment_status', 'completed')
                ->with('user')
                ->orderBy('attendance_status', 'asc')
                ->orderBy('user_id', 'asc');
        }]);

        return view('admin.bookings.check-in', compact('class'));
    }

    /**
     * Manually check in a booking.
     */
    public function manualCheckIn(Booking $booking)
    {
        if ($booking->payment_status !== 'completed') {
            return back()->with('error', 'Booking payment is not completed.');
        }

        if ($booking->attendance_status === 'cancelled') {
            return back()->with('error', 'This booking has been cancelled.');
        }

        if ($booking->attendance_status === 'attended') {
            return back()->with('warning', 'Customer already checked in.');
        }

        $booking->checkIn();

        return back()->with('success', 'Customer checked in successfully!');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        if ($booking->attendance_status === 'cancelled') {
            return back()->with('warning', 'Booking is already cancelled.');
        }

        $booking->cancel($request->cancellation_reason);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
