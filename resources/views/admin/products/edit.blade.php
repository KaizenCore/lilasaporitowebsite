<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Product: {{ $product->title }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" x-data="productForm()">
                    @csrf
                    @method('PUT')

                    <!-- Basic Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Title *</label>
                                <input type="text" name="title" id="title" value="{{ old('title', $product->title) }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <select name="category_id" id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">Uncategorized</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('sku') border-red-500 @enderror">
                                @error('sku')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Short Description</label>
                            <input type="text" name="short_description" id="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('short_description') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Brief description shown in product listings (max 500 characters)</p>
                            @error('short_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Description *</label>
                            <textarea name="description" id="description" rows="6" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Product Type & Pricing -->
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Type & Pricing</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product Type *</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'physical' ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" name="product_type" value="physical" x-model="productType" class="sr-only" required>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Physical Product</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Art supplies, prints, etc.</div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'digital' ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" name="product_type" value="digital" x-model="productType" class="sr-only">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Digital Product</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">PDFs, videos, tutorials</div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none" :class="productType === 'class_package' ? 'border-purple-600 bg-purple-50 dark:bg-purple-900/30' : 'border-gray-300 dark:border-gray-600'">
                                    <input type="radio" name="product_type" value="class_package" x-model="productType" class="sr-only">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">Class Package</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Multi-class bundles</div>
                                    </div>
                                </label>
                            </div>
                            @error('product_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (in cents) *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="price_cents" id="price_cents" value="{{ old('price_cents', $product->price_cents) }}" min="0" required
                                        class="pl-7 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('price_cents') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter price in cents (e.g., 2500 for $25.00)</p>
                                @error('price_cents')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="compare_at_price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Compare At Price (in cents)</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" name="compare_at_price_cents" id="compare_at_price_cents" value="{{ old('compare_at_price_cents', $product->compare_at_price_cents) }}" min="0"
                                        class="pl-7 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('compare_at_price_cents') border-red-500 @enderror">
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For showing sale pricing (optional)</p>
                                @error('compare_at_price_cents')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Physical Product Fields -->
                    <div x-show="productType === 'physical'" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Physical Product Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Quantity</label>
                                <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('stock_quantity') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave blank for unlimited stock</p>
                                @error('stock_quantity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="weight_grams" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (grams)</label>
                                <input type="number" name="weight_grams" id="weight_grams" value="{{ old('weight_grams', $product->weight_grams) }}" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('weight_grams') border-red-500 @enderror">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">For shipping calculations</p>
                                @error('weight_grams')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="requires_shipping" value="1" {{ old('requires_shipping', $product->requires_shipping) ? 'checked' : '' }}
                                    class="rounded border-gray-300 dark:border-gray-600 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">This product requires shipping</span>
                            </label>
                        </div>
                    </div>

                    <!-- Digital Product Fields -->
                    <div x-show="productType === 'digital'" class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Digital Product Details</h3>

                        @if($product->product_type === 'digital' && $product->digital_file_path)
                            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-md">
                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                    <strong>Current file:</strong> {{ basename($product->digital_file_path) }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <label for="digital_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Digital File</label>
                            <input type="file" name="digital_file" id="digital_file"
                                class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900 @error('digital_file') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a new file to replace the current one. Maximum file size: 50MB</p>
                            @error('digital_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Images</h3>

                        <!-- Main Image -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Main Product Image</label>
                            @if($product->image_path)
                                <div class="mb-4">
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->title }}" class="h-48 w-48 object-cover rounded shadow">
                                </div>
                            @endif
                            <input type="file" name="image" id="image" accept="image/*"
                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900 @error('image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload a new image to replace the current one. Max 2MB.</p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gallery Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gallery Images</label>

                            @php
                                $galleryImages = $product->gallery_images ? json_decode($product->gallery_images, true) : [];
                            @endphp

                            @if(count($galleryImages) > 0)
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    @foreach($galleryImages as $image)
                                        <div class="relative group">
                                            <img src="{{ Storage::url($image) }}" alt="Gallery image" class="h-32 w-full object-cover rounded shadow">
                                            <label class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer">
                                                <input type="checkbox" name="remove_gallery_images[]" value="{{ $image }}" class="sr-only peer">
                                                <span class="text-white text-sm peer-checked:text-red-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Hover and click to mark images for removal.</p>
                            @endif

                            <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple
                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900 @error('gallery_images.*') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add more gallery images. Max 2MB each.</p>
                            @error('gallery_images.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Publishing Options -->
                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Publishing Options</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                                <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $product->status) == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center pt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Feature this product on homepage</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                            Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function productForm() {
            return {
                productType: '{{ old('product_type', $product->product_type) }}'
            }
        }
    </script>
</x-admin-layout>
