<x-public-layout>
    <x-slot name="title">Booking Confirmed!</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden text-center">
                <!-- Success Header -->
                <div class="bg-green-500 text-white px-6 py-8">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold">Booking Confirmed!</h1>
                    <p class="text-green-100 mt-2">Your party is all set</p>
                </div>

                <div class="p-8">
                    <!-- Booking Number -->
                    <div class="mb-8">
                        <p class="text-sm text-gray-500 mb-1">Booking Reference</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $partyBooking->booking_number }}</p>
                    </div>

                    <!-- Event Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <h2 class="font-semibold text-gray-900 mb-4">Event Details</h2>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Date</p>
                                <p class="font-medium">{{ ($partyBooking->confirmed_date ?? $partyBooking->preferred_date)->format('l, F j, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Guests</p>
                                <p class="font-medium">{{ $partyBooking->guest_count }} people</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Event Type</p>
                                <p class="font-medium">{{ $partyBooking->event_type_display }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Location</p>
                                <p class="font-medium">{{ $partyBooking->location_type_display }}</p>
                            </div>
                        </div>

                        @if($partyBooking->location_type === 'lila_hosts')
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-sm text-gray-600">
                                    <strong>Studio Address:</strong> Details will be sent to your email.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-green-50 rounded-lg p-6 mb-8 text-left">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-green-600">Payment Received</p>
                                <p class="text-2xl font-bold text-green-700">{{ $partyBooking->formatted_quoted_total }}</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- What's Next -->
                    <div class="text-left mb-8">
                        <h2 class="font-semibold text-gray-900 mb-4">What's Next?</h2>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                <span>A confirmation email has been sent to <strong>{{ $partyBooking->contact_email }}</strong></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                <span>Lila will reach out with any final details as your event approaches</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-6 h-6 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                <span>Get ready for an amazing paint party!</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('parties.booking.show', $partyBooking) }}" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition">
                            View Booking Details
                        </a>
                        <a href="{{ route('parties.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition">
                            Back to Parties
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Questions? Contact us at <a href="mailto:hello@fizzboss.com" class="text-purple-600 hover:text-purple-800">hello@fizzboss.com</a>
                </p>
            </div>
        </div>
    </div>
</x-public-layout>
