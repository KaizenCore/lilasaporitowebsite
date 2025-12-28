<x-public-layout>
    <x-slot name="title">Booking Confirmed - FrizzBoss</x-slot>

    <!-- Success Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Booking Confirmed!</h1>
            <p class="text-xl text-gray-600">Your spot has been secured. We can't wait to see you!</p>
        </div>

        <!-- Ticket Code -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 mb-8 text-white">
            <div class="text-center">
                <p class="text-purple-100 mb-2 text-lg">Your Ticket Code</p>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl py-6 px-8 mb-4">
                    <p class="text-6xl font-bold tracking-wider">{{ $booking->ticket_code }}</p>
                </div>
                <p class="text-purple-100 text-sm">Show this code when you arrive at the class</p>
            </div>
        </div>

        <!-- Class Details -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Class Details</h2>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ $booking->artClass->title }}</h3>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Date & Time</p>
                                <p class="text-gray-600">{{ $booking->artClass->class_date->format('l, F j, Y') }}</p>
                                <p class="text-gray-600">{{ $booking->artClass->class_date->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Location</p>
                                <p class="text-gray-600">{{ $booking->artClass->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Duration</p>
                                <p class="text-gray-600">{{ $booking->artClass->duration_minutes }} minutes</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking->artClass->image_path)
                <div>
                    <div class="rounded-xl overflow-hidden shadow-lg">
                        <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-64 object-cover">
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Summary</h2>

            <div class="space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Class Price</span>
                    <span>{{ $booking->payment->formatted_amount }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Payment Method</span>
                    <span class="capitalize">{{ $booking->payment->payment_method }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Payment Date</span>
                    <span>{{ $booking->payment->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-200">
                    <span>Total Paid</span>
                    <span class="text-green-600">{{ $booking->payment->formatted_amount }}</span>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">What's Next?</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Save Your Ticket Code</p>
                        <p class="text-sm text-gray-600">Write down or screenshot your ticket code: <span class="font-mono font-bold">{{ $booking->ticket_code }}</span></p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Check Your Email</p>
                        <p class="text-sm text-gray-600">A confirmation email has been sent to {{ $booking->user->email }}</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Arrive on Time</p>
                        <p class="text-sm text-gray-600">Please arrive 10-15 minutes early for check-in</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">All Materials Included</p>
                        <p class="text-sm text-gray-600">Just bring yourself and your creativity!</p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('bookings.index') }}" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition text-center shadow-lg">
                View My Bookings
            </a>
            <a href="{{ route('classes.index') }}" class="flex-1 bg-white border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-50 transition text-center">
                Browse More Classes
            </a>
        </div>

        <!-- Support -->
        <div class="text-center mt-12 text-gray-600">
            <p>Questions about your booking?</p>
            <p class="mt-2">Contact us and we'll be happy to help!</p>
        </div>
    </section>
</x-public-layout>
