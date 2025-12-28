<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Admin Navigation -->
            <nav class="bg-purple-700 border-b border-purple-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
                                    FrizzBoss Admin
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-white text-white' : 'border-transparent text-purple-100 hover:text-white hover:border-purple-300' }} text-sm font-medium">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.classes.*') ? 'border-white text-white' : 'border-transparent text-purple-100 hover:text-white hover:border-purple-300' }} text-sm font-medium">
                                    Classes
                                </a>
                                <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.bookings.*') ? 'border-white text-white' : 'border-transparent text-purple-100 hover:text-white hover:border-purple-300' }} text-sm font-medium">
                                    Bookings
                                </a>
                                <a href="{{ route('admin.calendar.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.calendar.*') ? 'border-white text-white' : 'border-transparent text-purple-100 hover:text-white hover:border-purple-300' }} text-sm font-medium">
                                    Calendar
                                </a>
                            </div>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="ml-3 relative">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-700 hover:bg-purple-600 focus:outline-none transition ease-in-out duration-150" onclick="document.getElementById('user-dropdown').classList.toggle('hidden')">
                                    <div>{{ Auth::user()->name }}</div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>

                                <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">User Dashboard</a>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Log Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button onclick="document.getElementById('responsive-nav').classList.toggle('hidden')" class="inline-flex items-center justify-center p-2 rounded-md text-purple-100 hover:text-white hover:bg-purple-600 focus:outline-none focus:bg-purple-600 focus:text-white transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div id="responsive-nav" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-white bg-purple-800 text-white' : 'border-transparent text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300' }} text-base font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.classes.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.classes.*') ? 'border-white bg-purple-800 text-white' : 'border-transparent text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300' }} text-base font-medium">
                            Classes
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.bookings.*') ? 'border-white bg-purple-800 text-white' : 'border-transparent text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300' }} text-base font-medium">
                            Bookings
                        </a>
                        <a href="{{ route('admin.calendar.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.calendar.*') ? 'border-white bg-purple-800 text-white' : 'border-transparent text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300' }} text-base font-medium">
                            Calendar
                        </a>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-purple-800">
                        <div class="px-4">
                            <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                            <div class="font-medium text-sm text-purple-200">{{ Auth::user()->email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300">User Dashboard</a>
                            <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-purple-100 hover:text-white hover:bg-purple-600 hover:border-purple-300">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('warning'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="py-6">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
