<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use App\Models\Booking;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    /**
     * Show the reports page.
     */
    public function index()
    {
        // Get summary stats
        $currentYear = now()->year;

        $stats = [
            'total_revenue' => Payment::succeeded()->live()->sum('amount_cents'),
            'total_fees' => Payment::succeeded()->live()->sum('stripe_fee_cents'),
            'total_net' => Payment::succeeded()->live()->sum('net_amount_cents'),
            'year_revenue' => Payment::succeeded()->live()
                ->whereYear('created_at', $currentYear)
                ->sum('amount_cents'),
            'year_fees' => Payment::succeeded()->live()
                ->whereYear('created_at', $currentYear)
                ->sum('stripe_fee_cents'),
            'year_net' => Payment::succeeded()->live()
                ->whereYear('created_at', $currentYear)
                ->sum('net_amount_cents'),
            'transaction_count' => Payment::succeeded()->live()->count(),
            'year_transaction_count' => Payment::succeeded()->live()
                ->whereYear('created_at', $currentYear)
                ->count(),
        ];

        // Get available years for export (from live payments only)
        $years = Payment::live()
            ->selectRaw('EXTRACT(YEAR FROM created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($y) => (int) $y);

        return view('admin.reports.index', compact('stats', 'years', 'currentYear'));
    }

    /**
     * Export payments to CSV.
     */
    public function exportPayments(Request $request): StreamedResponse
    {
        $year = $request->input('year');

        $query = Payment::with(['booking.user', 'booking.artClass', 'order.user', 'classBookingOrder.user'])
            ->succeeded()
            ->live()
            ->orderBy('created_at', 'asc');

        if ($year && $year !== 'all') {
            $query->whereYear('created_at', $year);
        }

        $payments = $query->get();

        $filename = $year && $year !== 'all'
            ? "frizzboss-payments-{$year}.csv"
            : "frizzboss-payments-all.csv";

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, [
                'Date',
                'Type',
                'Customer Name',
                'Customer Email',
                'Description',
                'Gross Amount',
                'Stripe Fee',
                'Net Amount',
                'Payment ID',
                'Stripe ID',
            ]);

            foreach ($payments as $payment) {
                // Determine type and description
                $type = 'Unknown';
                $customerName = 'N/A';
                $customerEmail = 'N/A';
                $description = 'N/A';

                if ($payment->booking_id && $payment->booking) {
                    $type = 'Class Booking';
                    $customerName = $payment->booking->user?->name ?? 'Unknown';
                    $customerEmail = $payment->booking->user?->email ?? 'N/A';
                    $description = $payment->booking->artClass?->title ?? 'Deleted Class';
                } elseif ($payment->class_booking_order_id && $payment->classBookingOrder) {
                    $type = 'Multi-Class Booking';
                    $customerName = $payment->classBookingOrder->user?->name ?? 'Unknown';
                    $customerEmail = $payment->classBookingOrder->email ?? 'N/A';
                    $description = 'Order #' . $payment->classBookingOrder->order_number;
                } elseif ($payment->order_id && $payment->order) {
                    $type = 'Store Order';
                    $customerName = $payment->order->user?->name ?? ($payment->order->customer_name ?? 'Guest');
                    $customerEmail = $payment->order->user?->email ?? ($payment->order->customer_email ?? 'N/A');
                    $description = 'Order #' . $payment->order->order_number;
                }

                fputcsv($handle, [
                    $payment->created_at->format('Y-m-d'),
                    $type,
                    $customerName,
                    $customerEmail,
                    $description,
                    number_format($payment->amount_cents / 100, 2),
                    number_format(($payment->stripe_fee_cents ?? 0) / 100, 2),
                    number_format(($payment->net_amount_cents ?? $payment->amount_cents) / 100, 2),
                    $payment->id,
                    $payment->stripe_payment_intent_id ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export revenue report as PDF.
     */
    public function revenuePdf(Request $request)
    {
        $year = $request->input('year');

        $query = Payment::with(['booking.user', 'booking.artClass', 'order.user', 'classBookingOrder.user'])
            ->succeeded()
            ->live()
            ->orderBy('created_at', 'asc');

        if ($year && $year !== 'all') {
            $query->whereYear('created_at', $year);
        }

        $payments = $query->get();

        $grossRevenue = $payments->sum('amount_cents');
        $stripeFees = $payments->sum('stripe_fee_cents') ?? 0;
        $netRevenue = $payments->sum('net_amount_cents') ?? $grossRevenue;

        $period = $year && $year !== 'all' ? $year : 'All Time';

        $pdf = Pdf::loadView('pdf.revenue-report', [
            'title' => "Revenue Report - {$period}",
            'payments' => $payments,
            'grossRevenue' => $grossRevenue,
            'stripeFees' => $stripeFees,
            'netRevenue' => $netRevenue,
            'transactionCount' => $payments->count(),
            'period' => $period,
        ]);

        $filename = $year && $year !== 'all'
            ? "frizzboss-revenue-{$year}.pdf"
            : "frizzboss-revenue-all.pdf";

        return $pdf->download($filename);
    }

    /**
     * Email revenue report to admin.
     */
    public function emailRevenue(Request $request)
    {
        $year = $request->input('year');

        $query = Payment::with(['booking.user', 'booking.artClass', 'order.user', 'classBookingOrder.user'])
            ->succeeded()
            ->live()
            ->orderBy('created_at', 'asc');

        if ($year && $year !== 'all') {
            $query->whereYear('created_at', $year);
        }

        $payments = $query->get();

        $grossRevenue = $payments->sum('amount_cents');
        $stripeFees = $payments->sum('stripe_fee_cents') ?? 0;
        $netRevenue = $payments->sum('net_amount_cents') ?? $grossRevenue;

        $period = $year && $year !== 'all' ? $year : 'All Time';

        $pdf = Pdf::loadView('pdf.revenue-report', [
            'title' => "Revenue Report - {$period}",
            'payments' => $payments,
            'grossRevenue' => $grossRevenue,
            'stripeFees' => $stripeFees,
            'netRevenue' => $netRevenue,
            'transactionCount' => $payments->count(),
            'period' => $period,
        ]);

        $filename = $year && $year !== 'all'
            ? "frizzboss-revenue-{$year}.pdf"
            : "frizzboss-revenue-all.pdf";

        try {
            // Send to Lila's email
            Mail::raw("Here is your FrizzBoss revenue report for {$period}.\n\nGenerated: " . now()->format('F j, Y \a\t g:i A'), function ($message) use ($pdf, $filename) {
                $message->to('Lesaporito@gmail.com')
                    ->subject('FrizzBoss Revenue Report')
                    ->attachData($pdf->output(), $filename, [
                        'mime' => 'application/pdf',
                    ]);
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send email.'], 500);
        }
    }

    /**
     * Show booking reports page.
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['user', 'artClass'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '<=', $request->date_to);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('art_class_id', $request->class_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        // Clone query for stats before pagination
        $statsQuery = clone $query;

        $stats = [
            'confirmed' => (clone $statsQuery)->where('payment_status', 'completed')->count(),
            'attended' => (clone $statsQuery)->where('attendance_status', 'attended')->count(),
            'cancelled' => (clone $statsQuery)->where('attendance_status', 'cancelled')->count(),
        ];

        $bookings = $query->paginate(25);

        // Get all classes for the filter dropdown
        $classes = ArtClass::orderBy('class_date', 'desc')->get();

        return view('admin.reports.bookings', compact('bookings', 'stats', 'classes'));
    }

    /**
     * Export bookings to CSV.
     */
    public function exportBookings(Request $request): StreamedResponse
    {
        $query = Booking::with(['user', 'artClass'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as bookings page
        if ($request->filled('date_from')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '<=', $request->date_to);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('art_class_id', $request->class_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        $bookings = $query->get();

        return response()->streamDownload(function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Ticket Code',
                'Customer Name',
                'Customer Email',
                'Class',
                'Class Date',
                'Payment Status',
                'Attendance Status',
                'Booked At',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->ticket_code,
                    $booking->user?->name ?? 'Unknown',
                    $booking->user?->email ?? 'N/A',
                    $booking->artClass?->title ?? 'Deleted Class',
                    $booking->artClass?->class_date?->format('Y-m-d H:i') ?? 'N/A',
                    ucfirst($booking->payment_status ?? 'N/A'),
                    ucfirst($booking->attendance_status ?? 'booked'),
                    $booking->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 'frizzboss-bookings-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export bookings to PDF.
     */
    public function exportBookingsPdf(Request $request)
    {
        $query = Booking::with(['user', 'artClass'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as bookings page
        if ($request->filled('date_from')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('artClass', function ($q) use ($request) {
                $q->whereDate('class_date', '<=', $request->date_to);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('art_class_id', $request->class_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        $bookings = $query->get();

        $filters = [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'class' => $request->class_id ? ArtClass::find($request->class_id)?->title : null,
            'payment_status' => $request->payment_status,
            'attendance_status' => $request->attendance_status,
        ];

        $pdf = Pdf::loadView('pdf.booking-report', [
            'title' => 'Booking Report',
            'bookings' => $bookings,
            'totalBookings' => $bookings->count(),
            'confirmedCount' => $bookings->where('payment_status', 'completed')->count(),
            'attendedCount' => $bookings->where('attendance_status', 'attended')->count(),
            'cancelledCount' => $bookings->where('attendance_status', 'cancelled')->count(),
            'filters' => array_filter($filters),
        ]);

        return $pdf->download('frizzboss-bookings-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Show attendance list page.
     */
    public function attendance(Request $request)
    {
        $upcomingClasses = ArtClass::with(['bookings' => function ($q) {
            $q->where('payment_status', 'completed');
        }])
            ->where('class_date', '>', now())
            ->orderBy('class_date', 'asc')
            ->get();

        $pastClasses = ArtClass::where('class_date', '<=', now())
            ->orderBy('class_date', 'desc')
            ->limit(20)
            ->get();

        $selectedClass = null;
        $bookings = collect();

        if ($request->filled('class_id')) {
            $selectedClass = ArtClass::find($request->class_id);
            if ($selectedClass) {
                $bookings = Booking::with('user')
                    ->where('art_class_id', $selectedClass->id)
                    ->where('payment_status', 'completed')
                    ->whereIn('attendance_status', ['booked', 'attended'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('admin.reports.attendance', compact('upcomingClasses', 'pastClasses', 'selectedClass', 'bookings'));
    }

    /**
     * Generate attendance PDF for a class.
     */
    public function attendancePdf(ArtClass $artClass)
    {
        $bookings = Booking::with('user')
            ->where('art_class_id', $artClass->id)
            ->where('payment_status', 'completed')
            ->whereIn('attendance_status', ['booked', 'attended'])
            ->orderBy('created_at', 'asc')
            ->get();

        $pdf = Pdf::loadView('pdf.attendance-sheet', [
            'title' => 'Attendance Sheet - ' . $artClass->title,
            'artClass' => $artClass,
            'bookings' => $bookings,
        ]);

        $filename = 'attendance-' . Str::slug($artClass->title) . '-' . $artClass->class_date->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
