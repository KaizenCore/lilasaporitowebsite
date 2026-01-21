<x-public-layout>
    <x-slot name="title">My Party Bookings</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">My Party Bookings</h1>
                <a href="{{ route('parties.inquire') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                    New Inquiry
                </a>
            </div>

            @if($bookings->count() > 0)
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <a href="{{ route('parties.booking.show', $booking) }}" class="block bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-semibold text-lg">{{ $booking->booking_number }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $booking->status_badge_color }}-100 text-{{ $booking->status_badge_color }}-800">
                                                {{ $booking->status_display }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 mt-1">{{ $booking->event_type_display }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">{{ ($booking->confirmed_date ?? $booking->preferred_date)->format('M j, Y') }}</p>
                                        <p class="text-sm text-gray-500">{{ $booking->guest_count }} guests</p>
                                    </div>
                                </div>

                                @if($booking->quoted_total_cents)
                                    <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                        <span class="text-gray-500 text-sm">Quote Total</span>
                                        <span class="font-semibold text-lg">{{ $booking->formatted_quoted_total }}</span>
                                    </div>
                                @endif

                                @if($booking->status === 'quoted')
                                    <div class="mt-4 pt-4 border-t">
                                        <span class="inline-block px-4 py-2 bg-purple-600 text-white rounded-lg text-sm">
                                            View Quote & Pay &rarr;
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <div class="text-6xl mb-4">🎨</div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">No Bookings Yet</h2>
                    <p class="text-gray-600 mb-6">Ready to plan your first paint party?</p>
                    <a href="{{ route('parties.inquire') }}" class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Start Your Inquiry
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
