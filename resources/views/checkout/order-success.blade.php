<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmed - FrizzBoss Store</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        FrizzBoss
                    </a>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-purple-600 font-medium transition">Home</a>
                    <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Store</a>
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Cart</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Success Content -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Order Confirmed!</h1>
            <p class="text-xl text-gray-600">Thank you for your purchase. Your order has been received!</p>
        </div>

        <!-- Order Number -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 mb-8 text-white">
            <div class="text-center">
                <p class="text-purple-100 mb-2 text-lg">Order Number</p>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl py-6 px-8 mb-4">
                    <p class="text-5xl font-bold tracking-wider">{{ $order->order_number }}</p>
                </div>
                <p class="text-purple-100 text-sm">Save this number for your records</p>
            </div>
        </div>

        <!-- Digital Downloads (if applicable) -->
        @php
            $digitalItems = $order->orderItems->filter(fn($item) => $item->product_type === 'digital');
        @endphp

        @if($digitalItems->count() > 0)
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Your Digital Downloads
                </h2>
                <p class="text-gray-600 mb-6">Click below to download your digital products</p>

                <div class="space-y-3">
                    @foreach($digitalItems as $item)
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $item->product_title }}</h3>
                                    <p class="text-sm text-gray-500">Downloads: {{ $item->download_count }}</p>
                                </div>
                                <a href="{{ route('download', $item->digital_download_url) }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Download
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Order Details -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Details</h2>

            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                        @if($item->product && $item->product->image_path)
                            <img src="{{ asset('storage/' . $item->product->image_path) }}" alt="{{ $item->product_title }}" class="w-20 h-20 object-cover rounded">
                        @else
                            <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-xs text-gray-400">No img</span>
                            </div>
                        @endif

                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $item->product_title }}</h3>
                            <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $item->product_type)) }}</p>
                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                        </div>

                        <div class="text-right">
                            <p class="font-semibold text-gray-900">${{ number_format($item->total_cents / 100, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Summary</h2>

            <div class="space-y-3">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal_cents / 100, 2) }}</span>
                </div>
                @if($order->shipping_cents > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span>${{ number_format($order->shipping_cents / 100, 2) }}</span>
                    </div>
                @endif
                @if($order->tax_cents > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span>${{ number_format($order->tax_cents / 100, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Payment Method</span>
                    <span class="capitalize">{{ $order->payment->payment_method ?? 'Card' }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Payment Date</span>
                    <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-200">
                    <span>Total Paid</span>
                    <span class="text-green-600">${{ number_format($order->total_amount_cents / 100, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">What's Next?</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Order Confirmation Email</p>
                        <p class="text-sm text-gray-600">A confirmation email has been sent to {{ $order->email }}</p>
                    </div>
                </li>

                @if($digitalItems->count() > 0)
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Download Your Files</p>
                            <p class="text-sm text-gray-600">Digital products are available immediately above</p>
                        </div>
                    </li>
                @endif

                @php
                    $physicalItems = $order->orderItems->filter(fn($item) => $item->product_type === 'physical');
                @endphp

                @if($physicalItems->count() > 0)
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <div>
                            <p class="font-semibold">Order Processing</p>
                            <p class="text-sm text-gray-600">Your order is being prepared for shipment. You'll receive a shipping notification soon.</p>
                        </div>
                    </li>
                @endif

                <li class="flex items-start">
                    <svg class="w-6 h-6 text-purple-600 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold">Keep Shopping</p>
                        <p class="text-sm text-gray-600">Explore our full collection of art supplies, prints, and classes</p>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('store.index') }}" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition text-center shadow-lg">
                Continue Shopping
            </a>
            <a href="/" class="flex-1 bg-white border-2 border-purple-600 text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-purple-50 transition text-center">
                Back to Home
            </a>
        </div>

        <!-- Support -->
        <div class="text-center mt-12 text-gray-600">
            <p>Questions about your order?</p>
            <p class="mt-2">Contact us and we'll be happy to help!</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center">
                <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent mb-4">FrizzBoss</h3>
                <p class="text-gray-400 mb-8">Inspiring creativity, one class at a time.</p>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FrizzBoss. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
