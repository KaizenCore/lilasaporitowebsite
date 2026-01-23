<x-public-layout>
    <x-slot name="title">Order {{ $order->order_number }} - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <a href="{{ route('orders.index') }}" class="inline-flex items-center text-purple-100 hover:text-white transition mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Orders
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Order {{ $order->order_number }}</h1>
            <p class="text-purple-100">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
    </section>

    <!-- Order Content -->
    <section class="py-12 px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Status Banner -->
            <div class="mb-8">
                @if($order->payment_status === 'completed')
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 flex items-center gap-3">
                        <div class="bg-green-100 dark:bg-green-900/50 p-2 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-green-800 dark:text-green-300">Payment Completed</p>
                            <p class="text-sm text-green-600 dark:text-green-400">Your order has been paid successfully</p>
                        </div>
                    </div>
                @elseif($order->payment_status === 'pending')
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 flex items-center gap-3">
                        <div class="bg-yellow-100 dark:bg-yellow-900/50 p-2 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-300">Payment Pending</p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Your payment is being processed</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Digital Downloads -->
            @php
                $digitalItems = $order->items->filter(fn($item) => $item->product_type === 'digital' && $item->digital_download_url);
            @endphp

            @if($digitalItems->count() > 0)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Digital Downloads
                    </h2>
                    <div class="space-y-3">
                        @foreach($digitalItems as $item)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product_title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Downloaded {{ $item->download_count }} {{ Str::plural('time', $item->download_count) }}</p>
                                </div>
                                <a href="{{ route('download', $item->digital_download_url) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Order Items -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Order Items</h2>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($order->items as $item)
                        <div class="p-6 flex gap-4">
                            @if($item->product && $item->product->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product_title }}" class="w-20 h-20 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->product_title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($item->product_type) }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $item->formatted_price }} x {{ $item->quantity }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900 dark:text-white">{{ $item->formatted_total }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Order Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal_cents / 100, 2) }}</span>
                    </div>
                    @if($order->shipping_cents > 0)
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Shipping</span>
                            <span>${{ number_format($order->shipping_cents / 100, 2) }}</span>
                        </div>
                    @endif
                    @if($order->tax_cents > 0)
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Tax</span>
                            <span>${{ number_format($order->tax_cents / 100, 2) }}</span>
                        </div>
                    @endif
                    @if($order->discount_cents > 0)
                        <div class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Discount</span>
                            <span>-${{ number_format($order->discount_cents / 100, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white pt-3 border-t border-gray-200 dark:border-gray-700">
                        <span>Total</span>
                        <span>{{ $order->formatted_total }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            @if($order->payment)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Payment Details</h2>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Payment Method</p>
                            <p class="font-semibold text-gray-900 dark:text-white capitalize">{{ $order->payment->payment_method ?? 'Card' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Payment Date</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $order->payment->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @if($order->payment->stripe_payment_intent_id)
                            <div class="sm:col-span-2">
                                <p class="text-gray-500 dark:text-gray-400">Transaction ID</p>
                                <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $order->payment->stripe_payment_intent_id }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4">
                <a href="{{ route('store.index') }}" class="flex-1 text-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                    Continue Shopping
                </a>
                <a href="{{ route('orders.index') }}" class="flex-1 text-center px-6 py-3 border-2 border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400 font-semibold rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 transition">
                    View All Orders
                </a>
            </div>
        </div>
    </section>
</x-public-layout>
