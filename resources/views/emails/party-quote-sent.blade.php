<x-mail::message>
# Your Party Quote is Ready!

Hi {{ $booking->contact_name }},

Great news! We've reviewed your party inquiry and prepared a personalized quote for you.

**Booking Reference:** {{ $booking->booking_number }}

## Event Details

- **Event Type:** {{ $booking->event_type_display }}
- **Date:** {{ ($booking->confirmed_date ?? $booking->preferred_date)->format('l, F j, Y') }}
- **Guest Count:** {{ $booking->guest_count }} people
- **Location:** {{ $booking->location_type_display }}

@if($booking->partyPainting)
- **Painting:** {{ $booking->partyPainting->title }}
@elseif($booking->wants_custom_painting)
- **Painting:** Custom Design
@endif

## Quote Summary

<x-mail::table>
| Item | Amount |
|:-----|-------:|
@if($booking->quoted_subtotal_cents)
| Base Price | ${{ number_format($booking->quoted_subtotal_cents / 100, 2) }} |
@endif
@if($booking->quoted_addons_cents)
| Add-ons | ${{ number_format($booking->quoted_addons_cents / 100, 2) }} |
@endif
@if($booking->quoted_venue_fee_cents)
| Venue Fee | ${{ number_format($booking->quoted_venue_fee_cents / 100, 2) }} |
@endif
@if($booking->quoted_custom_painting_fee_cents)
| Custom Painting | ${{ number_format($booking->quoted_custom_painting_fee_cents / 100, 2) }} |
@endif
| **Total** | **{{ $booking->formatted_quoted_total }}** |
</x-mail::table>

@if($booking->quote_notes)
### Note from Lila
{{ $booking->quote_notes }}
@endif

@if($booking->quote_expires_at)
<x-mail::panel>
**Quote Expires:** {{ $booking->quote_expires_at->format('F j, Y') }}

Please accept and complete payment before this date to secure your booking.
</x-mail::panel>
@endif

## Ready to Book?

Click the button below to review your quote details and complete your payment to confirm your party!

<x-mail::button :url="route('parties.checkout', $booking)">
Accept Quote & Pay
</x-mail::button>

If you have any questions about your quote, email Lila at Lesaporito@gmail.com or DM @frizzboss on Instagram.

We're looking forward to creating an amazing party experience for you!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
