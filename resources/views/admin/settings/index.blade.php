<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Site Settings - About Page
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 dark:bg-green-900/50 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Hero Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Hero Section
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="about_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                                <input type="text" name="about_title" id="about_title" value="{{ old('about_title', $settings['about_title']) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">e.g., "Hello, I'm Lila!"</p>
                            </div>

                            <div>
                                <label for="about_subtitle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtitle</label>
                                <input type="text" name="about_subtitle" id="about_subtitle" value="{{ old('about_subtitle', $settings['about_subtitle']) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">e.g., "Artist, Teacher, Creative Spirit"</p>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Section -->
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Profile Photo
                        </h3>

                        <div class="flex items-start gap-6">
                            @if($settings['about_photo'])
                                <div class="flex-shrink-0">
                                    <img src="{{ Storage::url($settings['about_photo']) }}" alt="About photo" class="w-32 h-32 object-cover rounded-lg shadow-md">
                                    <button type="submit" form="delete-photo-form" class="mt-2 text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" onclick="return confirm('Delete this photo?')">
                                        Remove photo
                                    </button>
                                </div>
                            @else
                                <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="flex-1">
                                <label for="about_photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload New Photo</label>
                                <input type="file" name="about_photo" id="about_photo" accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 dark:file:bg-purple-900/50 file:text-purple-700 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900">
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max 5MB (jpeg, png, jpg, gif)</p>
                                @error('about_photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Bio Section -->
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Bio / About Me
                        </h3>

                        <div>
                            <label for="about_bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Bio</label>
                            <textarea name="about_bio" id="about_bio" rows="8"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="Tell visitors about yourself, your background, and your passion for art...">{{ old('about_bio', $settings['about_bio']) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use separate paragraphs for different sections. Each paragraph will be styled automatically.</p>
                        </div>
                    </div>

                    <!-- Why Take Classes Section -->
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Why Take Classes With Me?
                        </h3>

                        <div>
                            <label for="why_take_classes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Class Benefits</label>
                            <textarea name="why_take_classes" id="why_take_classes" rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="List the benefits of taking classes with you...">{{ old('why_take_classes', $settings['why_take_classes']) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Each line will become a bullet point.</p>
                        </div>
                    </div>

                    <!-- Teaching Philosophy Section -->
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Teaching Philosophy
                        </h3>

                        <div>
                            <label for="teaching_philosophy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Teaching Philosophy</label>
                            <textarea name="teaching_philosophy" id="teaching_philosophy" rows="6"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="Describe your teaching philosophy and approach...">{{ old('teaching_philosophy', $settings['teaching_philosophy']) }}</textarea>
                        </div>
                    </div>

                    <!-- Cancellation Policy Section -->
                    <div class="mb-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Cancellation Policy
                        </h3>

                        <div>
                            <label for="cancellation_policy" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Cancellation & Refund Policy</label>
                            <textarea name="cancellation_policy" id="cancellation_policy" rows="8"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500"
                                placeholder="Describe your cancellation and refund policy for classes...">{{ old('cancellation_policy', $settings['cancellation_policy']) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This will be displayed on the public Policy page. Use separate paragraphs for different sections.</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                            Save Changes
                        </button>
                    </div>
                </form>

                @if($settings['about_photo'])
                    <form id="delete-photo-form" action="{{ route('admin.settings.delete-photo') }}" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
