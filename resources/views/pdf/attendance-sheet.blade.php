@extends('pdf.layout')

@section('content')
    <div style="margin-bottom: 20px; padding: 15px; background: #f3e8ff; border-radius: 4px;">
        <table style="margin: 0; border: none;">
            <tr>
                <td style="border: none; font-weight: bold; width: 120px;">Class:</td>
                <td style="border: none;">{{ $artClass->title }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">Date & Time:</td>
                <td style="border: none;">{{ $artClass->class_date->format('l, F j, Y \a\t g:i A') }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">Duration:</td>
                <td style="border: none;">{{ $artClass->duration_minutes }} minutes</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">Location:</td>
                <td style="border: none;">{{ $artClass->location }}</td>
            </tr>
            <tr>
                <td style="border: none; font-weight: bold;">Capacity:</td>
                <td style="border: none;">{{ $bookings->count() }} / {{ $artClass->capacity }} registered</td>
            </tr>
        </table>
    </div>

    <h3 class="section-title">Attendance List</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Check</th>
                <th>Name</th>
                <th>Ticket Code</th>
                <th>Email</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $index => $booking)
                <tr>
                    <td class="text-center">
                        @if($booking->attendance_status === 'attended')
                            <span style="color: #16a34a; font-size: 16px;">&#10003;</span>
                        @else
                            <span class="check-box"></span>
                        @endif
                    </td>
                    <td class="font-bold">{{ $booking->user?->name ?? 'Unknown' }}</td>
                    <td class="text-purple font-bold">{{ $booking->ticket_code }}</td>
                    <td style="font-size: 10px;">{{ $booking->user?->email ?? 'N/A' }}</td>
                    <td class="text-center">
                        @if($booking->attendance_status === 'attended')
                            <span class="badge badge-green">Checked In</span>
                        @elseif($booking->attendance_status === 'cancelled')
                            <span class="badge badge-red">Cancelled</span>
                        @else
                            <span class="badge badge-gray">Registered</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No attendees registered for this class.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px;">
        <h3 class="section-title">Notes</h3>
        <div style="border: 1px solid #e5e7eb; border-radius: 4px; padding: 15px; min-height: 100px; background: #fafafa;">
            <!-- Space for handwritten notes -->
        </div>
    </div>

    <div style="margin-top: 20px; font-size: 11px; color: #666; text-align: center;">
        Total Registered: {{ $bookings->count() }} |
        Checked In: {{ $bookings->where('attendance_status', 'attended')->count() }} |
        Remaining: {{ $bookings->where('attendance_status', '!=', 'attended')->where('attendance_status', '!=', 'cancelled')->count() }}
    </div>
@endsection
