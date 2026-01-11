<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Class Cart') }}
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

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if(empty($cart))
                <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">Your class cart is empty</h3>
                    <p class="text-gray-600 mb-6">Browse our classes and add some to get started!</p>
                    <a href="{{ route('classes.index') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition">
                        Browse Classes
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Classes in Cart ({{ $count }})</h3>
                            </div>

                            <div class="divide-y divide-gray-200">
                                @foreach($cart as $classId => $item)
                                    <div class="p-6">
                                        <div class="flex items-start gap-4">
                                            <!-- Class Image -->
                                            <div class="flex-shrink-0">
                                                @if($item['image_path'])
                                                    <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['title'] }}" class="w-24 h-24 object-cover rounded-lg">
                                                @else
                                                    <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Class Info -->
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <h4 class="text-lg font-semibold text-gray-900">
                                                            <a href="{{ route('classes.show', $item['slug']) }}" class="hover:text-purple-600">
                                                                {{ $item['title'] }}
                                                            </a>
                                                        </h4>
                                                        <div class="mt-2 space-y-1">
                                                            <p class="text-sm text-gray-600 flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                                {{ $item['class_date_formatted'] }}
                                                            </p>
                                                            @if($item['duration_minutes'])
                                                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    {{ $item['duration_minutes'] }} minutes
                                                                </p>
                                                            @endif
                                                            @if($item['location'])
                                                                <p class="text-sm text-gray-600 flex items-center gap-1">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    {{ $item['location'] }}
                                                                </p>
                                                            @endif
                                                        </div>

                                                        @if(isset($item['is_available']) && !$item['is_available'])
                                                            <p class="text-sm text-red-600 mt-2 font-semibold">This class is no longer available</p>
                                                        @elseif(isset($item['spots_available']) && $item['spots_available'] <= 3)
                                                            <p class="text-sm text-orange-600 mt-2 font-semibold">Only {{ $item['spots_available'] }} spots left!</p>
                                                        @endif
                                                    </div>

                                                    <div class="text-right">
                                                        <p class="text-lg font-bold text-gray-900">${{ number_format($item['price_cents'] / 100, 2) }}</p>
                                                        <p class="text-xs text-gray-500">1 ticket</p>
                                                    </div>
                                                </div>

                                                <!-- Remove Button -->
                                                <div class="mt-4 flex items-center justify-end">
                                                    <form action="{{ route('class-cart.remove', $classId) }}" method="POST">
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
                                @endforeach
                            </div>

                            <div class="p-6 border-t border-gray-200">
                                <div class="flex justify-between">
                                    <a href="{{ route('classes.index') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                                        &larr; Browse More Classes
                                    </a>
                                    <form action="{{ route('class-cart.clear') }}" method="POST" onsubmit="return confirm('Clear entire cart?')">
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
                                    <span>Subtotal ({{ $count }} {{ Str::plural('class', $count) }})</span>
                                    <span>${{ number_format($subtotal / 100, 2) }}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4 mb-6">
                                <div class="flex justify-between text-lg font-bold text-gray-900">
                                    <span>Total</span>
                                    <span>${{ number_format($subtotal / 100, 2) }}</span>
                                </div>
                            </div>

                            <a href="{{ route('checkout.classes') }}" class="block w-full bg-purple-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition mb-3">
                                Proceed to Checkout
                            </a>

                            <p class="text-xs text-gray-500 text-center mt-4">
                                Secure checkout powered by Stripe
                            </p>

                            <div class="mt-6 p-4 bg-purple-50 rounded-lg">
                                <h4 class="text-sm font-semibold text-purple-900 mb-2">What's included:</h4>
                                <ul class="text-sm text-purple-700 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        One ticket per class
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        All materials included
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Unique ticket code for each class
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
