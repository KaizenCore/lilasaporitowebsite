<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New Product
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
                    @csrf

                    <!-- Basic Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">Product Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">Uncategorized</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('sku') border-red-500 @enderror">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="short_description" class="block text-sm font-medium text-gray-700">Short Description</label>
                            <input type="text" name="short_description" id="short_description" value="{{ old('short_description') }}" maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('short_description') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Brief description shown in product listings (max 500 characters)</p>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">Full Description *</label>
                            <textarea name="description" id="description" rows="6" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Type & Pricing -->
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Product Type & Pricing</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product Type *</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'physical' ? 'border-purple-600 bg-purple-50' : 'border-gray-300'">
                                    <input type="radio" name="product_type" value="physical" x-model="productType" class="sr-only" required>
                                    <div>
                                        <div class="font-medium">Physical Product</div>
                                        <div class="text-sm text-gray-500">Art supplies, prints, etc.</div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'digital' ? 'border-purple-600 bg-purple-50' : 'border-gray-300'">
                                    <input type="radio" name="product_type" value="digital" x-model="productType" class="sr-only">
                                    <div>
                                        <div class="font-medium">Digital Product</div>
                                        <div class="text-sm text-gray-500">PDFs, videos, tutorials</div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'class_package' ? 'border-purple-600 bg-purple-50' : 'border-gray-300'">
                                    <input type="radio" name="product_type" value="class_package" x-model="productType" class="sr-only">
                                    <div>
                                        <div class="font-medium">Class Package</div>
                                        <div class="text-sm text-gray-500">Multi-class bundles</div>
                                    </div>
                                </label>
                            </div>
                            @error('product_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="price_cents" class="block text-sm font-medium text-gray-700">Price (in cents) *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price_cents" id="price_cents" value="{{ old('price_cents') }}" min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('price_cents') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Enter price in cents (e.g., 2500 for $25.00)</p>
                                @error('price_cents')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="compare_at_price_cents" class="block text-sm font-medium text-gray-700">Compare At Price (in cents)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="compare_at_price_cents" id="compare_at_price_cents" value="{{ old('compare_at_price_cents') }}" min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('compare_at_price_cents') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">For showing sale pricing (optional)</p>
                                @error('compare_at_price_cents')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Physical Product Fields -->
                    <div x-show="productType === 'physical'" class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Physical Product Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Stock Quantity</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') }}" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('stock_quantity') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">Leave blank for unlimited stock</p>
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weight_grams" class="block text-sm font-medium text-gray-700">Weight (grams)</label>
                                <input type="number" name="weight_grams" id="weight_grams" value="{{ old('weight_grams') }}" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('weight_grams') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500">For shipping calculations</p>
                                @error('weight_grams')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="requires_shipping" value="1" {{ old('requires_shipping', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700">This product requires shipping</span>
                            </label>
                        </div>
                    </div>

                    <!-- Digital Product Fields -->
                    <div x-show="productType === 'digital'" class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Digital Product Details</h3>

                        <div>
                            <label for="digital_file" class="block text-sm font-medium text-gray-700">Digital File *</label>
                            <input type="file" name="digital_file" id="digital_file"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 @error('digital_file') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Maximum file size: 50MB</p>
                            @error('digital_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Product Image</h3>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Product Image</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 @error('image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Maximum file size: 2MB (jpeg, png, jpg, gif)</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing Options</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center pt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <span class="ml-2 text-sm text-gray-700">Feature this product on homepage</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 border-t pt-6">
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function productForm() {
            return {
                productType: '{{ old('product_type', 'physical') }}'
            }
        }
    </script>
</x-admin-layout>
