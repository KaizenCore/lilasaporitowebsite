@props(['weekDays'])

<div class="bg-white rounded-lg shadow overflow-hidden">
    <!-- Day Headers -->
    <div class="grid grid-cols-8 bg-gray-50 border-b sticky top-0">
        <div class="px-2 py-3 border-r"></div> <!-- Time column header -->
        @foreach($weekDays as $weekDay)
            <div class="px-2 py-3 text-center border-r">
                <div class="text-xs font-semibold text-gray-700 uppercase">{{ $weekDay->date->format('D') }}</div>
                <div class="mt-1 {{ $weekDay->date->isToday() ? 'bg-purple-600 text-white rounded-full w-8 h-8 mx-auto flex items-center justify-center font-bold' : 'text-lg text-gray-900' }}">
                    {{ $weekDay->date->format('j') }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- Time slots (8 AM - 8 PM) -->
    <div class="overflow-auto" style="max-height: 800px;">
        @foreach(range(8, 20) as $hour)
            <div class="grid grid-cols-8" style="min-height: 80px;">
                <!-- Time label -->
                <div class="border-r border-b px-2 py-2 text-xs text-gray-500 font-medium bg-gray-50">
                    {{ $hour === 12 ? '12:00 PM' : ($hour > 12 ? ($hour - 12) . ':00 PM' : $hour . ':00 AM') }}
                </div>

                <!-- Day columns -->
                @foreach($weekDays as $weekDay)
                    <div class="border-r border-b p-1 relative bg-white hover:bg-gray-50">
                        @foreach($weekDay->classes as $class)
                            @if($class->class_date->hour === $hour)
                                <div onclick="openQuickView({{ $class->id }})"
                                     class="cursor-pointer rounded p-2 mb-1 border-l-4 {{ $class->status === 'published' ? 'bg-blue-50 border-blue-500 hover:bg-blue-100' : 'bg-yellow-50 border-yellow-500 hover:bg-yellow-100' }}">
                                    <div class="font-semibold text-sm {{ $class->status === 'published' ? 'text-blue-900' : 'text-yellow-900' }}">
                                        {{ $class->title }}
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        {{ $class->class_date->format('g:i A') }}
                                    </div>
                                    <div class="flex items-center justify-between mt-2 text-xs">
                                        <span class="font-medium {{ $class->is_full ? 'text-red-600' : 'text-gray-700' }}">
                                            {{ $class->confirmed_bookings_count }}/{{ $class->capacity }}
                                            @if($class->is_full)
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-red-200 text-red-800 font-semibold">FULL</span>
                                            @endif
                                        </span>
                                        <span class="text-green-700 font-semibold">
                                            ${{ number_format($class->total_revenue / 100, 0) }}
                                        </span>
                                    </div>
                                    <div class="mt-1">
                                        @if($class->status === 'published')
                                            <span class="px-1.5 py-0.5 text-xs rounded bg-green-200 text-green-800 font-medium">Published</span>
                                        @elseif($class->status === 'draft')
                                            <span class="px-1.5 py-0.5 text-xs rounded bg-yellow-200 text-yellow-800 font-medium">Draft</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
