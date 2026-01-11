<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create New Art Class
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Short Description -->
                    <div class="mb-4">
                        <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Short Description</label>
                        <input type="text" name="short_description" id="short_description" value="{{ old('short_description') }}" maxlength="500"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('short_description') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Brief one-liner about the class (max 500 characters)</p>
                        @error('short_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Description *</label>
                        <textarea name="description" id="description" rows="5" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Materials Included -->
                    <div class="mb-4">
                        <label for="materials_included" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Materials Included</label>
                        <textarea name="materials_included" id="materials_included" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('materials_included') border-red-500 @enderror">{{ old('materials_included') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">List what materials are provided</p>
                        @error('materials_included')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Main Image -->
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Main Class Image</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900 @error('image') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">JPG, PNG, or GIF (max 2MB)</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gallery Images -->
                    <div class="mb-4">
                        <label for="gallery_images" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gallery Images</label>
                        <input type="file" name="gallery_images[]" id="gallery_images" accept="image/*" multiple
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900 @error('gallery_images.*') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select multiple images to create a gallery (max 2MB each)</p>
                        @error('gallery_images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Class Date -->
                        <div>
                            <label for="class_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class Date & Time *</label>
                            <input type="datetime-local" name="class_date" id="class_date" value="{{ old('class_date') }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('class_date') border-red-500 @enderror">
                            @error('class_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (minutes) *</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 120) }}" min="30" max="480" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('duration_minutes') border-red-500 @enderror">
                            @error('duration_minutes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Address *</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}" required placeholder="123 Main St, Brooklyn, NY 11211"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('location') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Full address - only shown in booking confirmation emails</p>
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Public Location -->
                    <div class="mb-4">
                        <label for="location_public" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Public Display Location</label>
                        <input type="text" name="location_public" id="location_public" value="{{ old('location_public') }}" placeholder="Williamsburg, Brooklyn"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('location_public') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">General area shown on the website (e.g., "Williamsburg, Brooklyn"). Leave blank to use full address.</p>
                        @error('location_public')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Price -->
                        <div>
                            <label for="price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (cents) *</label>
                            <input type="number" name="price_cents" id="price_cents" value="{{ old('price_cents', 5000) }}" min="0" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('price_cents') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter in cents (e.g., 5000 = $50.00)</p>
                            @error('price_cents')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity *</label>
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 10) }}" min="1" max="100" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('capacity') border-red-500 @enderror">
                            @error('capacity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status *</label>
                            <select name="status" id="status" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 @error('status') border-red-500 @enderror">
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Party Event Section -->
                    <div class="mb-6 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg" x-data="{ isParty: {{ old('is_party_event') ? 'true' : 'false' }} }">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" name="is_party_event" id="is_party_event" value="1" x-model="isParty"
                                {{ old('is_party_event') ? 'checked' : '' }}
                                class="rounded border-gray-300 dark:border-gray-600 text-purple-600 focus:ring-purple-500">
                            <label for="is_party_event" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                This is a Party/Event (tiered pricing)
                            </label>
                        </div>

                        <div x-show="isParty" x-cloak class="space-y-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Set up package pricing for birthday parties and events. The regular price above will be ignored for party events.
                            </p>

                            <!-- Small Party -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="small_party_price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Small Party Price (cents)</label>
                                    <input type="number" name="small_party_price_cents" id="small_party_price_cents" value="{{ old('small_party_price_cents', 15000) }}" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">e.g., 15000 = $150.00</p>
                                </div>
                                <div>
                                    <label for="small_party_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kids Included in Small</label>
                                    <input type="number" name="small_party_size" id="small_party_size" value="{{ old('small_party_size', 6) }}" min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>

                            <!-- Large Party -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="large_party_price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Large Party Price (cents)</label>
                                    <input type="number" name="large_party_price_cents" id="large_party_price_cents" value="{{ old('large_party_price_cents', 25000) }}" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">e.g., 25000 = $250.00</p>
                                </div>
                                <div>
                                    <label for="large_party_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kids Included in Large</label>
                                    <input type="number" name="large_party_size" id="large_party_size" value="{{ old('large_party_size', 12) }}" min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>

                            <!-- Additional & Max -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="additional_guest_price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Additional Kid Price (cents)</label>
                                    <input type="number" name="additional_guest_price_cents" id="additional_guest_price_cents" value="{{ old('additional_guest_price_cents', 2000) }}" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">e.g., 2000 = $20.00 per extra kid</p>
                                </div>
                                <div>
                                    <label for="max_party_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Kids</label>
                                    <input type="number" name="max_party_size" id="max_party_size" value="{{ old('max_party_size', 20) }}" min="1"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>

                            <!-- Party Add-ons -->
                            <div class="mt-6 pt-6 border-t border-purple-200 dark:border-purple-700" x-data="{
                                addons: {{ json_encode(old('party_addons', [])) ?: '[]' }},
                                addAddon() {
                                    this.addons.push({ name: '', price_cents: 300, description: '' });
                                },
                                removeAddon(index) {
                                    this.addons.splice(index, 1);
                                }
                            }">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Optional Add-ons (per kid)</label>
                                    <button type="button" @click="addAddon()" class="text-sm bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-md">
                                        + Add Option
                                    </button>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Create optional extras customers can add to their party (e.g., goody bags, pizza, custom shirts)</p>

                                <template x-for="(addon, index) in addons" :key="index">
                                    <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-3">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="'Add-on #' + (index + 1)"></span>
                                            <button type="button" @click="removeAddon(index)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Name</label>
                                                <input type="text" x-model="addon.name" :name="'party_addons[' + index + '][name]'" placeholder="Goody Bag"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Price (cents per kid)</label>
                                                <input type="number" x-model.number="addon.price_cents" :name="'party_addons[' + index + '][price_cents]'" min="0" placeholder="300"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Description (optional)</label>
                                                <input type="text" x-model="addon.description" :name="'party_addons[' + index + '][description]'" placeholder="Art supplies loot bag"
                                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-2" x-show="addon.price_cents > 0">
                                            = $<span x-text="(addon.price_cents / 100).toFixed(2)"></span> per kid
                                        </p>
                                    </div>
                                </template>

                                <div x-show="addons.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic">
                                    No add-ons configured. Click "+ Add Option" to create one.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end space-x-4 mt-6">
                        <a href="{{ route('admin.classes.index') }}" class="bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Create Class
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
