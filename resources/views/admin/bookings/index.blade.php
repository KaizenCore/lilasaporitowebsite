<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Bookings
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search and Filters -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.bookings.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ticket code, name, or email"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Status</label>
                            <select name="payment_status" id="payment_status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">All</option>
                                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('payment_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        <!-- Attendance Status -->
                        <div>
                            <label for="attendance_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attendance</label>
                            <select name="attendance_status" id="attendance_status"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">All</option>
                                <option value="booked" {{ request('attendance_status') === 'booked' ? 'selected' : '' }}>Booked</option>
                                <option value="attended" {{ request('attendance_status') === 'attended' ? 'selected' : '' }}>Attended</option>
                                <option value="cancelled" {{ request('attendance_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class</label>
                            <select name="class_id" id="class_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->title }} - {{ $class->class_date->format('M d, Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.bookings.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Check-in Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Check-In</h3>
                <form method="POST" action="{{ route('admin.bookings.check-in') }}" class="flex items-end space-x-4">
                    @csrf
                    <div class="flex-1">
                        <label for="ticket_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ticket Code</label>
                        <input type="text" name="ticket_code" id="ticket_code" placeholder="FB-1234" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                        Check In
                    </button>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attendance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->ticket_code }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->user->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->artClass->title }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->artClass->class_date->format('M d, Y g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->payment_status === 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">
                                                    Completed
                                                </span>
                                            @elseif($booking->payment_status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">
                                                    Failed
                                                </span>
                                            @endif
                                            @if($booking->payment)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $booking->payment->formatted_amount }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->attendance_status === 'attended')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">
                                                    Checked In
                                                </span>
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $booking->checked_in_at?->format('M d, g:i A') }}</div>
                                            @elseif($booking->attendance_status === 'cancelled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">
                                                    Booked
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($booking->payment_status === 'completed' && $booking->attendance_status === 'booked')
                                                <form action="{{ route('admin.bookings.manual-check-in', $booking) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300">Check In</button>
                                                </form>
                                            @endif

                                            @if($booking->attendance_status !== 'cancelled')
                                                <button onclick="document.getElementById('cancel-modal-{{ $booking->id }}').classList.toggle('hidden')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 ml-3">Cancel</button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Cancel Modal -->
                                    <tr id="cancel-modal-{{ $booking->id }}" class="hidden">
                                        <td colspan="6" class="px-6 py-4 bg-gray-50 dark:bg-gray-900">
                                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="max-w-md">
                                                @csrf
                                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Cancel Booking</h4>
                                                <textarea name="cancellation_reason" rows="2" placeholder="Reason for cancellation (optional)"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 mb-2"></textarea>
                                                <div class="flex space-x-2">
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Confirm Cancel
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('cancel-modal-{{ $booking->id }}').classList.add('hidden')" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-1 px-3 rounded text-sm">
                                                        Close
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No bookings found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
