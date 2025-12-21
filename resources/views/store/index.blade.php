<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Store') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Featured Products -->
            @if($featuredProducts->count() > 0 && !request()->filled('search') && !request()->filled('category'))
                <div class="mb-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Featured Products</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredProducts as $product)
                            <a href="{{ route('store.show', $product->slug) }}" class="group">
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-400">No Image</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $product->title }}</h4>
                                        @if($product->short_description)
                                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->short_description, 60) }}</p>
                                        @endif
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-lg font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                                                @if($product->is_on_sale)
                                                    <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->compare_at_price_cents / 100, 2) }}</span>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">
                                                {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" action="{{ route('store.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Types</option>
                            <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical Products</option>
                            <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital Products</option>
                            <option value="class_package" {{ request('type') == 'class_package' ? 'selected' : '' }}>Class Packages</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Filter
                        </button>
                        <a href="{{ route('store.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                    @foreach($products as $product)
                        <a href="{{ route('store.show', $product->slug) }}" class="group">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-400">No Image</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    @if($product->is_featured)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded mb-2">Featured</span>
                                    @endif
                                    <h4 class="font-semibold text-gray-900 mb-2">{{ $product->title }}</h4>
                                    @if($product->category)
                                        <p class="text-xs text-gray-500 mb-2">{{ $product->category->name }}</p>
                                    @endif
                                    @if($product->short_description)
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->short_description, 80) }}</p>
                                    @endif
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-lg font-bold text-indigo-600">{{ $product->formatted_price }}</span>
                                            @if($product->is_on_sale)
                                                <span class="text-sm text-gray-500 line-through ml-2">${{ number_format($product->compare_at_price_cents / 100, 2) }}</span>
                                                <span class="text-xs text-red-600 font-semibold ml-2">-{{ $product->sale_percentage }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">
                                            {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <p class="text-gray-500 text-lg">No products found.</p>
                    @if(request()->filled('search') || request()->filled('category') || request()->filled('type'))
                        <a href="{{ route('store.index') }}" class="mt-4 inline-block text-indigo-600 hover:text-indigo-800">
                            Clear filters and view all products
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
