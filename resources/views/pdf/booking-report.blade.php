@extends('pdf.layout')

@section('content')
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value">{{ $totalBookings }}</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-box">
            <div class="stat-value text-green">{{ $confirmedCount }}</div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-box">
            <div class="stat-value text-purple">{{ $attendedCount }}</div>
            <div class="stat-label">Attended</div>
        </div>
        <div class="stat-box">
            <div class="stat-value text-red">{{ $cancelledCount }}</div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    @if($filters)
        <div style="margin: 15px 0; padding: 10px; background: #f3f4f6; border-radius: 4px; font-size: 11px;">
            <strong>Filters Applied:</strong>
            @if(!empty($filters['date_from'])) From: {{ $filters['date_from'] }} @endif
            @if(!empty($filters['date_to'])) To: {{ $filters['date_to'] }} @endif
            @if(!empty($filters['class'])) | Class: {{ $filters['class'] }} @endif
            @if(!empty($filters['payment_status'])) | Payment: {{ ucfirst($filters['payment_status']) }} @endif
            @if(!empty($filters['attendance_status'])) | Attendance: {{ ucfirst($filters['attendance_status']) }} @endif
        </div>
    @endif

    <h3 class="section-title">Bookings List</h3>

    <table>
        <thead>
            <tr>
                <th>Ticket Code</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Class</th>
                <th>Class Date</th>
                <th class="text-center">Payment</th>
                <th class="text-center">Attendance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td class="font-bold text-purple">{{ $booking->ticket_code }}</td>
                    <td>{{ Str::limit($booking->user?->name ?? 'Unknown', 20) }}</td>
                    <td style="font-size: 10px;">{{ Str::limit($booking->user?->email ?? 'N/A', 25) }}</td>
                    <td>{{ Str::limit($booking->artClass?->title ?? 'Deleted', 20) }}</td>
                    <td>{{ $booking->artClass?->class_date?->format('M j, Y') ?? 'N/A' }}</td>
                    <td class="text-center">
                        @if($booking->payment_status === 'completed')
                            <span class="badge badge-green">Paid</span>
                        @elseif($booking->payment_status === 'pending')
                            <span class="badge badge-yellow">Pending</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($booking->payment_status ?? 'N/A') }}</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($booking->attendance_status === 'attended')
                            <span class="badge badge-green">Attended</span>
                        @elseif($booking->attendance_status === 'cancelled')
                            <span class="badge badge-red">Cancelled</span>
                        @else
                            <span class="badge badge-gray">{{ ucfirst($booking->attendance_status ?? 'Booked') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No bookings found matching the criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 11px; color: #666;">
        <strong>Summary:</strong> {{ $totalBookings }} bookings total |
        {{ $confirmedCount }} confirmed |
        {{ $attendedCount }} attended |
        {{ $cancelledCount }} cancelled
    </div>
@endsection
