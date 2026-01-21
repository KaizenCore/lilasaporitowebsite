<x-public-layout>
    <x-slot name="title">{{ $product->title }} - FrizzBoss Store</x-slot>

    <!-- Back Button -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <a href="{{ route('store.index') }}" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Store
        </a>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <!-- Product Details -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                <!-- Product Image -->
                <div>
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-96 object-cover rounded-xl shadow-lg">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 flex items-center justify-center rounded-xl shadow-lg">
                            <svg class="w-24 h-24 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Product Info -->
                <div>
                    <div class="mb-4">
                        @if($product->category)
                            <a href="{{ route('store.category', $product->category->slug) }}" class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium">
                                {{ $product->category->name }}
                            </a>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $product->title }}</h1>

                    @if($product->sku)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">SKU: {{ $product->sku }}</p>
                    @endif

                    <!-- Price -->
                    <div class="mb-6">
                        <div class="flex items-center gap-4">
                            <span class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $product->formatted_price }}</span>
                            @if($product->is_on_sale)
                                <span class="text-xl text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->compare_at_price_cents / 100, 2) }}</span>
                                <span class="px-3 py-1 bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 text-sm font-semibold rounded-full">
                                    Save {{ $product->sale_percentage }}%
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Product Type -->
                    <div class="mb-6 flex flex-wrap gap-2">
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                            {{ $product->product_type === 'physical' ? 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300' : '' }}
                            {{ $product->product_type === 'digital' ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : '' }}
                            {{ $product->product_type === 'class_package' ? 'bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-300' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
                        </span>
                        @if($product->is_featured)
                            <span class="inline-block px-3 py-1 text-sm font-semibold bg-pink-100 dark:bg-pink-900/50 text-pink-800 dark:text-pink-300 rounded-full">
                                Featured
                            </span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    @if($product->product_type === 'physical')
                        <div class="mb-6">
                            @if($product->is_out_of_stock)
                                <span class="text-red-600 dark:text-red-400 font-semibold">Out of Stock</span>
                            @elseif(is_null($product->stock_quantity))
                                <span class="text-green-600 dark:text-green-400 font-semibold">In Stock</span>
                            @elseif($product->stock_quantity < 10)
                                <span class="text-orange-600 dark:text-orange-400 font-semibold">Only {{ $product->stock_quantity }} left in stock!</span>
                            @else
                                <span class="text-green-600 dark:text-green-400 font-semibold">In Stock</span>
                            @endif
                        </div>
                    @endif

                    <!-- Short Description -->
                    @if($product->short_description)
                        <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">{{ $product->short_description }}</p>
                    @endif

                    <!-- Add to Cart -->
                    <div class="mb-6">
                        @if($product->is_out_of_stock)
                            <button disabled class="w-full bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 px-6 py-4 rounded-xl font-semibold cursor-not-allowed text-lg">
                                Out of Stock
                            </button>
                        @else
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full bg-purple-600 text-white px-6 py-4 rounded-xl font-semibold hover:bg-purple-700 transition text-lg shadow-lg">
                                    Add to Cart - {{ $product->formatted_price }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Full Description -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Description</h3>
                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <a href="{{ route('store.show', $relatedProduct->slug) }}" class="group">
                            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                @if($relatedProduct->image_path)
                                    <img src="{{ asset('storage/' . $relatedProduct->image_path) }}" alt="{{ $relatedProduct->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $relatedProduct->title }}</h4>
                                    <div class="flex items-center justify-between">
                                        <span class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $relatedProduct->formatted_price }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-public-layout>
