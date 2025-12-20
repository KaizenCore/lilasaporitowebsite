<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About Lila - FrizzBoss</title>
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
                    <a href="{{ route('about') }}" class="text-purple-600 font-medium">About</a>
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
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Meet Lila</h1>
            <p class="text-xl text-purple-100">Artist, Teacher, Creative Spirit</p>
        </div>
    </section>

    <!-- About Content -->
    <section class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center mb-16">
                <!-- Image -->
                <div class="order-2 lg:order-1">
                    <div class="rounded-2xl overflow-hidden shadow-2xl bg-gradient-to-br from-purple-200 to-pink-200 aspect-square flex items-center justify-center">
                        <svg class="w-48 h-48 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <!-- Bio -->
                <div class="order-1 lg:order-2">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">Hello, I'm Lila!</h2>
                    <div class="prose prose-lg text-gray-600 space-y-4">
                        <p>
                            I'm an artist and creative educator passionate about helping others discover their artistic voice.
                            For over a decade, I've been teaching art classes that blend technique with creative freedom,
                            creating a safe space where everyone can explore their creativity.
                        </p>
                        <p>
                            My teaching philosophy is simple: there's no "wrong" way to create art. Whether you're picking up
                            a paintbrush for the first time or you're an experienced artist looking to try something new,
                            my classes are designed to be accessible, fun, and inspiring.
                        </p>
                        <p>
                            I specialize in mixed media, acrylic painting, and creative workshops that encourage experimentation
                            and self-expression. Every class is an opportunity to learn new techniques, connect with fellow
                            creatives, and most importantly, have fun!
                        </p>
                    </div>
                </div>
            </div>

            <!-- What Makes Classes Special -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Why Take Classes with Me?</h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div class="flex items-start">
                        <div class="bg-purple-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Personalized Attention</h3>
                            <p class="text-gray-600">Small class sizes mean I can work with each student individually, adapting to your skill level and creative goals.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-pink-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">All Materials Provided</h3>
                            <p class="text-gray-600">I provide all the art supplies you need - canvases, paints, brushes, and more. Just bring yourself!</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-orange-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Beginner-Friendly</h3>
                            <p class="text-gray-600">No experience needed! My classes are designed to be accessible and enjoyable for artists of all levels.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="bg-purple-100 rounded-full p-3 mr-4 flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Creative Community</h3>
                            <p class="text-gray-600">Join a supportive community of fellow artists where we celebrate creativity and encourage each other.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teaching Philosophy -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-xl p-8 md:p-12 text-white mb-16">
                <h2 class="text-3xl font-bold mb-6 text-center">My Teaching Philosophy</h2>
                <div class="max-w-3xl mx-auto text-lg text-purple-100 space-y-4">
                    <p>
                        Art should be joyful, not stressful. In my classes, I create an environment where mistakes are
                        celebrated as part of the creative process. There's no pressure to create a "perfect" piece -
                        instead, we focus on exploration, self-expression, and having fun.
                    </p>
                    <p>
                        I believe everyone is creative, and my goal is to help you tap into that creativity in your own
                        unique way. Whether you leave with a finished masterpiece or just a newfound love for the process,
                        that's a win in my book!
                    </p>
                </div>
            </div>

            <!-- Call to Action -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Ready to Create Together?</h2>
                <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                    Browse my upcoming classes and find the perfect creative experience for you!
                </p>
                <a href="{{ route('classes.index') }}" class="inline-block bg-purple-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-700 transition shadow-xl">
                    View Upcoming Classes
                </a>
            </div>
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
                        <li><a href="/" class="hover:text-purple-400 transition">Home</a></li>
                        <li><a href="{{ route('classes.index') }}" class="hover:text-purple-400 transition">Classes</a></li>
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
