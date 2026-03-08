<x-public-layout>
    <x-slot name="title">Gallery - FrizzBoss</x-slot>

    <!-- Page Header -->
    <section class="bg-gradient-to-r from-purple-600 to-pink-600 py-16 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Class Gallery</h1>
            <p class="text-xl text-purple-100">A look at our past creative sessions</p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="py-12 px-4" x-data="{ showLightbox: false, lightboxImage: '', lightboxTitle: '' }">
        <div class="max-w-7xl mx-auto">
            @if($classes->count() > 0)
                @foreach($classes as $class)
                    @php
                        $galleryImages = $class->gallery_images ? json_decode($class->gallery_images, true) : [];
                        $allImages = [];
                        if ($class->image_path) {
                            $allImages[] = $class->image_path;
                        }
                        $allImages = array_merge($allImages, $galleryImages);
                    @endphp

                    @if(count($allImages) > 0)
                    <div class="mb-12">
                        <div class="flex items-center gap-4 mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $class->title }}</h2>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $class->class_date->format('F j, Y') }}</span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($allImages as $image)
                            <div class="cursor-pointer rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
                                 @click="lightboxImage = '{{ Storage::url($image) }}'; lightboxTitle = '{{ addslashes($class->title) }}'; showLightbox = true;">
                                <img src="{{ Storage::url($image) }}" alt="{{ $class->title }}" class="w-full h-48 object-cover" loading="lazy">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
                <div class="text-center py-16">
                    <svg class="w-24 h-24 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">Gallery Coming Soon</h3>
                    <p class="text-gray-500 dark:text-gray-400">Photos from past classes will appear here.</p>
                    <a href="{{ route('classes.index') }}" class="mt-6 inline-block bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-semibold">
                        Browse Upcoming Classes
                    </a>
                </div>
            @endif
        </div>

        <!-- Lightbox Modal -->
        <div x-show="showLightbox"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showLightbox = false"
             @keydown.escape.window="showLightbox = false"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
             style="display: none;">
            <button @click="showLightbox = false" class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center" @click.stop>
                <img :src="lightboxImage" class="max-w-full max-h-[80vh] object-contain rounded-lg mx-auto">
                <p class="text-white text-lg mt-4" x-text="lightboxTitle"></p>
            </div>
        </div>
    </section>
</x-public-layout>
