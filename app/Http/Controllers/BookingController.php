<?php

namespace App\Http\Controllers;

use App\Mail\BookingCancellation;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Handle AJAX request to check for booking by payment intent
        if ($request->has('check_payment') && $request->expectsJson()) {
            $paymentIntentId = $request->input('check_payment');

            $booking = Booking::where('user_id', auth()->id())
                ->whereHas('payment', function ($query) use ($paymentIntentId) {
                    $query->where('stripe_payment_intent_id', $paymentIntentId);
                })
                ->first();

            if ($booking) {
                return response()->json(['booking_id' => $booking->id]);
            }

            return response()->json(['booking_id' => null]);
        }

        // Normal view request
        $upcomingBookings = Booking::where('user_id', auth()->id())
            ->confirmed()
            ->upcoming()
            ->where('attendance_status', '!=', 'cancelled')
            ->with('artClass')
            ->get()
            ->sortBy('artClass.class_date');

        $pastBookings = Booking::where('user_id', auth()->id())
            ->confirmed()
            ->past()
            ->with('artClass')
            ->get()
            ->sortByDesc('artClass.class_date')
            ->take(10);

        $cancelledBookings = Booking::where('user_id', auth()->id())
            ->where('attendance_status', 'cancelled')
            ->with('artClass')
            ->orderByDesc('cancelled_at')
            ->take(10)
            ->get();

        return view('bookings.index', compact('upcomingBookings', 'pastBookings', 'cancelledBookings'));
    }

    public function cancel(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if already cancelled
        if ($booking->is_cancelled) {
            return redirect()->route('bookings.index')
                ->with('error', 'This booking has already been cancelled.');
        }

        // Check if class has already passed
        if ($booking->artClass->class_date < now()) {
            return redirect()->route('bookings.index')
                ->with('error', 'Cannot cancel a booking for a class that has already occurred.');
        }

        // Cancel the booking
        $booking->cancel($request->input('reason', 'Cancelled by user'));

        // Send cancellation email
        try {
            Mail::to($booking->user->email)->send(new BookingCancellation($booking));
        } catch (\Throwable $e) {
            // Log error but don't fail the cancellation
            \Log::error('Failed to send cancellation email: ' . $e->getMessage());
        }

        return redirect()->route('bookings.index')
            ->with('success', 'Your booking has been cancelled. Please contact us if you would like to request a refund.');
    }
}
