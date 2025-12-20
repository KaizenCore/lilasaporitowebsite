<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manage Bookings
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Search and Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('admin.bookings.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Search -->
                        <div class="lg:col-span-2">
                            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Ticket code, name, or email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Payment Status -->
                        <div>
                            <label for="payment_status" class="block text-sm font-medium text-gray-700">Payment Status</label>
                            <select name="payment_status" id="payment_status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All</option>
                                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ request('payment_status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                        </div>

                        <!-- Attendance Status -->
                        <div>
                            <label for="attendance_status" class="block text-sm font-medium text-gray-700">Attendance</label>
                            <select name="attendance_status" id="attendance_status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">All</option>
                                <option value="booked" {{ request('attendance_status') === 'booked' ? 'selected' : '' }}>Booked</option>
                                <option value="attended" {{ request('attendance_status') === 'attended' ? 'selected' : '' }}>Attended</option>
                                <option value="cancelled" {{ request('attendance_status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <!-- Class -->
                        <div>
                            <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                            <select name="class_id" id="class_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Apply Filters
                            </button>
                            <a href="{{ route('admin.bookings.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Check-in Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Check-In</h3>
                <form method="POST" action="{{ route('admin.bookings.check-in') }}" class="flex items-end space-x-4">
                    @csrf
                    <div class="flex-1">
                        <label for="ticket_code" class="block text-sm font-medium text-gray-700">Ticket Code</label>
                        <input type="text" name="ticket_code" id="ticket_code" placeholder="FB-1234" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                        Check In
                    </button>
                </form>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ticket</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bookings as $booking)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->ticket_code }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->artClass->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->artClass->class_date->format('M d, Y g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->payment_status === 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($booking->payment_status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Failed
                                                </span>
                                            @endif
                                            @if($booking->payment)
                                                <div class="text-xs text-gray-500 mt-1">{{ $booking->payment->formatted_amount }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($booking->attendance_status === 'attended')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Checked In
                                                </span>
                                                <div class="text-xs text-gray-500 mt-1">{{ $booking->checked_in_at?->format('M d, g:i A') }}</div>
                                            @elseif($booking->attendance_status === 'cancelled')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Cancelled
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Booked
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($booking->payment_status === 'completed' && $booking->attendance_status === 'booked')
                                                <form action="{{ route('admin.bookings.manual-check-in', $booking) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Check In</button>
                                                </form>
                                            @endif

                                            @if($booking->attendance_status !== 'cancelled')
                                                <button onclick="document.getElementById('cancel-modal-{{ $booking->id }}').classList.toggle('hidden')" class="text-red-600 hover:text-red-900 ml-3">Cancel</button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Cancel Modal -->
                                    <tr id="cancel-modal-{{ $booking->id }}" class="hidden">
                                        <td colspan="6" class="px-6 py-4 bg-gray-50">
                                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="max-w-md">
                                                @csrf
                                                <h4 class="font-medium text-gray-900 mb-2">Cancel Booking</h4>
                                                <textarea name="cancellation_reason" rows="2" placeholder="Reason for cancellation (optional)"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-2"></textarea>
                                                <div class="flex space-x-2">
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                        Confirm Cancel
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('cancel-modal-{{ $booking->id }}').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1 px-3 rounded text-sm">
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
