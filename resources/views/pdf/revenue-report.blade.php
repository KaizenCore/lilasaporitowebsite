@extends('pdf.layout')

@section('content')
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value">{{ $transactionCount }}</div>
            <div class="stat-label">Transactions</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">${{ number_format($grossRevenue / 100, 2) }}</div>
            <div class="stat-label">Gross Revenue</div>
        </div>
        <div class="stat-box">
            <div class="stat-value text-red">-${{ number_format($stripeFees / 100, 2) }}</div>
            <div class="stat-label">Stripe Fees</div>
        </div>
        <div class="stat-box">
            <div class="stat-value text-green">${{ number_format($netRevenue / 100, 2) }}</div>
            <div class="stat-label">Net Revenue</div>
        </div>
    </div>

    <h3 class="section-title">Payment Transactions ({{ $period }})</h3>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Customer</th>
                <th>Description</th>
                <th class="text-right">Gross</th>
                <th class="text-right">Fee</th>
                <th class="text-right">Net</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                @php
                    $type = 'Unknown';
                    $customerName = 'N/A';
                    $description = 'N/A';

                    if ($payment->booking_id && $payment->booking) {
                        $type = 'Class';
                        $customerName = $payment->booking->user?->name ?? 'Unknown';
                        $description = $payment->booking->artClass?->title ?? 'Deleted Class';
                    } elseif ($payment->class_booking_order_id && $payment->classBookingOrder) {
                        $type = 'Multi-Class';
                        $customerName = $payment->classBookingOrder->user?->name ?? 'Unknown';
                        $description = '#' . $payment->classBookingOrder->order_number;
                    } elseif ($payment->order_id && $payment->order) {
                        $type = 'Store';
                        $customerName = $payment->order->user?->name ?? ($payment->order->customer_name ?? 'Guest');
                        $description = '#' . $payment->order->order_number;
                    }
                @endphp
                <tr>
                    <td>{{ $payment->created_at->format('M j, Y') }}</td>
                    <td>{{ $type }}</td>
                    <td>{{ Str::limit($customerName, 20) }}</td>
                    <td>{{ Str::limit($description, 25) }}</td>
                    <td class="text-right">${{ number_format($payment->amount_cents / 100, 2) }}</td>
                    <td class="text-right text-red">-${{ number_format(($payment->stripe_fee_cents ?? 0) / 100, 2) }}</td>
                    <td class="text-right text-green">${{ number_format(($payment->net_amount_cents ?? $payment->amount_cents) / 100, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No transactions found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; padding: 15px; background: #f3e8ff; border-radius: 4px;">
        <table style="margin: 0;">
            <tr>
                <td style="border: none; font-weight: bold;">Total Gross Revenue:</td>
                <td style="border: none; text-align: right; font-weight: bold;">${{ number_format($grossRevenue / 100, 2) }}</td>
            </tr>
            <tr>
                <td style="border: none; color: #dc2626;">Total Stripe Fees:</td>
                <td style="border: none; text-align: right; color: #dc2626;">-${{ number_format($stripeFees / 100, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #7c3aed;">
                <td style="border: none; font-weight: bold; font-size: 14px; padding-top: 10px;">Net Revenue:</td>
                <td style="border: none; text-align: right; font-weight: bold; font-size: 14px; color: #16a34a; padding-top: 10px;">${{ number_format($netRevenue / 100, 2) }}</td>
            </tr>
        </table>
    </div>
@endsection
