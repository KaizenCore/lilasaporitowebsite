<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $class->title }} - FrizzBoss</title>
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
                    @auth
                        <a href="{{ route('bookings.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">My Bookings</a>
                        <a href="{{ route('dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Login</a>
                        <a href="{{ route('register') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Back Button -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <a href="{{ route('classes.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Classes
        </a>
    </div>

    <!-- Class Details -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Image Section -->
            <div>
                @if($class->image_path)
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-full h-auto object-cover">
                </div>
                @else
                <div class="rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-purple-200 to-pink-200 aspect-square flex items-center justify-center">
                    <svg class="w-32 h-32 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                @endif
            </div>

            <!-- Details Section -->
            <div>
                @if($class->is_full)
                <span class="inline-block bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    SOLD OUT
                </span>
                @elseif($class->spots_available <= 3)
                <span class="inline-block bg-orange-100 text-orange-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Only {{ $class->spots_available }} spots left!
                </span>
                @else
                <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    {{ $class->spots_available }} spots available
                </span>
                @endif

                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">{{ $class->title }}</h1>
                <p class="text-xl text-gray-600 mb-8">{{ $class->short_description }}</p>

                <!-- Key Info -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Date & Time</p>
                                <p class="text-gray-600">{{ $class->class_date->format('l, F j, Y') }}</p>
                                <p class="text-gray-600">{{ $class->class_date->format('g:i A') }} - {{ $class->class_date->addMinutes($class->duration_minutes)->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Location</p>
                                <p class="text-gray-600">{{ $class->location }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Duration</p>
                                <p class="text-gray-600">{{ $class->duration_minutes }} minutes</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Class Size</p>
                                <p class="text-gray-600">Maximum {{ $class->capacity }} students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price and Book Button -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-xl p-8 text-white">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-purple-100 mb-1">Price per person</p>
                            <p class="text-5xl font-bold">{{ $class->formatted_price }}</p>
                        </div>
                    </div>

                    @if($class->is_full)
                    <button disabled class="w-full bg-gray-400 text-white px-8 py-4 rounded-lg text-lg font-semibold cursor-not-allowed">
                        Class is Full
                    </button>
                    @else
                    @auth
                    <a href="{{ route('checkout.show', $class->slug) }}" class="block w-full bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition text-center shadow-lg">
                        Book Your Spot Now
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="block w-full bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition text-center shadow-lg">
                        Login to Book
                    </a>
                    @endauth
                    @endif

                    <p class="text-sm text-purple-100 mt-4 text-center">All materials included • Beginner friendly</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-16">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">About This Class</h2>
                <div class="prose prose-lg max-w-none text-gray-600">
                    {!! nl2br(e($class->description)) !!}
                </div>

                @if($class->materials_included)
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">What's Included</h3>
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! nl2br(e($class->materials_included)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Classes -->
        @if($relatedClasses->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Other Classes You Might Like</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedClasses as $relatedClass)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    @if($relatedClass->image_path)
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 relative overflow-hidden">
                        <img src="{{ Storage::url($relatedClass->image_path) }}" alt="{{ $relatedClass->title }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $relatedClass->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $relatedClass->short_description }}</p>

                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $relatedClass->class_date->format('M d, Y') }}
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-purple-600">{{ $relatedClass->formatted_price }}</span>
                            <a href="{{ route('classes.show', $relatedClass->slug) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
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
                        @auth
                        <li><a href="{{ route('bookings.index') }}" class="hover:text-purple-400 transition">My Bookings</a></li>
                        @endauth
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
