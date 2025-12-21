<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Calendar View
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- View Toggle Tabs (Month/Week) -->
        <div x-data="{ activeView: '{{ $view }}' }" class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.calendar.index', ['view' => 'month', 'date' => $date]) }}"
                       @click="activeView = 'month'"
                       :class="activeView === 'month' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Month View
                    </a>
                    <a href="{{ route('admin.calendar.index', ['view' => 'week', 'date' => $date]) }}"
                       @click="activeView = 'week'"
                       :class="activeView === 'week' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Week View
                    </a>
                </nav>
            </div>
        </div>

        <!-- Navigation (Previous/Next/Today) -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.calendar.index', ['view' => $view, 'date' => $previousDate]) }}"
                   class="bg-white px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
                    ← Previous
                </a>
                <h3 class="text-xl font-semibold text-gray-900">{{ $displayDate }}</h3>
                <a href="{{ route('admin.calendar.index', ['view' => $view, 'date' => $nextDate]) }}"
                   class="bg-white px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
                    Next →
                </a>
            </div>
            <a href="{{ route('admin.calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                Today
            </a>
        </div>

        <!-- Calendar Grid -->
        @if($view === 'month')
            <x-calendar.month-view :calendarDays="$calendarDays" />
        @else
            <x-calendar.week-view :weekDays="$weekDays" />
        @endif

        <!-- Quick View Modal -->
        <x-calendar.quick-view-modal />
    </div>
</x-admin-layout>
