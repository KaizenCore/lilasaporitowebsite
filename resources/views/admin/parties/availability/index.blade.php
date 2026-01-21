<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Party Availability Calendar
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add Slot Form -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Single Slot</h3>
                    <form action="{{ route('admin.parties.availability.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Date *</label>
                            <input type="date" name="date" required min="{{ now()->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Time *</label>
                                <input type="time" name="start_time" required value="10:00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Time *</label>
                                <input type="time" name="end_time" required value="14:00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Add Slot
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Bulk Add Slots</h3>
                    <form action="{{ route('admin.parties.availability.bulk') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date *</label>
                                <input type="date" name="start_date" required min="{{ now()->format('Y-m-d') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date *</label>
                                <input type="date" name="end_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Days of Week *</label>
                            <div class="grid grid-cols-4 gap-2">
                                @foreach(['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6] as $day => $num)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $num }}" class="rounded border-gray-300 text-purple-600">
                                        <span class="ml-1 text-sm">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Time *</label>
                                <input type="time" name="start_time" required value="10:00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Time *</label>
                                <input type="time" name="end_time" required value="14:00"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Create Slots
                        </button>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Blackout Dates</h3>
                    <form action="{{ route('admin.parties.availability.blackout') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date *</label>
                                <input type="date" name="start_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date *</label>
                                <input type="date" name="end_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Reason</label>
                            <input type="text" name="reason" placeholder="e.g., Vacation, Holiday"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Block Dates
                        </button>
                    </form>
                </div>
            </div>

            <!-- Calendar View -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <!-- Month Navigation -->
                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('admin.parties.availability.index', ['month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}"
                            class="px-3 py-1 border rounded hover:bg-gray-100">&larr; Prev</a>
                        <h3 class="text-lg font-semibold">{{ $startDate->format('F Y') }}</h3>
                        <a href="{{ route('admin.parties.availability.index', ['month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}"
                            class="px-3 py-1 border rounded hover:bg-gray-100">Next &rarr;</a>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1 text-center text-sm mb-2">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="font-semibold text-gray-500 py-2">{{ $day }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-1">
                        @php
                            $current = $startDate->copy()->startOfWeek(0);
                            $end = $endDate->copy()->endOfWeek(6);
                        @endphp

                        @while($current <= $end)
                            @php
                                $dateKey = $current->format('Y-m-d');
                                $daySlots = $slots->get($dateKey) ?? collect();
                                $isCurrentMonth = $current->month === $startDate->month;
                                $isToday = $current->isToday();
                                $isPast = $current->isPast() && !$isToday;
                            @endphp

                            <div class="min-h-24 border rounded p-1 {{ !$isCurrentMonth ? 'bg-gray-50 text-gray-400' : '' }} {{ $isToday ? 'ring-2 ring-purple-500' : '' }} {{ $isPast ? 'opacity-50' : '' }}">
                                <div class="text-xs font-medium {{ $isToday ? 'text-purple-600' : '' }}">{{ $current->day }}</div>
                                @if($daySlots->count() > 0)
                                    <div class="mt-1 space-y-1">
                                        @foreach($daySlots as $slot)
                                            <div class="text-xs px-1 py-0.5 rounded truncate
                                                {{ $slot->status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $slot->status === 'booked' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $slot->status === 'blocked' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $slot->start_time->format('g:ia') }}
                                                @if($slot->status === 'available' && !$isPast)
                                                    <form action="{{ route('admin.parties.availability.destroy', $slot) }}" method="POST" class="inline">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 ml-1">&times;</button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            @php $current->addDay(); @endphp
                        @endwhile
                    </div>

                    <!-- Legend -->
                    <div class="mt-4 flex gap-4 text-sm">
                        <span class="flex items-center"><span class="w-3 h-3 rounded bg-green-100 mr-1"></span> Available</span>
                        <span class="flex items-center"><span class="w-3 h-3 rounded bg-blue-100 mr-1"></span> Booked</span>
                        <span class="flex items-center"><span class="w-3 h-3 rounded bg-red-100 mr-1"></span> Blocked</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
