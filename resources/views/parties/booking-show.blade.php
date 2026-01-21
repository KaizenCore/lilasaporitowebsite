<x-public-layout>
    <x-slot name="title">Party Booking {{ $partyBooking->booking_number }}</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-purple-600 text-white px-6 py-8 text-center">
                    <p class="text-purple-200 text-sm mb-2">Booking Reference</p>
                    <h1 class="text-3xl font-bold">{{ $partyBooking->booking_number }}</h1>
                    <div class="mt-4">
                        <span class="inline-block px-4 py-2 bg-white/20 rounded-full text-sm font-medium">
                            {{ $partyBooking->status_display }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Status Message -->
                    @if($partyBooking->status === 'inquiry')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800">Your inquiry has been received! We'll send you a personalized quote within 24-48 hours.</p>
                        </div>
                    @elseif($partyBooking->status === 'quoted')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <p class="text-yellow-800 mb-2">Your quote is ready! Review the details below and accept to proceed with payment.</p>
                            @if($partyBooking->quote_expires_at)
                                <p class="text-sm text-yellow-600">Quote expires: {{ $partyBooking->quote_expires_at->format('F j, Y') }}</p>
                            @endif
                            <a href="{{ route('parties.checkout', $partyBooking) }}" class="mt-4 inline-block px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                Accept Quote & Pay
                            </a>
                        </div>
                    @elseif($partyBooking->status === 'confirmed')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-800 font-medium">Your party is confirmed! We can't wait to see you!</p>
                        </div>
                    @endif

                    <!-- Event Details -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Details</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Event Type</p>
                                <p class="font-medium">{{ $partyBooking->event_type_display }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Guest Count</p>
                                <p class="font-medium">{{ $partyBooking->guest_count }} people</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Date</p>
                                <p class="font-medium">{{ ($partyBooking->confirmed_date ?? $partyBooking->preferred_date)->format('l, F j, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Location</p>
                                <p class="font-medium">{{ $partyBooking->location_type_display }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Painting -->
                    @if($partyBooking->partyPainting)
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Selected Painting</h2>
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $partyBooking->partyPainting->image_path) }}" alt="{{ $partyBooking->partyPainting->title }}" class="w-20 h-20 object-cover rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $partyBooking->partyPainting->title }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($partyBooking->partyPainting->difficulty_level) }}</p>
                                </div>
                            </div>
                        </div>
                    @elseif($partyBooking->wants_custom_painting)
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Custom Painting Request</h2>
                            <p class="text-gray-600">{{ $partyBooking->custom_painting_description ?: 'Custom design to be discussed' }}</p>
                        </div>
                    @endif

                    <!-- Selected Add-ons -->
                    @if($selectedAddons->count() > 0)
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Add-ons</h2>
                            <ul class="space-y-2">
                                @foreach($selectedAddons as $addon)
                                    <li class="flex justify-between text-sm">
                                        <span>{{ $addon->name }}</span>
                                        <span class="text-gray-500">{{ $addon->formatted_price }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Quote Details (if available) -->
                    @if($partyBooking->quoted_total_cents)
                        <div class="border-t pt-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quote Summary</h2>
                            <div class="space-y-2 text-sm">
                                @if($partyBooking->quoted_subtotal_cents)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Subtotal</span>
                                        <span>${{ number_format($partyBooking->quoted_subtotal_cents / 100, 2) }}</span>
                                    </div>
                                @endif
                                @if($partyBooking->quoted_addons_cents)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Add-ons</span>
                                        <span>${{ number_format($partyBooking->quoted_addons_cents / 100, 2) }}</span>
                                    </div>
                                @endif
                                @if($partyBooking->quoted_venue_fee_cents)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Venue Fee</span>
                                        <span>${{ number_format($partyBooking->quoted_venue_fee_cents / 100, 2) }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between font-semibold text-lg border-t pt-2 mt-2">
                                    <span>Total</span>
                                    <span>{{ $partyBooking->formatted_quoted_total }}</span>
                                </div>
                                @if($partyBooking->total_paid_cents > 0)
                                    <div class="flex justify-between text-green-600">
                                        <span>Paid</span>
                                        <span>{{ $partyBooking->formatted_total_paid }}</span>
                                    </div>
                                @endif
                                @if($partyBooking->remaining_balance > 0 && $partyBooking->status !== 'inquiry')
                                    <div class="flex justify-between text-orange-600">
                                        <span>Balance Due</span>
                                        <span>{{ $partyBooking->formatted_remaining_balance }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($partyBooking->quote_notes)
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <p class="text-sm text-gray-600">{{ $partyBooking->quote_notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Contact Info -->
                    <div class="border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                        <div class="text-sm">
                            <p><strong>{{ $partyBooking->contact_name }}</strong></p>
                            <p class="text-gray-500">{{ $partyBooking->contact_email }}</p>
                            @if($partyBooking->contact_phone)
                                <p class="text-gray-500">{{ $partyBooking->contact_phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('parties.my-inquiries') }}" class="text-purple-600 hover:text-purple-800 text-sm">
                            &larr; All My Bookings
                        </a>
                        @if($partyBooking->canAcceptQuote())
                            <a href="{{ route('parties.checkout', $partyBooking) }}" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                                Accept Quote & Pay
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
