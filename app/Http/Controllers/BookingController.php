<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

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

        return view('bookings.index', compact('upcomingBookings', 'pastBookings'));
    }
}
