<x-public-layout>
    <x-slot name="title">My Orders - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">My Orders</h1>
            <p class="text-purple-100">View your purchase history and order details</p>
        </div>
    </section>

    <!-- Orders Content -->
    <section class="py-12 px-4">
        <div class="max-w-4xl mx-auto">
            @if($orders->isEmpty())
                <!-- Empty State -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 dark:bg-purple-900/50 rounded-full mb-6">
                        <svg class="w-10 h-10 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No orders yet</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">You haven't made any purchases yet. Check out our store!</p>
                    <a href="{{ route('store.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition">
                        Browse Store
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            @else
                <!-- Orders List -->
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="block bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition group">
                            <div class="p-6">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <!-- Order Info -->
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition">
                                                {{ $order->order_number }}
                                            </span>
                                            @if($order->payment_status === 'completed')
                                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-full">
                                                    Paid
                                                </span>
                                            @elseif($order->payment_status === 'pending')
                                                <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 rounded-full">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                                            {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                                        </p>
                                    </div>

                                    <!-- Order Total & Arrow -->
                                    <div class="flex items-center gap-4">
                                        <span class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $order->formatted_total }}
                                        </span>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Item Thumbnails -->
                                @if($order->items->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            @foreach($order->items->take(4) as $item)
                                                @if($item->product && $item->product->image_path)
                                                    <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product_title }}" class="w-12 h-12 object-cover rounded-lg">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            @endforeach
                                            @if($order->items->count() > 4)
                                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                                                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">+{{ $order->items->count() - 4 }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
