<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Browse Classes - FrizzBoss</title>
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
                    <a href="{{ route('classes.index') }}" class="text-purple-600 font-medium">Classes</a>
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

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Browse Our Classes</h1>
            <p class="text-xl text-purple-100">Find the perfect creative experience for you</p>
        </div>
    </section>

    <!-- Filters and Sort -->
    <section class="py-6 px-4 bg-white/50 backdrop-blur-sm sticky top-16 z-40">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center">
                <div class="text-gray-600">
                    Showing <span class="font-semibold">{{ $classes->total() }}</span> classes
                </div>
                <div class="flex items-center gap-4">
                    <label for="sort" class="text-gray-700 font-medium">Sort by:</label>
                    <select id="sort" onchange="window.location.href='{{ route('classes.index') }}?sort=' + this.value" class="border-gray-300 rounded-lg shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="date" {{ $sortBy === 'date' ? 'selected' : '' }}>Date (Earliest First)</option>
                        <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price (Low to High)</option>
                        <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price (High to Low)</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Classes Grid -->
    <section class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            @if($classes->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($classes as $class)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    @if($class->image_path)
                    <div class="h-56 bg-gradient-to-br from-purple-200 to-pink-200 relative overflow-hidden">
                        <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-full h-full object-cover">
                        @if($class->is_full)
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            SOLD OUT
                        </div>
                        @elseif($class->spots_available <= 3)
                        <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Only {{ $class->spots_available }} left!
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="h-56 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center relative">
                        <svg class="w-20 h-20 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        @if($class->is_full)
                        <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            SOLD OUT
                        </div>
                        @elseif($class->spots_available <= 3)
                        <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                            Only {{ $class->spots_available }} left!
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $class->title }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $class->short_description }}</p>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $class->class_date->format('M d, Y') }}
                            </div>

                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $class->class_date->format('g:i A') }} ({{ $class->duration_minutes }} min)
                            </div>

                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $class->location }}
                            </div>

                            <div class="flex items-center text-sm {{ $class->is_full ? 'text-red-500 font-semibold' : 'text-gray-500' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $class->is_full ? 'Sold Out' : $class->spots_available . ' spots available' }}
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <span class="text-2xl font-bold text-purple-600">{{ $class->formatted_price }}</span>
                            <a href="{{ route('classes.show', $class->slug) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $classes->links() }}
            </div>
            @else
            <div class="text-center py-16">
                <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-700 mb-2">No Classes Available</h3>
                <p class="text-gray-500">Check back soon for upcoming classes!</p>
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
