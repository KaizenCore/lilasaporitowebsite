<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(!empty($errors))
                <div class="mb-4">
                    @foreach($errors as $error)
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-2" role="alert">
                            <span class="block sm:inline">{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(empty($cart))
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                    <p class="text-gray-600 mb-6">Add some products to get started!</p>
                    <a href="{{ route('store.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition">
                        Continue Shopping
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Cart Items ({{ $count }})</h3>
                            </div>

                            <div class="divide-y divide-gray-200">
                                @foreach($cart as $productId => $item)
                                    <div class="p-6">
                                        <div class="flex items-start gap-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                @if($item['image_path'])
                                                    <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['title'] }}" class="w-24 h-24 object-cover rounded-lg">
                                                @else
                                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                                        <span class="text-gray-400 text-xs">No Image</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Product Info -->
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-gray-900">
                                                            <a href="{{ route('store.show', $item['slug']) }}" class="hover:text-indigo-600">
                                                                {{ $item['title'] }}
                                                            </a>
                                                        </h4>
                                                        <p class="text-sm text-gray-500 mt-1">
                                                            {{ ucfirst(str_replace('_', ' ', $item['product_type'])) }}
                                                        </p>

                                                        @if(isset($item['is_available']) && !$item['is_available'])
                                                            <p class="text-sm text-red-600 mt-2 font-semibold">No longer available</p>
                                                        @elseif(isset($item['in_stock']) && !$item['in_stock'])
                                                            <p class="text-sm text-red-600 mt-2 font-semibold">Out of stock</p>
                                                        @endif
                                                    </div>

                                                    <div class="text-right">
                                                        <p class="text-lg font-bold text-gray-900">${{ number_format($item['price_cents'] / 100, 2) }}</p>
                                                    </div>
                                                </div>

                                                <!-- Quantity Controls & Remove -->
                                                <div class="mt-4 flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-2">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="product_id" value="{{ $productId }}">

                                                            <label for="quantity-{{ $productId }}" class="text-sm text-gray-700">Qty:</label>
                                                            <select name="quantity" id="quantity-{{ $productId }}" onchange="this.form.submit()"
                                                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                                                @for($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}" {{ $item['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </form>
                                                    </div>

                                                    <div class="flex items-center gap-4">
                                                        <p class="text-lg font-semibold text-gray-900">
                                                            ${{ number_format(($item['price_cents'] * $item['quantity']) / 100, 2) }}
                                                        </p>

                                                        <form action="{{ route('cart.remove', $productId) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-6 border-t border-gray-200">
                                <div class="flex justify-between">
                                    <a href="{{ route('store.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        ← Continue Shopping
                                    </a>
                                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear entire cart?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm">
                                            Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>

                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal ({{ $count }} items)</span>
                                    <span>${{ number_format($subtotal / 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Shipping</span>
                                    <span>Calculated at checkout</span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax</span>
                                    <span>Calculated at checkout</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <div class="flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total</span>
                                    <span>${{ number_format($subtotal / 100, 2) }}</span>
                                </div>
                            </div>

                            @auth
                                <a href="{{ route('checkout.order') }}" class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition mb-3">
                                    Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition mb-3">
                                    Login to Checkout
                                </a>
                                <p class="text-sm text-gray-600 text-center">or <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800">create an account</a></p>
                            @endauth

                            <p class="text-xs text-gray-500 text-center mt-4">
                                Secure checkout powered by Stripe
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
