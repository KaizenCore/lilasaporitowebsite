<x-mail::message>
# New Party Inquiry Received!

A new party inquiry has been submitted and needs your attention.

**Booking Reference:** {{ $booking->booking_number }}

## Contact Information

- **Name:** {{ $booking->contact_name }}
- **Email:** {{ $booking->contact_email }}
@if($booking->contact_phone)
- **Phone:** {{ $booking->contact_phone }}
@endif

## Event Details

- **Event Type:** {{ $booking->event_type_display }}
- **Preferred Date:** {{ $booking->preferred_date->format('l, F j, Y') }}
@if($booking->preferred_time)
- **Preferred Time:** {{ $booking->preferred_time }}
@endif
@if($booking->alternate_date)
- **Alternate Date:** {{ $booking->alternate_date->format('l, F j, Y') }}
@if($booking->alternate_time)
- **Alternate Time:** {{ $booking->alternate_time }}
@endif
@endif
- **Guest Count:** {{ $booking->guest_count }} people
- **Location:** {{ $booking->location_type_display }}

@if($booking->location_type === 'customer_location')
### Customer Address
{{ $booking->customer_address }}<br>
{{ $booking->customer_city }}, {{ $booking->customer_state }} {{ $booking->customer_zip }}
@endif

@if($booking->partyPainting)
## Selected Painting
{{ $booking->partyPainting->title }} ({{ ucfirst($booking->partyPainting->difficulty_level) }})
@elseif($booking->wants_custom_painting)
## Custom Painting Request
{{ $booking->custom_painting_description ?: 'Custom design to be discussed' }}
@endif

@if($booking->honoree_name)
## Honoree Information
- **Name:** {{ $booking->honoree_name }}
@if($booking->honoree_age)
- **Age:** {{ $booking->honoree_age }}
@endif
@endif

@if($booking->event_details)
## Additional Details
{{ $booking->event_details }}
@endif

<x-mail::button :url="route('admin.parties.bookings.show', $booking)">
View & Send Quote
</x-mail::button>

---

*Submitted: {{ $booking->created_at->format('M j, Y g:i A') }}*
</x-mail::message>
