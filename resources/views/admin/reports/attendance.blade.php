<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Attendance Lists
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.reports.index') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Revenue
                </a>
                <a href="{{ route('admin.reports.bookings') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Bookings
                </a>
                <a href="{{ route('admin.reports.attendance') }}" class="border-b-2 border-purple-500 py-4 px-1 text-sm font-medium text-purple-600 dark:text-purple-400">
                    Attendance
                </a>
            </nav>
        </div>

        <!-- Class Selector -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Select a Class</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Choose a class to view and print the attendance sheet for check-in.
                </p>
                <form method="GET" action="{{ route('admin.reports.attendance') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-64">
                        <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Class</label>
                        <select name="class_id" id="class_id" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">-- Select a class --</option>
                            @foreach($upcomingClasses as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->title }} - {{ $class->class_date->format('M j, Y g:i A') }}
                                    ({{ $class->bookings->where('payment_status', 'completed')->count() }}/{{ $class->capacity }})
                                </option>
                            @endforeach
                            @if($pastClasses->isNotEmpty())
                                <optgroup label="Past Classes">
                                    @foreach($pastClasses as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->title }} - {{ $class->class_date->format('M j, Y') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View Attendance
                    </button>
                </form>
            </div>
        </div>

        @if($selectedClass)
            <!-- Class Info -->
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4 mb-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-purple-800 dark:text-purple-300">{{ $selectedClass->title }}</h3>
                        <p class="text-purple-600 dark:text-purple-400">
                            {{ $selectedClass->class_date->format('l, F j, Y \a\t g:i A') }}
                            &bull; {{ $selectedClass->location }}
                        </p>
                    </div>
                    <div class="flex space-x-4">
                        <button onclick="window.print()" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bookings->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Registered</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $bookings->where('attendance_status', 'attended')->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Checked In</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-gray-600">{{ $bookings->whereNotIn('attendance_status', ['attended', 'cancelled'])->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600">{{ $selectedClass->capacity - $bookings->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Spots Left</div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">Check</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ticket Code</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($bookings as $booking)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($booking->attendance_status === 'attended')
                                            <span class="text-green-600 text-2xl">&#10003;</span>
                                        @else
                                            <span class="inline-block w-5 h-5 border-2 border-gray-400 rounded"></span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $booking->user?->name ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $booking->ticket_code }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $booking->user?->email ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($booking->attendance_status === 'attended')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Checked In</span>
                                        @elseif($booking->attendance_status === 'cancelled')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Cancelled</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Registered</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        No attendees registered for this class yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Check-In Link -->
            <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300">Need to check in attendees?</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-400">Use the check-in form to scan or enter ticket codes.</p>
                    </div>
                    <a href="{{ route('admin.classes.check-in-form', $selectedClass) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Go to Check-In
                    </a>
                </div>
            </div>
        @else
            <!-- No Class Selected -->
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No class selected</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Select a class from the dropdown above to view and print the attendance list.
                </p>
            </div>
        @endif
    </div>

    @push('styles')
    <style>
        @media print {
            nav, .no-print, button, a:not(.print-link), form, .mb-6:first-child {
                display: none !important;
            }
            .bg-purple-50, .bg-blue-50 {
                background: white !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
    @endpush
</x-admin-layout>
