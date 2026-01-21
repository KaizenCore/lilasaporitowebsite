<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.parties.paintings.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Painting: {{ $painting->title }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.parties.paintings.update', $painting) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $painting->title) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $painting->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Image</label>
                        <img src="{{ asset('storage/' . $painting->image_path) }}" alt="{{ $painting->title }}" class="w-48 h-32 object-cover rounded mb-2">
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Replace Image</label>
                        <input type="file" name="image" id="image" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty Level *</label>
                            <select name="difficulty_level" id="difficulty_level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="beginner" {{ old('difficulty_level', $painting->difficulty_level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('difficulty_level', $painting->difficulty_level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('difficulty_level', $painting->difficulty_level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (minutes) *</label>
                            <input type="number" name="estimated_duration_minutes" id="estimated_duration_minutes" value="{{ old('estimated_duration_minutes', $painting->estimated_duration_minutes) }}" required min="30" max="480"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $painting->sort_order) }}" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="flex items-center pt-6">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $painting->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (visible to customers)</label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.parties.paintings.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Update Painting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
