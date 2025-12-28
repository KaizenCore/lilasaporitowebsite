<x-public-layout>
    <x-slot name="title">Store - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Shop</h1>
            <p class="text-xl text-purple-100">Art supplies, prints, and creative goodies</p>
        </div>
    </section>

    <div class="py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Featured Products -->
            @if($featuredProducts->count() > 0 && !request()->filled('search') && !request()->filled('category'))
                <div class="mb-12">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Featured Products</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($featuredProducts as $product)
                            <a href="{{ route('store.show', $product->slug) }}" class="group">
                                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                    @if($product->image_path)
                                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                    @else
                                        <div class="w-full h-48 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $product->title }}</h4>
                                        @if($product->short_description)
                                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product->short_description, 60) }}</p>
                                        @endif
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-lg font-bold text-purple-600">{{ $product->formatted_price }}</span>
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
            <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-6 mb-8">
                <form method="GET" action="{{ route('store.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <input type="text" name="search" placeholder="Search products..." value="{{ request('search') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <select name="category" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">All Types</option>
                            <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical Products</option>
                            <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital Products</option>
                            <option value="class_package" {{ request('type') == 'class_package' ? 'selected' : '' }}>Class Packages</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            Filter
                        </button>
                        <a href="{{ route('store.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-purple-200 to-pink-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div class="p-4">
                                    @if($product->is_featured)
                                        <span class="inline-block px-2 py-1 text-xs font-semibold text-pink-800 bg-pink-100 rounded mb-2">Featured</span>
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
                                            <span class="text-lg font-bold text-purple-600">{{ $product->formatted_price }}</span>
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
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                    <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">No products found.</p>
                    @if(request()->filled('search') || request()->filled('category') || request()->filled('type'))
                        <a href="{{ route('store.index') }}" class="mt-4 inline-block text-purple-600 hover:text-purple-800 font-semibold">
                            Clear filters and view all products
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
