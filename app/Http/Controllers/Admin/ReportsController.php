<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
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
}
