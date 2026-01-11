<x-public-layout>
    <x-slot name="title">My Bookings - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">My Bookings</h1>
            <p class="text-xl text-purple-100">Your creative journey starts here</p>
        </div>
    </section>

    <!-- Bookings Content -->
    <section class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Upcoming Bookings -->
            <div class="mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Upcoming Classes</h2>

                @if($upcomingBookings->count() > 0)
                <div class="space-y-6">
                    @foreach($upcomingBookings as $booking)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300">
                        <div class="md:flex">
                            <!-- Class Image -->
                            <div class="md:w-1/3">
                                @if($booking->artClass?->image_path)
                                <div class="h-64 md:h-full bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 relative overflow-hidden">
                                    <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-64 md:h-full bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <!-- Booking Details -->
                            <div class="md:w-2/3 p-8">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $booking->artClass?->title ?? 'Class No Longer Available' }}</h3>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $booking->artClass?->short_description ?? '' }}</p>
                                    </div>
                                    <span class="bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">
                                        Confirmed
                                    </span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 mb-6">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Date & Time</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->artClass?->class_date?->format('l, F j, Y') ?? 'N/A' }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->artClass?->class_date?->format('g:i A') ?? '' }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Location</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->artClass?->location ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ticket Code -->
                                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-lg p-6 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-purple-100 text-sm mb-1">Your Ticket Code</p>
                                            <p class="text-3xl font-bold tracking-wider">{{ $booking->ticket_code }}</p>
                                            <p class="text-purple-100 text-sm mt-2">Present this code when you arrive at class</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg">
                                            <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    @if($booking->artClass)
                                    <a href="{{ route('classes.show', $booking->artClass->slug) }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold inline-flex items-center">
                                        View Class Details
                                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                    @else
                                    <span class="text-gray-400">Class details unavailable</span>
                                    @endif
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? Contact us if you would like to request a refund.')">
                                        @csrf
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 font-semibold inline-flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Cancel Booking
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">No Upcoming Classes</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">You haven't booked any classes yet</p>
                    <a href="{{ route('classes.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Browse Available Classes
                    </a>
                </div>
                @endif
            </div>

            <!-- Past Bookings -->
            @if($pastBookings->count() > 0)
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Past Classes</h2>

                <div class="space-y-6">
                    @foreach($pastBookings as $booking)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden opacity-75 hover:opacity-100 transition-opacity">
                        <div class="md:flex">
                            <!-- Class Image -->
                            <div class="md:w-1/4">
                                @if($booking->artClass?->image_path)
                                <div class="h-48 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 relative overflow-hidden">
                                    <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-48 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <!-- Booking Details -->
                            <div class="md:w-3/4 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $booking->artClass?->title ?? 'Deleted Class' }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->artClass?->class_date?->format('F j, Y') ?? 'N/A' }}</p>
                                    </div>
                                    @if($booking->is_checked_in)
                                    <span class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-3 py-1 rounded-full text-sm font-semibold">
                                        Attended
                                    </span>
                                    @else
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-3 py-1 rounded-full text-sm font-semibold">
                                        Completed
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Ticket: {{ $booking->ticket_code }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Cancelled Bookings -->
            @if($cancelledBookings->count() > 0)
            <div class="mt-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Cancelled Bookings</h2>

                <div class="space-y-4">
                    @foreach($cancelledBookings as $booking)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden opacity-60">
                        <div class="md:flex">
                            <!-- Class Image -->
                            <div class="md:w-1/4">
                                @if($booking->artClass?->image_path)
                                <div class="h-32 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 relative overflow-hidden">
                                    <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-full object-cover grayscale">
                                </div>
                                @else
                                <div class="h-32 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <!-- Booking Details -->
                            <div class="md:w-3/4 p-6">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $booking->artClass?->title ?? 'Deleted Class' }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $booking->artClass?->class_date?->format('F j, Y') ?? 'N/A' }}</p>
                                    </div>
                                    <span class="bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-3 py-1 rounded-full text-sm font-semibold">
                                        Cancelled
                                    </span>
                                </div>

                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Cancelled on {{ $booking->cancelled_at?->format('F j, Y') ?? 'N/A' }}
                                </div>

                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Contact us if you would like to request a refund.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </section>
</x-public-layout>
