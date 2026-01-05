<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Calendar View
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- View Toggle Tabs (Month/Week) -->
        <div x-data="{ activeView: '{{ $view }}' }" class="mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.calendar.index', ['view' => 'month', 'date' => $date]) }}"
                       @click="activeView = 'month'"
                       :class="activeView === 'month' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Month View
                    </a>
                    <a href="{{ route('admin.calendar.index', ['view' => 'week', 'date' => $date]) }}"
                       @click="activeView = 'week'"
                       :class="activeView === 'week' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
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
                   class="bg-white dark:bg-gray-800 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                    ← Previous
                </a>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $displayDate }}</h3>
                <a href="{{ route('admin.calendar.index', ['view' => $view, 'date' => $nextDate]) }}"
                   class="bg-white dark:bg-gray-800 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Next →
                </a>
            </div>
            <a href="{{ route('admin.calendar.index', ['view' => $view, 'date' => now()->format('Y-m-d')]) }}"
               class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 text-sm font-medium">
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
