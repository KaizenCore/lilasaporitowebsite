<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Generate Recurring Classes
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="recurringClassGenerator()">
        <!-- Template Info -->
        <div class="bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-700 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100 mb-2">Template Class</h3>
            <div class="flex items-start gap-4">
                @if($class->image_path)
                    <img src="{{ asset('storage/' . $class->image_path) }}" alt="{{ $class->title }}" class="w-20 h-20 object-cover rounded-lg">
                @else
                    <div class="w-20 h-20 bg-purple-200 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $class->title }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $class->formatted_price }} - {{ $class->duration_minutes }} minutes</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $class->location }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Capacity: {{ $class->capacity }} students</p>
                </div>
            </div>
            <p class="text-sm text-purple-700 dark:text-purple-300 mt-4">
                New classes will be created using this class as a template. Each generated class is independent and fully editable.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.classes.recurring.generate', $class) }}" method="POST" @submit="handleSubmit">
                    @csrf

                    <!-- Recurrence Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Recurrence Type *</label>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none"
                                   :class="recurrenceType === 'weekly' ? 'border-purple-500 ring-2 ring-purple-500' : 'border-gray-300 dark:border-gray-600'">
                                <input type="radio" name="recurrence_type" value="weekly" x-model="recurrenceType" class="sr-only">
                                <div class="flex flex-1 flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Weekly</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">Every week on same day</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none"
                                   :class="recurrenceType === 'biweekly' ? 'border-purple-500 ring-2 ring-purple-500' : 'border-gray-300 dark:border-gray-600'">
                                <input type="radio" name="recurrence_type" value="biweekly" x-model="recurrenceType" class="sr-only">
                                <div class="flex flex-1 flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Bi-Weekly</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">Every two weeks</span>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none"
                                   :class="recurrenceType === 'custom' ? 'border-purple-500 ring-2 ring-purple-500' : 'border-gray-300 dark:border-gray-600'">
                                <input type="radio" name="recurrence_type" value="custom" x-model="recurrenceType" class="sr-only">
                                <div class="flex flex-1 flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">Custom</span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">Select specific days</span>
                                </div>
                            </label>
                        </div>
                        @error('recurrence_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Days of Week (for custom) -->
                    <div class="mb-6" x-show="recurrenceType === 'custom'" x-transition>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Days of Week *</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="day in days" :key="day.value">
                                <button type="button"
                                    @click="toggleDay(day.value)"
                                    class="relative flex cursor-pointer rounded-lg border px-4 py-2 shadow-sm transition-all"
                                    :class="selectedDays.includes(day.value) ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/50 ring-1 ring-purple-500' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:border-purple-300'">
                                    <span class="text-sm font-medium" :class="selectedDays.includes(day.value) ? 'text-purple-700 dark:text-purple-300' : 'text-gray-900 dark:text-white'" x-text="day.label"></span>
                                </button>
                            </template>
                            <!-- Hidden inputs for form submission -->
                            <template x-for="dayValue in selectedDays" :key="'input-' + dayValue">
                                <input type="hidden" name="days_of_week[]" :value="dayValue">
                            </template>
                        </div>
                        @error('days_of_week')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date *</label>
                            <input type="date" name="start_date" id="start_date" x-model="startDate" required
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date *</label>
                            <input type="date" name="end_date" id="end_date" x-model="endDate" required
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class Time *</label>
                            <input type="time" name="time" id="time" x-model="time" required
                                value="{{ $class->class_date->format('H:i') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('time') border-red-500 @enderror">
                            @error('time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Series Name -->
                    <div class="mb-6">
                        <label for="series_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Series Name (optional)</label>
                        <input type="text" name="series_name" id="series_name" x-model="seriesName" maxlength="255"
                            placeholder="e.g., January 2026 Painting Series"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('series_name') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Group these classes together for easy filtering</p>
                        @error('series_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preview Button -->
                    <div class="mb-6">
                        <button type="button" @click="previewDates"
                            :disabled="loading || !canPreview"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!loading">Preview Dates</span>
                            <span x-show="loading" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Loading...
                            </span>
                        </button>
                    </div>

                    <!-- Preview Results -->
                    <div x-show="previewResults.length > 0" x-transition class="mb-6">
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-lg p-4">
                            <h4 class="text-lg font-semibold text-green-900 dark:text-green-100 mb-3">
                                <span x-text="previewResults.length"></span> Classes Will Be Generated
                            </h4>
                            <div class="max-h-60 overflow-y-auto">
                                <ul class="space-y-1">
                                    <template x-for="(date, index) in previewResults" :key="index">
                                        <li class="flex items-center text-sm text-green-800 dark:text-green-200">
                                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span x-text="date.formatted"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="errorMessage" x-transition class="mb-6">
                        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                            <p class="text-red-800 dark:text-red-200" x-text="errorMessage"></p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('admin.classes.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit"
                            :disabled="submitting || previewResults.length === 0"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!submitting">Generate <span x-text="previewResults.length"></span> Classes</span>
                            <span x-show="submitting" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Generating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function recurringClassGenerator() {
            return {
                recurrenceType: 'weekly',
                startDate: '',
                endDate: '',
                time: '{{ $class->class_date->format("H:i") }}',
                seriesName: '',
                selectedDays: [],
                days: [
                    { value: 1, label: 'Mon' },
                    { value: 2, label: 'Tue' },
                    { value: 3, label: 'Wed' },
                    { value: 4, label: 'Thu' },
                    { value: 5, label: 'Fri' },
                    { value: 6, label: 'Sat' },
                    { value: 7, label: 'Sun' },
                ],
                previewResults: [],
                loading: false,
                submitting: false,
                errorMessage: '',

                toggleDay(value) {
                    const index = this.selectedDays.indexOf(value);
                    if (index === -1) {
                        this.selectedDays.push(value);
                    } else {
                        this.selectedDays.splice(index, 1);
                    }
                },

                get canPreview() {
                    if (!this.startDate || !this.endDate || !this.time) return false;
                    if (this.recurrenceType === 'custom' && this.selectedDays.length === 0) return false;
                    return true;
                },

                async previewDates() {
                    this.loading = true;
                    this.errorMessage = '';
                    this.previewResults = [];

                    try {
                        const response = await fetch('{{ route("admin.classes.recurring.preview", $class) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                recurrence_type: this.recurrenceType,
                                start_date: this.startDate,
                                end_date: this.endDate,
                                time: this.time,
                                days_of_week: this.selectedDays
                            })
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (data.errors) {
                                this.errorMessage = Object.values(data.errors).flat().join(' ');
                            } else {
                                this.errorMessage = data.message || 'An error occurred';
                            }
                            return;
                        }

                        this.previewResults = data.dates;

                        if (this.previewResults.length === 0) {
                            this.errorMessage = 'No dates found in the selected range with the given settings.';
                        }

                    } catch (error) {
                        this.errorMessage = 'Failed to preview dates. Please try again.';
                        console.error(error);
                    } finally {
                        this.loading = false;
                    }
                },

                handleSubmit(e) {
                    if (this.previewResults.length === 0) {
                        e.preventDefault();
                        this.errorMessage = 'Please preview dates first before generating classes.';
                        return false;
                    }
                    this.submitting = true;
                    return true;
                }
            }
        }
    </script>
</x-admin-layout>
