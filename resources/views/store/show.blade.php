<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Product Image -->
                    <div>
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-96 object-cover rounded-lg">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded-lg">
                                <span class="text-gray-400 text-lg">No Image Available</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div>
                        <div class="mb-4">
                            @if($product->category)
                                <a href="{{ route('store.category', $product->category->slug) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    {{ $product->category->name }}
                                </a>
                            @endif
                        </div>

                        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->title }}</h1>

                        @if($product->sku)
                            <p class="text-sm text-gray-500 mb-4">SKU: {{ $product->sku }}</p>
                        @endif

                        <!-- Price -->
                        <div class="mb-6">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                                @if($product->is_on_sale)
                                    <span class="text-xl text-gray-500 line-through">${{ number_format($product->compare_at_price_cents / 100, 2) }}</span>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded-full">
                                        Save {{ $product->sale_percentage }}%
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Product Type -->
                        <div class="mb-6">
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full
                                {{ $product->product_type === 'physical' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $product->product_type === 'digital' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $product->product_type === 'class_package' ? 'bg-purple-100 text-purple-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
                            </span>
                            @if($product->is_featured)
                                <span class="inline-block px-3 py-1 text-sm font-semibold bg-yellow-100 text-yellow-800 rounded-full ml-2">
                                    Featured
                                </span>
                            @endif
                        </div>

                        <!-- Stock Status -->
                        @if($product->product_type === 'physical')
                            <div class="mb-6">
                                @if($product->is_out_of_stock)
                                    <span class="text-red-600 font-semibold">Out of Stock</span>
                                @elseif(is_null($product->stock_quantity))
                                    <span class="text-green-600">In Stock</span>
                                @elseif($product->stock_quantity < 10)
                                    <span class="text-orange-600">Only {{ $product->stock_quantity }} left in stock!</span>
                                @else
                                    <span class="text-green-600">In Stock</span>
                                @endif
                            </div>
                        @endif

                        <!-- Short Description -->
                        @if($product->short_description)
                            <p class="text-lg text-gray-700 mb-6">{{ $product->short_description }}</p>
                        @endif

                        <!-- Add to Cart Button -->
                        <div class="mb-6">
                            @if($product->is_out_of_stock)
                                <button disabled class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg font-semibold cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @else
                                <form action="{{ route('cart.add') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    @if($product->product_type === 'physical' && !is_null($product->stock_quantity))
                                        <div>
                                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                            <select name="quantity" id="quantity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                @for($i = 1; $i <= min(10, $product->stock_quantity); $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    @endif

                                    <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                                        Add to Cart - {{ $product->formatted_price }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Full Description -->
                <div class="border-t border-gray-200 p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Description</h3>
                    <div class="prose max-w-none">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $relatedProduct)
                            <a href="{{ route('store.show', $relatedProduct->slug) }}" class="group">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                    @if($relatedProduct->image_path)
                                        <img src="{{ asset('storage/' . $relatedProduct->image_path) }}" alt="{{ $relatedProduct->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">No Image</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $relatedProduct->title }}</h4>
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-indigo-600">{{ $relatedProduct->formatted_price }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
