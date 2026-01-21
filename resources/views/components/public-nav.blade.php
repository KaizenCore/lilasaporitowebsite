<nav x-data="{ open: false }" class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-sm dark:shadow-gray-900/50 sticky top-0 z-50 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                    FrizzBoss
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden sm:flex items-center space-x-8">
                <a href="/" class="{{ request()->routeIs('home') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">Home</a>
                <a href="{{ route('classes.index') }}" class="{{ request()->routeIs('classes.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">Classes</a>
                <a href="{{ route('store.index') }}" class="{{ request()->routeIs('store.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">Store</a>
                @php $cartCount = array_sum(array_column(session('shopping_cart', []), 'quantity')); @endphp
                <a href="{{ route('cart.index') }}" class="relative {{ request()->routeIs('cart.*') ? 'text-purple-600 dark:text-purple-400' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-pink-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('parties.index') }}" class="{{ request()->routeIs('parties.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">Book a Party</a>
                <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">About</a>
                @auth
                    <a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400' }} font-medium transition">My Bookings</a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-pink-600 dark:text-pink-400 hover:text-pink-700 dark:hover:text-pink-300 font-semibold transition">Admin</a>
                    @endif
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-medium transition">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg dark:shadow-gray-900/50 py-2 z-50">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 dark:hover:text-purple-400">Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 dark:hover:text-purple-400">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-gray-700 hover:text-purple-600 dark:hover:text-purple-400">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 font-medium transition">Login</a>
                    <a href="{{ route('register') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Sign Up</a>
                @endauth

                <x-theme-toggle />
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center space-x-2 sm:hidden">
                <x-theme-toggle />
                <button @click="open = !open" class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-t border-gray-100 dark:border-gray-800">
        <div class="px-4 py-3 space-y-2">
            <a href="/" class="block py-2 {{ request()->routeIs('home') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">Home</a>
            <a href="{{ route('classes.index') }}" class="block py-2 {{ request()->routeIs('classes.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">Classes</a>
            <a href="{{ route('store.index') }}" class="block py-2 {{ request()->routeIs('store.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">Store</a>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-2 py-2 {{ request()->routeIs('cart.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">
                Cart
                @if($cartCount > 0)
                    <span class="bg-pink-600 text-white text-xs font-bold rounded-full px-2 py-0.5">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('parties.index') }}" class="block py-2 {{ request()->routeIs('parties.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">Book a Party</a>
            <a href="{{ route('about') }}" class="block py-2 {{ request()->routeIs('about') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">About</a>
            @auth
                <a href="{{ route('bookings.index') }}" class="block py-2 {{ request()->routeIs('bookings.*') ? 'text-purple-600 dark:text-purple-400 font-semibold' : 'text-gray-700 dark:text-gray-300' }}">My Bookings</a>
                <a href="{{ route('dashboard') }}" class="block py-2 text-gray-700 dark:text-gray-300">Dashboard</a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 text-pink-600 dark:text-pink-400 font-semibold">Admin</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="block py-2 text-gray-700 dark:text-gray-300">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left py-2 text-gray-700 dark:text-gray-300">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block py-2 text-gray-700 dark:text-gray-300">Login</a>
                <a href="{{ route('register') }}" class="block py-2 text-purple-600 dark:text-purple-400 font-semibold">Sign Up</a>
            @endauth
        </div>
    </div>
</nav>
