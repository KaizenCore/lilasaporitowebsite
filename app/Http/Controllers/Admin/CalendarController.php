<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display the calendar view
     */
    public function index(Request $request)
    {
        $view = $request->get('view', 'month'); // 'month' or 'week'
        $date = $request->get('date', now()->format('Y-m-d'));
        $currentDate = Carbon::parse($date);

        // Calculate date range based on view type
        if ($view === 'month') {
            $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
            $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
            $displayDate = $currentDate->format('F Y');
            $previousDate = $currentDate->copy()->subMonth()->format('Y-m-d');
            $nextDate = $currentDate->copy()->addMonth()->format('Y-m-d');
        } else {
            $startDate = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
            $endDate = $currentDate->copy()->endOfWeek(Carbon::SATURDAY);
            $displayDate = $startDate->format('M d') . ' - ' . $endDate->format('M d, Y');
            $previousDate = $currentDate->copy()->subWeek()->format('Y-m-d');
            $nextDate = $currentDate->copy()->addWeek()->format('Y-m-d');
        }

        // Fetch classes with eager loading (prevent N+1 queries)
        $classes = ArtClass::whereBetween('class_date', [$startDate, $endDate])
            ->with([
                'bookings' => fn($q) => $q->where('payment_status', 'completed')
                    ->with(['payment', 'user']),
            ])
            ->withCount([
                'bookings as confirmed_bookings_count' => fn($q) => $q->where('payment_status', 'completed')
            ])
            ->orderBy('class_date', 'asc')
            ->get()
            ->map(function ($class) {
                // Calculate revenue and check if full
                $class->total_revenue = $class->bookings->sum(fn($b) => $b->payment->amount_cents ?? 0);
                $class->is_full = $class->confirmed_bookings_count >= $class->capacity;
                return $class;
            });

        // Group classes by date for month view
        $calendarDays = [];
        if ($view === 'month') {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dayClasses = $classes->filter(function ($class) use ($current) {
                    return $class->class_date->format('Y-m-d') === $current->format('Y-m-d');
                })->values();

                $calendarDays[] = (object) [
                    'date' => $current->copy(),
                    'day' => $current->day,
                    'isCurrentMonth' => $current->month === $currentDate->month,
                    'isToday' => $current->isToday(),
                    'classes' => $dayClasses,
                ];

                $current->addDay();
            }
        }

        // Group classes by day for week view
        $weekDays = [];
        if ($view === 'week') {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                $dayClasses = $classes->filter(function ($class) use ($current) {
                    return $class->class_date->format('Y-m-d') === $current->format('Y-m-d');
                })->values();

                $weekDays[] = (object) [
                    'date' => $current->copy(),
                    'classes' => $dayClasses,
                ];

                $current->addDay();
            }
        }

        return view('admin.calendar.index', compact(
            'view',
            'date',
            'currentDate',
            'displayDate',
            'previousDate',
            'nextDate',
            'classes',
            'calendarDays',
            'weekDays',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get calendar data via AJAX
     */
    public function getData(Request $request)
    {
        $view = $request->get('view', 'month');
        $date = $request->get('date', now()->format('Y-m-d'));
        $currentDate = Carbon::parse($date);

        // Calculate date range
        if ($view === 'month') {
            $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
            $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        } else {
            $startDate = $currentDate->copy()->startOfWeek(Carbon::SUNDAY);
            $endDate = $currentDate->copy()->endOfWeek(Carbon::SATURDAY);
        }

        // Fetch classes
        $classes = ArtClass::whereBetween('class_date', [$startDate, $endDate])
            ->with(['bookings' => fn($q) => $q->where('payment_status', 'completed')->with('payment')])
            ->withCount(['bookings as confirmed_bookings_count' => fn($q) => $q->where('payment_status', 'completed')])
            ->get()
            ->map(function ($class) {
                $class->total_revenue = $class->bookings->sum(fn($b) => $b->payment->amount_cents ?? 0);
                $class->is_full = $class->confirmed_bookings_count >= $class->capacity;
                return $class;
            });

        return response()->json([
            'classes' => $classes,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Get class details for quick view modal
     */
    public function quickView(ArtClass $class)
    {
        $class->load([
            'bookings' => fn($q) => $q->where('payment_status', 'completed')
                ->with(['user', 'payment'])
                ->orderBy('created_at', 'desc'),
        ]);

        $confirmedBookingsCount = $class->bookings->count();
        $totalRevenue = $class->bookings->sum(fn($b) => $b->payment->amount_cents ?? 0);

        return response()->json([
            'id' => $class->id,
            'title' => $class->title,
            'description' => $class->description,
            'class_date' => $class->class_date->toISOString(),
            'location' => $class->location,
            'duration_minutes' => $class->duration_minutes,
            'price_cents' => $class->price_cents,
            'capacity' => $class->capacity,
            'status' => $class->status,
            'bookings_count' => $confirmedBookingsCount,
            'revenue' => $totalRevenue,
            'is_full' => $confirmedBookingsCount >= $class->capacity,
            'bookings' => $class->bookings->map(fn($booking) => [
                'id' => $booking->id,
                'ticket_code' => $booking->ticket_code,
                'attendance_status' => $booking->attendance_status,
                'created_at' => $booking->created_at->toISOString(),
                'user' => [
                    'name' => $booking->user->name,
                    'email' => $booking->user->email,
                ],
            ]),
        ]);
    }
}
