<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FrizzBoss - Art Classes with Lila</title>
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

    <!-- Hero Section -->
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                Unleash Your Creative Spirit with
                <span class="bg-gradient-to-r from-purple-600 via-pink-600 to-orange-500 bg-clip-text text-transparent">
                    Lila
                </span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Join intimate art classes where creativity flows freely and every brushstroke tells a story.
                Perfect for beginners and experienced artists alike.
            </p>
            <div class="flex gap-4 justify-center">
                <a href="{{ route('classes.index') }}" class="bg-purple-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-purple-700 transition shadow-lg hover:shadow-xl">
                    Browse Classes
                </a>
                <a href="{{ route('about') }}" class="bg-white text-purple-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-50 transition shadow-lg hover:shadow-xl border-2 border-purple-600">
                    Meet Lila
                </a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-16 px-4 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Small Class Sizes</h3>
                    <p class="text-gray-600">Intimate groups ensure personalized attention and guidance</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">All Materials Included</h3>
                    <p class="text-gray-600">Everything you need provided - just bring your creativity</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">All Skill Levels Welcome</h3>
                    <p class="text-gray-600">From complete beginners to seasoned artists</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Classes -->
    @if($featuredClasses->count() > 0)
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 text-center">Upcoming Classes</h2>
            <p class="text-gray-600 text-center mb-12">Join us for these amazing creative experiences</p>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach($featuredClasses as $class)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    @if($class->image_path)
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 relative overflow-hidden">
                        <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                        <svg class="w-16 h-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $class->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $class->short_description }}</p>

                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $class->class_date->format('M d, Y \a\t g:i A') }}
                        </div>

                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            {{ $class->spots_available }} spots left
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-purple-600">{{ $class->formatted_price }}</span>
                            <a href="{{ route('classes.show', $class->slug) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('classes.index') }}" class="inline-block bg-white text-purple-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-50 transition shadow-lg border-2 border-purple-600">
                    View All Classes
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-20 px-4 bg-gradient-to-r from-purple-600 to-pink-600">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Ready to Start Your Creative Journey?</h2>
            <p class="text-xl text-purple-100 mb-8">
                Join our community of artists and discover your creative potential
            </p>
            <a href="{{ route('classes.index') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition shadow-xl inline-block">
                Book Your First Class
            </a>
        </div>
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
