<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics.
     */
    public function index()
    {
        // Upcoming classes
        $upcomingClasses = ArtClass::published()
            ->upcoming()
            ->with(['bookings' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->orderBy('class_date', 'asc')
            ->take(5)
            ->get();

        // Statistics
        $stats = [
            'total_classes' => ArtClass::count(),
            'upcoming_classes' => ArtClass::upcoming()->count(),
            'published_classes' => ArtClass::published()->count(),
            'total_bookings' => Booking::where('payment_status', 'completed')->count(),
            'upcoming_bookings' => Booking::where('payment_status', 'completed')
                ->upcoming()
                ->count(),
            'total_revenue' => Payment::where('status', 'succeeded')
                ->sum('amount_cents'),
            'net_revenue' => Payment::where('status', 'succeeded')
                ->sum('net_amount_cents'),
            'total_customers' => Booking::where('payment_status', 'completed')
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // Recent bookings
        $recentBookings = Booking::with(['user', 'artClass', 'payment'])
            ->where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Revenue by month (last 6 months)
        $revenueByMonth = Payment::where('status', 'succeeded')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount_cents) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Bookings by class
        $bookingsByClass = ArtClass::withCount(['bookings' => function ($query) {
                $query->where('payment_status', 'completed');
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'upcomingClasses',
            'stats',
            'recentBookings',
            'revenueByMonth',
            'bookingsByClass'
        ));
    }
}
