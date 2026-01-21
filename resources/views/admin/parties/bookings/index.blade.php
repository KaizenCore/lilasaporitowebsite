<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Party Inquiries & Bookings
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <a href="{{ route('admin.parties.bookings.index', ['status' => 'inquiry']) }}" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-md transition">
                <div class="text-2xl font-bold text-blue-600">{{ $counts['inquiries'] }}</div>
                <div class="text-sm text-gray-500">New Inquiries</div>
            </a>
            <a href="{{ route('admin.parties.bookings.index', ['status' => 'quoted']) }}" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-md transition">
                <div class="text-2xl font-bold text-yellow-600">{{ $counts['quoted'] }}</div>
                <div class="text-sm text-gray-500">Quotes Sent</div>
            </a>
            <a href="{{ route('admin.parties.bookings.index') }}" class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-md transition">
                <div class="text-2xl font-bold text-green-600">{{ $counts['confirmed'] }}</div>
                <div class="text-sm text-gray-500">Confirmed</div>
            </a>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow">
                <div class="text-2xl font-bold text-purple-600">{{ $counts['upcoming'] }}</div>
                <div class="text-sm text-gray-500">Upcoming Parties</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow mb-6">
            <form method="GET" class="flex gap-4 items-end flex-wrap">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All</option>
                        <option value="inquiry" {{ request('status') === 'inquiry' ? 'selected' : '' }}>New Inquiry</option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>Quote Sent</option>
                        <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="deposit_paid" {{ request('status') === 'deposit_paid' ? 'selected' : '' }}>Deposit Paid</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, or booking #"
                        class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Filter</button>
                <a href="{{ route('admin.parties.bookings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Clear</a>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guests</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($bookings as $booking)
                                    <tr class="{{ $booking->status === 'inquiry' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booking_number }}</div>
                                            <div class="text-xs text-gray-500">{{ $booking->created_at->format('M j, Y') }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->contact_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $booking->contact_email }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->event_type_display }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $booking->preferred_date->format('M j, Y') }}</div>
                                            @if($booking->preferred_time)
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->preferred_time)->format('g:i A') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->guest_count }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->location_type_display }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $booking->status_badge_color }}-100 text-{{ $booking->status_badge_color }}-800">
                                                {{ $booking->status_display }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            @if($booking->quoted_total_cents)
                                                {{ $booking->formatted_quoted_total }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('admin.parties.bookings.show', $booking) }}" class="text-purple-600 hover:text-purple-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $bookings->withQueryString()->links() }}</div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No party bookings found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
