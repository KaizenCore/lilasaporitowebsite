<x-public-layout>
    <x-slot name="title">{{ $class->title }} - FrizzBoss</x-slot>

    <!-- Back Button -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <a href="{{ route('classes.index') }}" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Classes
        </a>
    </div>

    <!-- Class Details -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Image Section -->
            <div x-data="{
                activeImage: '{{ $class->image_path ? Storage::url($class->image_path) : '' }}',
                showLightbox: false,
                lightboxImage: ''
            }">
                <!-- Main Image -->
                <div class="rounded-2xl overflow-hidden shadow-2xl cursor-pointer" @click="if(activeImage) { lightboxImage = activeImage; showLightbox = true; }">
                    @if($class->image_path)
                    <img :src="activeImage" alt="{{ $class->title }}" class="w-full h-auto object-cover">
                    @else
                    <div class="bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 aspect-square flex items-center justify-center">
                        <svg class="w-32 h-32 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    @endif
                </div>

                <!-- Gallery Thumbnails -->
                @php
                    $galleryImages = $class->gallery_images ? json_decode($class->gallery_images, true) : [];
                @endphp

                @if($class->image_path || count($galleryImages) > 0)
                <div class="mt-4 grid grid-cols-4 gap-2">
                    @if($class->image_path)
                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 transition-all"
                         :class="activeImage === '{{ Storage::url($class->image_path) }}' ? 'border-purple-500' : 'border-transparent hover:border-purple-300'"
                         @click="activeImage = '{{ Storage::url($class->image_path) }}'">
                        <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-full h-20 object-cover">
                    </div>
                    @endif

                    @foreach($galleryImages as $image)
                    <div class="cursor-pointer rounded-lg overflow-hidden border-2 transition-all"
                         :class="activeImage === '{{ Storage::url($image) }}' ? 'border-purple-500' : 'border-transparent hover:border-purple-300'"
                         @click="activeImage = '{{ Storage::url($image) }}'">
                        <img src="{{ Storage::url($image) }}" alt="Gallery image" class="w-full h-20 object-cover">
                    </div>
                    @endforeach
                </div>
                @endif

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
                    <img :src="lightboxImage" class="max-w-full max-h-full object-contain rounded-lg" @click.stop>
                </div>
            </div>

            <!-- Details Section -->
            <div>
                @if($class->is_full)
                <span class="inline-block bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    SOLD OUT
                </span>
                @elseif($class->spots_available <= 3)
                <span class="inline-block bg-orange-100 dark:bg-orange-900/50 text-orange-800 dark:text-orange-300 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    Only {{ $class->spots_available }} spots left!
                </span>
                @else
                <span class="inline-block bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 px-4 py-2 rounded-full text-sm font-semibold mb-4">
                    {{ $class->spots_available }} spots available
                </span>
                @endif

                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">{{ $class->title }}</h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 mb-8">{{ $class->short_description }}</p>

                <!-- Key Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8">
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Date & Time</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $class->class_date->format('l, F j, Y') }}</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $class->class_date->format('g:i A') }} - {{ $class->class_date->addMinutes($class->duration_minutes)->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Location</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $class->display_location }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Full address provided after booking</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Duration</p>
                                <p class="text-gray-600 dark:text-gray-400">{{ $class->duration_minutes }} minutes</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">Class Size</p>
                                <p class="text-gray-600 dark:text-gray-400">Maximum {{ $class->capacity }} students</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price and Book Button -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-xl p-8 text-white">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-purple-100 mb-1">Price per person</p>
                            <p class="text-5xl font-bold">{{ $class->formatted_price }}</p>
                        </div>
                    </div>

                    @if($class->is_full)
                    <button disabled class="w-full bg-gray-400 text-white px-8 py-4 rounded-lg text-lg font-semibold cursor-not-allowed">
                        Class is Full
                    </button>
                    @else
                    @auth
                    <div class="space-y-3" x-data="{ adding: false, added: false, error: '' }">
                        <a href="{{ route('checkout.show', $class->slug) }}" class="block w-full bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition text-center shadow-lg">
                            Book Your Spot Now
                        </a>
                        <button
                            x-show="!added"
                            @click="
                                adding = true;
                                error = '';
                                fetch('{{ route('class-cart.add') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ art_class_id: {{ $class->id }} })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    adding = false;
                                    if (data.success) {
                                        added = true;
                                    } else {
                                        error = data.message || 'Unable to add to cart';
                                    }
                                })
                                .catch(() => {
                                    adding = false;
                                    error = 'Unable to add to cart';
                                });
                            "
                            :disabled="adding"
                            class="w-full bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white/10 transition text-center disabled:opacity-50"
                        >
                            <span x-show="!adding">Add to Cart</span>
                            <span x-show="adding" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Adding...
                            </span>
                        </button>
                        <template x-if="added">
                            <div class="text-center space-y-2">
                                <p class="text-white flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Added to cart!
                                </p>
                                <a href="{{ route('class-cart.index') }}" class="inline-block text-purple-200 hover:text-white underline text-sm">
                                    View Cart
                                </a>
                            </div>
                        </template>
                        <p x-show="error" x-text="error" class="text-red-200 text-sm text-center"></p>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="block w-full bg-white text-purple-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition text-center shadow-lg">
                        Login to Book
                    </a>
                    @endauth
                    @endif

                    <p class="text-sm text-purple-100 mt-4 text-center">All materials included</p>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-16">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">About This Class</h2>
                <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                    {!! nl2br(e($class->description)) !!}
                </div>

                @if($class->materials_included)
                <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">What's Included</h3>
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-600 dark:text-gray-400">
                        {!! nl2br(e($class->materials_included)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Related Classes -->
        @if($relatedClasses->count() > 0)
        <div class="mt-16">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Other Classes You Might Like</h2>
            <div class="grid md:grid-cols-3 gap-8">
                @foreach($relatedClasses as $relatedClass)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    @if($relatedClass->image_path)
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 relative overflow-hidden">
                        <img src="{{ Storage::url($relatedClass->image_path) }}" alt="{{ $relatedClass->title }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="h-48 bg-gradient-to-br from-purple-200 to-pink-200 dark:from-purple-900 dark:to-pink-900 flex items-center justify-center">
                        <svg class="w-16 h-16 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                    </div>
                    @endif

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $relatedClass->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $relatedClass->short_description }}</p>

                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $relatedClass->class_date->format('M d, Y') }}
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $relatedClass->formatted_price }}</span>
                            <a href="{{ route('classes.show', $relatedClass->slug) }}" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition font-semibold">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>
</x-public-layout>
