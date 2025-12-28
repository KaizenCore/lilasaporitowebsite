<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Products
            </h2>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                Create New Product
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" placeholder="Search by title or SKU..." value="{{ request('search') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                </div>
                <div>
                    <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Types</option>
                        <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>Physical</option>
                        <option value="digital" {{ request('type') == 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="class_package" {{ request('type') == 'class_package' ? 'selected' : '' }}>Class Package</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Filter
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($products->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($product->image_path)
                                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->title }}" class="h-12 w-12 rounded object-cover mr-3">
                                                @else
                                                    <div class="h-12 w-12 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                        <span class="text-gray-400 text-xs">No img</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->title }}</div>
                                                    @if($product->sku)
                                                        <div class="text-sm text-gray-500">SKU: {{ $product->sku }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $product->category?->name ?? 'Uncategorized' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->formatted_price }}
                                            @if($product->is_on_sale)
                                                <br>
                                                <span class="text-xs text-red-600 line-through">${{ number_format($product->compare_at_price_cents / 100, 2) }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $product->product_type === 'physical' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $product->product_type === 'digital' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $product->product_type === 'class_package' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($product->product_type === 'physical')
                                                @if(is_null($product->stock_quantity))
                                                    <span class="text-green-600">Unlimited</span>
                                                @elseif($product->is_out_of_stock)
                                                    <span class="text-red-600 font-medium">Out of Stock</span>
                                                @else
                                                    {{ $product->stock_quantity }}
                                                @endif
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $product->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $product->status === 'archived' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($product->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="text-purple-600 hover:text-purple-900 mr-3">Edit</a>
                                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 text-lg">No products found.</p>
                        <a href="{{ route('admin.products.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                            Create Your First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
