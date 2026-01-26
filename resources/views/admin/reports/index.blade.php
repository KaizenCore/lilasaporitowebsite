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
                    Download a CSV file with all payment transactions. This includes the date, customer info, description, gross amount, Stripe fees, and net amount. Perfect for your accountant!
                </p>

                <form action="{{ route('admin.reports.export') }}" method="GET" class="flex items-end space-x-4">
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time Period</label>
                        <select name="year" id="year" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="all">All Time</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download CSV
                    </button>
                </form>
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
</x-admin-layout>
