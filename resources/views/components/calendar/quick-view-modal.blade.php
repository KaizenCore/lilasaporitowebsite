<div x-data="calendarQuickView()"
     x-show="showModal"
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto"
     @keydown.escape.window="closeModal()">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="closeModal()"></div>

    <!-- Modal -->
    <div class="flex min-h-screen items-center justify-center p-4">
        <div @click.stop class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
            <!-- Close Button -->
            <button @click="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Loading State -->
            <div x-show="loading" class="text-center py-12">
                <svg class="animate-spin h-12 w-12 mx-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-4 text-gray-600">Loading class details...</p>
            </div>

            <!-- Content -->
            <div x-show="!loading && classData">
                <!-- Header -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-900" x-text="classData?.title"></h3>
                    <div class="mt-2 flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span x-text="formatDate(classData?.class_date)"></span>
                    </div>
                    <div class="mt-1 flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span x-text="classData?.location"></span>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Bookings</div>
                        <div class="text-2xl font-bold text-blue-900" x-text="classData?.bookings_count + '/' + classData?.capacity"></div>
                        <div class="text-xs mt-1" :class="classData?.is_full ? 'text-red-600 font-semibold' : 'text-gray-500'">
                            <span x-show="classData?.is_full">FULL</span>
                            <span x-show="!classData?.is_full" x-text="(classData?.capacity - classData?.bookings_count) + ' spots left'"></span>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Revenue</div>
                        <div class="text-2xl font-bold text-green-900" x-text="'$' + ((classData?.revenue || 0) / 100).toFixed(2)"></div>
                        <div class="text-xs text-gray-500 mt-1">Total earned</div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600">Status</div>
                        <div class="text-sm font-semibold capitalize mt-2">
                            <span x-show="classData?.status === 'published'" class="px-2 py-1 rounded bg-green-200 text-green-800">Published</span>
                            <span x-show="classData?.status === 'draft'" class="px-2 py-1 rounded bg-yellow-200 text-yellow-800">Draft</span>
                            <span x-show="classData?.status === 'cancelled'" class="px-2 py-1 rounded bg-red-200 text-red-800">Cancelled</span>
                        </div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="mb-6" x-show="classData?.bookings?.length > 0">
                    <h4 class="font-semibold text-lg mb-3 text-gray-900">Recent Bookings</h4>
                    <div class="max-h-64 overflow-y-auto space-y-2 bg-gray-50 rounded-lg p-3">
                        <template x-for="booking in classData?.bookings" :key="booking.id">
                            <div class="flex items-center justify-between p-2 bg-white rounded border">
                                <div>
                                    <div class="font-medium text-gray-900" x-text="booking.user.name"></div>
                                    <div class="text-xs text-gray-500" x-text="booking.ticket_code"></div>
                                </div>
                                <div>
                                    <span x-show="booking.attendance_status === 'attended'"
                                          class="px-2 py-1 text-xs rounded bg-green-100 text-green-800 font-medium">Checked In</span>
                                    <span x-show="booking.attendance_status === 'booked'"
                                          class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 font-medium">Booked</span>
                                    <span x-show="booking.attendance_status === 'cancelled'"
                                          class="px-2 py-1 text-xs rounded bg-red-100 text-red-800 font-medium">Cancelled</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="!classData?.bookings || classData?.bookings?.length === 0" class="mb-6 text-center py-8 bg-gray-50 rounded-lg">
                    <p class="text-gray-500">No bookings yet</p>
                </div>

                <!-- Quick Actions -->
                <div class="flex gap-3">
                    <a :href="'/admin/classes/' + classData?.id + '/edit'"
                       class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-center font-medium">
                        Edit Class
                    </a>
                    <a :href="'/admin/classes/' + classData?.id + '/check-in'"
                       class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-center font-medium"
                       x-show="classData?.bookings_count > 0">
                        Check-in
                    </a>
                    <button @click="closeModal()"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
