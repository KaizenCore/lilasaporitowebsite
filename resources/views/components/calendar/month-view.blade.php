@props(['calendarDays'])

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Day Headers (Sun-Sat) -->
    <div class="grid grid-cols-7 bg-gray-50 border-b">
        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
            <div class="px-2 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wide">
                {{ $day }}
            </div>
        @endforeach
    </div>

    <!-- Calendar Days Grid -->
    <div class="grid grid-cols-7" style="min-height: 600px;">
        @foreach($calendarDays as $day)
            <div class="border-r border-b p-2 min-h-[120px] {{ $day->isCurrentMonth ? 'bg-white' : 'bg-gray-50' }}">
                <!-- Day Number -->
                <div class="mb-2">
                    @if($day->isToday)
                        <span class="bg-indigo-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm font-bold">
                            {{ $day->day }}
                        </span>
                    @else
                        <span class="text-sm font-medium {{ $day->isCurrentMonth ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ $day->day }}
                        </span>
                    @endif
                </div>

                <!-- Events for this day -->
                <div class="space-y-1">
                    @php
                        $displayCount = 3;
                        $totalClasses = $day->classes->count();
                    @endphp

                    @foreach($day->classes->take($displayCount) as $class)
                        <div onclick="openQuickView({{ $class->id }})"
                             class="cursor-pointer rounded px-2 py-1 text-xs border-l-2 {{ $class->status === 'published' ? 'bg-blue-50 border-blue-400 hover:bg-blue-100' : 'bg-yellow-50 border-yellow-400 hover:bg-yellow-100' }}">
                            <div class="font-semibold truncate {{ $class->status === 'published' ? 'text-blue-900' : 'text-yellow-900' }}">
                                {{ $class->title }}
                            </div>
                            <div class="text-gray-600 flex items-center justify-between">
                                <span>{{ $class->class_date->format('g:i A') }}</span>
                                <span class="font-medium">{{ $class->confirmed_bookings_count }}/{{ $class->capacity }}</span>
                            </div>
                        </div>
                    @endforeach

                    @if($totalClasses > $displayCount)
                        <div class="text-xs text-gray-500 px-2 py-1 font-medium">
                            +{{ $totalClasses - $displayCount }} more
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
