<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.parties.addons.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Add-on: {{ $addon->name }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.parties.addons.update', $addon) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $addon->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" id="description" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $addon->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (cents) *</label>
                        <input type="number" name="price_cents" id="price_cents" value="{{ old('price_cents', $addon->price_cents) }}" required min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500">Enter in cents (e.g., 500 = $5.00)</p>
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_per_person" value="1" {{ old('is_per_person', $addon->is_per_person) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Price per person</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $addon->sort_order) }}" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex items-center pt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $addon->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.parties.addons.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Update Add-on</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
