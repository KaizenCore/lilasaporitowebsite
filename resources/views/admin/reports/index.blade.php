<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reports & Export
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Navigation Tabs -->
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('admin.reports.index') }}" class="border-b-2 border-purple-500 py-4 px-1 text-sm font-medium text-purple-600 dark:text-purple-400">
                    Revenue
                </a>
                <a href="{{ route('admin.reports.bookings') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Bookings
                </a>
                <a href="{{ route('admin.reports.attendance') }}" class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Attendance
                </a>
            </nav>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- All Time -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">All Time</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Transactions</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ number_format($stats['transaction_count']) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Gross Revenue</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">${{ number_format($stats['total_revenue'] / 100, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Stripe Fees</dt>
                            <dd class="text-red-600 dark:text-red-400 font-medium">-${{ number_format(($stats['total_fees'] ?? 0) / 100, 2) }}</dd>
                        </div>
                        <div class="flex justify-between border-t dark:border-gray-700 pt-3">
                            <dt class="text-gray-900 dark:text-white font-semibold">Net Revenue</dt>
                            <dd class="text-green-600 dark:text-green-400 font-bold">${{ number_format(($stats['total_net'] ?? $stats['total_revenue']) / 100, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- This Year -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $currentYear }}</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Transactions</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">{{ number_format($stats['year_transaction_count']) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Gross Revenue</dt>
                            <dd class="text-gray-900 dark:text-white font-medium">${{ number_format($stats['year_revenue'] / 100, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Stripe Fees</dt>
                            <dd class="text-red-600 dark:text-red-400 font-medium">-${{ number_format(($stats['year_fees'] ?? 0) / 100, 2) }}</dd>
                        </div>
                        <div class="flex justify-between border-t dark:border-gray-700 pt-3">
                            <dt class="text-gray-900 dark:text-white font-semibold">Net Revenue</dt>
                            <dd class="text-green-600 dark:text-green-400 font-bold">${{ number_format(($stats['year_net'] ?? $stats['year_revenue']) / 100, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Export Section -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Export Payment Data</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Download payment transactions as CSV (for spreadsheets) or PDF (for records/accountant).
                </p>

                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Period</label>
                        <select name="year" id="year" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="all">All Time</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('admin.reports.export') }}?year={{ $currentYear }}" id="csvLink" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download CSV
                    </a>
                    <a href="{{ route('admin.reports.revenue.pdf') }}?year={{ $currentYear }}" id="pdfLink" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </a>
                    <button type="button" onclick="emailReport()" id="emailBtn" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Email to Self
                    </button>
                </div>
            </div>
        </div>

        <!-- What's Included -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">What's included in the export:</h4>
            <ul class="text-sm text-blue-700 dark:text-blue-400 list-disc list-inside space-y-1">
                <li>Date of each transaction</li>
                <li>Type (Class Booking, Store Order, etc.)</li>
                <li>Customer name and email</li>
                <li>Description (class name or order number)</li>
                <li>Gross amount, Stripe fees, and net amount</li>
                <li>Payment IDs for reference</li>
            </ul>
        </div>
    </div>

    <!-- Email Confirmation Modal -->
    <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3 text-center">
                <div id="emailSpinner" class="hidden">
                    <svg class="animate-spin h-12 w-12 mx-auto text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">Sending report...</p>
                </div>
                <div id="emailSuccess" class="hidden">
                    <svg class="h-12 w-12 mx-auto text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Report Sent!</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Check your email for the PDF report.</p>
                </div>
                <div id="emailError" class="hidden">
                    <svg class="h-12 w-12 mx-auto text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mt-4">Failed to Send</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="emailErrorMsg">Please try again later.</p>
                </div>
                <div class="mt-4">
                    <button onclick="closeEmailModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update export links when year changes
        document.getElementById('year').addEventListener('change', function() {
            const year = this.value;
            document.getElementById('csvLink').href = '{{ route('admin.reports.export') }}?year=' + year;
            document.getElementById('pdfLink').href = '{{ route('admin.reports.revenue.pdf') }}?year=' + year;
        });

        function emailReport() {
            const year = document.getElementById('year').value;
            const modal = document.getElementById('emailModal');
            const spinner = document.getElementById('emailSpinner');
            const success = document.getElementById('emailSuccess');
            const error = document.getElementById('emailError');

            modal.classList.remove('hidden');
            spinner.classList.remove('hidden');
            success.classList.add('hidden');
            error.classList.add('hidden');

            fetch('{{ route('admin.reports.revenue.email') }}?year=' + year, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                spinner.classList.add('hidden');
                if (data.success) {
                    success.classList.remove('hidden');
                } else {
                    error.classList.remove('hidden');
                    document.getElementById('emailErrorMsg').textContent = data.message || 'Please try again later.';
                }
            })
            .catch(err => {
                spinner.classList.add('hidden');
                error.classList.remove('hidden');
            });
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
