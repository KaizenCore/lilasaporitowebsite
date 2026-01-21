<x-mail::message>
# Your Party is Confirmed!

Hi {{ $booking->contact_name }},

Woohoo! Your payment has been received and your party is officially confirmed. We can't wait to celebrate with you!

**Booking Reference:** {{ $booking->booking_number }}

## Event Details

- **Date:** {{ ($booking->confirmed_date ?? $booking->preferred_date)->format('l, F j, Y') }}
@if($booking->preferred_time)
- **Time:** {{ $booking->preferred_time }}
@endif
- **Guest Count:** {{ $booking->guest_count }} people
- **Event Type:** {{ $booking->event_type_display }}

@if($booking->location_type === 'lila_hosts')
### Location
Lila's Studio - Address details will be provided closer to your event date.
@else
### Location
Your venue at:
{{ $booking->customer_address }}<br>
{{ $booking->customer_city }}, {{ $booking->customer_state }} {{ $booking->customer_zip }}
@endif

@if($booking->partyPainting)
## Your Painting
{{ $booking->partyPainting->title }}

Everyone will create their own masterpiece version of this painting!
@elseif($booking->wants_custom_painting)
## Custom Painting
We'll be in touch to finalize your custom painting design before the event.
@endif

## Payment Summary

<x-mail::table>
| Description | Amount |
|:------------|-------:|
| Total Paid | {{ $booking->formatted_quoted_total }} |
</x-mail::table>

## What to Expect

- **All supplies included** - We bring everything needed for painting
- **Guided instruction** - Step-by-step guidance for all skill levels
- **Take home your art** - Each guest keeps their painting!
@if($booking->location_type === 'lila_hosts')
- **Light refreshments** - Feel free to bring snacks and drinks
@else
- **We come to you** - Lila will arrive to set up before your guests
@endif

## Before Your Party

1. We'll send a reminder email a few days before your event
2. Make sure your guests know the date and time
3. Prepare your space for painting (we'll protect surfaces)
4. Get ready to have fun!

<x-mail::button :url="route('parties.booking.show', $booking)">
View Booking Details
</x-mail::button>

If you have any questions or need to make changes, please reply to this email or contact us directly.

See you soon!

Thanks,<br>
{{ config('app.name') }}


---

*Need to cancel? Contact us at least 48 hours before your event for a full refund.*
</x-mail::message>
