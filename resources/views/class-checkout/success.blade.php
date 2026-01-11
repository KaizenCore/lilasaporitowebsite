<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmed - FrizzBoss</title>
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
                    <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">My Bookings</a>
                </div>
            </div>
        </div>
    </nav>

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
            <p class="text-xl text-gray-600">Thank you for booking. We can't wait to see you!</p>
        </div>

        <!-- Order Number -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 mb-8 text-white">
            <div class="text-center">
                <p class="text-purple-100 mb-2 text-lg">Order Number</p>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl py-6 px-8 mb-4">
                    <p class="text-5xl font-bold tracking-wider">{{ $order->order_number }}</p>
                </div>
                <p class="text-purple-100 text-sm">Save this number for your records</p>
            </div>
        </div>

        <!-- Tickets Section -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                Your Tickets
            </h2>
            <p class="text-gray-600 mb-6">Present these ticket codes at check-in for each class</p>

            <div class="space-y-4">
                @foreach($order->bookings as $booking)
                    <div class="border border-purple-200 rounded-xl overflow-hidden">
                        <div class="bg-purple-50 p-4 flex items-center gap-4">
                            @if($booking->artClass && $booking->artClass->image_path)
                                <img src="{{ asset('storage/' . $booking->artClass->image_path) }}" alt="{{ $booking->artClass->title }}" class="w-20 h-20 object-cover rounded-lg">
                            @else
                                <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900 text-lg">{{ $booking->artClass->title ?? 'Art Class' }}</h3>
                                <p class="text-gray-600">
                                    {{ $booking->artClass ? $booking->artClass->class_date->format('l, F j, Y') : '' }}
                                </p>
                                <p class="text-gray-600">
                                    {{ $booking->artClass ? $booking->artClass->class_date->format('g:i A') : '' }}
                                    @if($booking->artClass && $booking->artClass->duration_minutes)
                                        - {{ $booking->artClass->class_date->addMinutes($booking->artClass->duration_minutes)->format('g:i A') }}
                                    @endif
                                </p>
                                @if($booking->artClass && $booking->artClass->location)
                                    <p class="text-sm text-gray-500">{{ $booking->artClass->location }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="bg-white p-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Ticket Code</p>
                                <p class="text-2xl font-bold text-purple-600 tracking-wider">{{ $booking->ticket_code }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Confirmed
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Summary</h2>

            <div class="space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal ({{ $order->bookings->count() }} {{ Str::plural('class', $order->bookings->count()) }})</span>
                    <span>${{ number_format($order->subtotal_cents / 100, 2) }}</span>
                </div>
                @if($order->discount_cents > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span>-${{ number_format($order->discount_cents / 100, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Payment Date</span>
                    <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-200">
                    <span>Total Paid</span>
                    <span class="text-green-600">${{ number_format($order->total_amount_cents / 100, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">What's Next?</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Confirmation Email</p>
                        <p class="text-sm text-gray-600">A confirmation email with your ticket details has been sent to {{ $order->email }}</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Save the Dates</p>
                        <p class="text-sm text-gray-600">Add your class dates to your calendar so you don't forget!</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Bring Your Ticket Code</p>
                        <p class="text-sm text-gray-600">Have your ticket code ready when you arrive for check-in</p>
                    </div>
                </li>
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Everything is Included</p>
                        <p class="text-sm text-gray-600">All materials are provided - just bring yourself and your creativity!</p>
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
                Book More Classes
            </a>
        </div>

        <!-- Support -->
        <div class="text-center mt-12 text-gray-600">
            <p>Questions about your booking?</p>
            <p class="mt-2">Contact us and we'll be happy to help!</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">FrizzBoss</h3>
                <p class="text-gray-400 mb-8">Inspiring creativity, one class at a time.</p>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FrizzBoss. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
