<x-public-layout>
    <x-slot name="title">Dashboard - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Dashboard</h1>
            <p class="text-purple-100">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </section>

    <!-- Dashboard Content -->
    <section class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- My Bookings Card -->
                <a href="{{ route('bookings.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition">My Bookings</h3>
                    <p class="text-gray-600">View and manage your class bookings</p>
                </a>

                <!-- Browse Classes Card -->
                <a href="{{ route('classes.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                    <div class="flex items-center mb-4">
                        <div class="bg-pink-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-pink-600 transition">Browse Classes</h3>
                    <p class="text-gray-600">Discover new art classes to join</p>
                </a>

                <!-- Shop Card -->
                <a href="{{ route('store.index') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                    <div class="flex items-center mb-4">
                        <div class="bg-orange-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition">Shop</h3>
                    <p class="text-gray-600">Browse art supplies and products</p>
                </a>

                <!-- Profile Card -->
                <a href="{{ route('profile.edit') }}" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition">My Profile</h3>
                    <p class="text-gray-600">Update your account information</p>
                </a>

                @if(Auth::user()->isAdmin())
                <!-- Admin Panel Card -->
                <a href="{{ route('admin.dashboard') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-lg p-6 hover:shadow-xl transition group">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-3 rounded-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Admin Panel</h3>
                    <p class="text-purple-100">Manage classes, bookings & products</p>
                </a>
                @endif
            </div>
        </div>
    </section>
</x-public-layout>
