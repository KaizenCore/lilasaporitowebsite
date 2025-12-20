<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Bookings - FrizzBoss</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        FrizzBoss
                    </a>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-purple-600 font-medium transition">Home</a>
                    <a href="{{ route('classes.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Classes</a>
                    <a href="{{ route('about') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">About</a>
                    <a href="{{ route('bookings.index') }}" class="text-purple-600 font-medium">My Bookings</a>
                    <a href="{{ route('dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Dashboard</a>
                </div>
            </div>
        </div>
    </nav>

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
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Upcoming Classes</h2>

                @if($upcomingBookings->count() > 0)
                <div class="space-y-6">
                    @foreach($upcomingBookings as $booking)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300">
                        <div class="md:flex">
                            <!-- Class Image -->
                            <div class="md:w-1/3">
                                @if($booking->artClass->image_path)
                                <div class="h-64 md:h-full bg-gradient-to-br from-purple-200 to-pink-200 relative overflow-hidden">
                                    <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-64 md:h-full bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
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
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->artClass->title }}</h3>
                                        <p class="text-gray-600">{{ $booking->artClass->short_description }}</p>
                                    </div>
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">
                                        Confirmed
                                    </span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-4 mb-6">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Date & Time</p>
                                            <p class="text-sm text-gray-600">{{ $booking->artClass->class_date->format('l, F j, Y') }}</p>
                                            <p class="text-sm text-gray-600">{{ $booking->artClass->class_date->format('g:i A') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">Location</p>
                                            <p class="text-sm text-gray-600">{{ $booking->artClass->location }}</p>
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

                                <div class="mt-6">
                                    <a href="{{ route('classes.show', $booking->artClass->slug) }}" class="text-purple-600 hover:text-purple-700 font-semibold inline-flex items-center">
                                        View Class Details
                                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No Upcoming Classes</h3>
                    <p class="text-gray-500 mb-6">You haven't booked any classes yet</p>
                    <a href="{{ route('classes.index') }}" class="inline-block bg-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Browse Available Classes
                    </a>
                </div>
                @endif
            </div>

            <!-- Past Bookings -->
            @if($pastBookings->count() > 0)
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Past Classes</h2>

                <div class="space-y-6">
                    @foreach($pastBookings as $booking)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden opacity-75 hover:opacity-100 transition-opacity">
                        <div class="md:flex">
                            <!-- Class Image -->
                            <div class="md:w-1/4">
                                @if($booking->artClass->image_path)
                                <div class="h-48 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 relative overflow-hidden">
                                    <img src="{{ Storage::url($booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-full h-full object-cover">
                                </div>
                                @else
                                <div class="h-48 md:h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            <!-- Booking Details -->
                            <div class="md:w-3/4 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $booking->artClass->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $booking->artClass->class_date->format('F j, Y') }}</p>
                                    </div>
                                    @if($booking->is_checked_in)
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        Attended
                                    </span>
                                    @else
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                        Completed
                                    </span>
                                    @endif
                                </div>

                                <div class="flex items-center text-sm text-gray-500">
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
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4 mt-16">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">FrizzBoss</h3>
                    <p class="text-gray-400">Inspiring creativity, one class at a time.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/" class="hover:text-purple-400 transition">Home</a></li>
                        <li><a href="{{ route('classes.index') }}" class="hover:text-purple-400 transition">Classes</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-purple-400 transition">About Lila</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400">Questions? Reach out to us and we'll get back to you soon!</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FrizzBoss. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
