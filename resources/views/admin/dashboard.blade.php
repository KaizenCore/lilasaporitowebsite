<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Classes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Classes</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_classes'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">{{ $stats['published_classes'] }} published</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Classes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Upcoming Classes</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['upcoming_classes'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.classes.create') }}" class="text-sm text-purple-600 hover:text-purple-900">Create new class</a>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">{{ $stats['upcoming_bookings'] }} upcoming</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <p class="text-2xl font-semibold text-gray-900">${{ number_format($stats['total_revenue'] / 100, 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-gray-600">Net: ${{ number_format(($stats['net_revenue'] ?? 0) / 100, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upcoming Classes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Classes</h3>
                        <a href="{{ route('admin.classes.index') }}" class="text-sm text-purple-600 hover:text-purple-900">View all</a>
                    </div>

                    @if($upcomingClasses->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingClasses as $class)
                                <div class="border-l-4 border-purple-500 pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $class->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $class->class_date->format('M d, Y g:i A') }}</p>
                                            <p class="text-sm text-gray-500">{{ $class->location }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ $class->bookings->count() }}/{{ $class->capacity }}</p>
                                            <p class="text-xs text-gray-500">booked</p>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('admin.classes.edit', $class) }}" class="text-sm text-purple-600 hover:text-purple-900">Edit</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No upcoming classes</p>
                    @endif
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-purple-600 hover:text-purple-900">View all</a>
                    </div>

                    @if($recentBookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentBookings as $booking)
                                <div class="border-l-4 {{ $booking->attendance_status === 'attended' ? 'border-green-500' : 'border-blue-500' }} pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $booking->user->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $booking->artClass->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $booking->ticket_code }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm">
                                                @if($booking->attendance_status === 'attended')
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Checked In</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Booked</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $booking->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No recent bookings</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Popular Classes -->
        @if($bookingsByClass->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Popular Classes</h3>
                    <div class="space-y-3">
                        @foreach($bookingsByClass as $class)
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $class->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $class->class_date->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ ($class->bookings_count / $class->capacity) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $class->bookings_count }}/{{ $class->capacity }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
