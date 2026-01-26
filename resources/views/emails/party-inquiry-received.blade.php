<x-mail::message>
# We Received Your Party Inquiry!

Hi {{ $booking->contact_name }},

Thank you for your interest in hosting a paint party with us! We've received your inquiry and are excited to help make your event special.

**Booking Reference:** {{ $booking->booking_number }}

## Event Details

- **Event Type:** {{ $booking->event_type_display }}
- **Preferred Date:** {{ $booking->preferred_date->format('l, F j, Y') }}
@if($booking->alternate_date)
- **Alternate Date:** {{ $booking->alternate_date->format('l, F j, Y') }}
@endif
- **Guest Count:** {{ $booking->guest_count }} people
- **Location:** {{ $booking->location_type_display }}

@if($booking->partyPainting)
## Selected Painting
{{ $booking->partyPainting->title }} ({{ ucfirst($booking->partyPainting->difficulty_level) }})
@elseif($booking->wants_custom_painting)
## Custom Painting Request
{{ $booking->custom_painting_description ?: 'Custom design to be discussed' }}
@endif

## What Happens Next?

1. We'll review your request and put together a personalized quote
2. You'll receive your quote via email within 24-48 hours
3. Once you approve the quote, you can complete your payment to confirm

<x-mail::button :url="route('parties.booking.show', $booking)">
View Your Inquiry
</x-mail::button>

If you have any questions in the meantime, email Lila at Lesaporito@gmail.com or DM @frizzboss on Instagram.

We can't wait to party with you!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
