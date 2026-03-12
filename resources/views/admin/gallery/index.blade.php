<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gallery Photos
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <p class="text-gray-600 dark:text-gray-400 mb-6">Upload photos from past classes. These will appear on the public Gallery page.</p>

        @forelse($classes as $class)
            @php
                $galleryImages = $class->gallery_images ? json_decode($class->gallery_images, true) : [];
            @endphp
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg mb-6 overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if($class->image_path)
                            <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $class->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $class->class_date->format('M j, Y') }} &middot; {{ count($galleryImages) }} photo{{ count($galleryImages) !== 1 ? 's' : '' }}</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.gallery.upload', $class) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2" x-data="{ files: null }">
                        @csrf
                        <label class="cursor-pointer bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-3 py-1.5 rounded-md flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Photos
                            <input type="file" name="gallery_images[]" multiple accept="image/*" class="hidden" @change="files = $event.target.files; if(files.length) $el.closest('form').submit()">
                        </label>
                    </form>
                </div>

                @if(count($galleryImages) > 0)
                    <div class="p-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        @foreach($galleryImages as $image)
                            <div class="relative group">
                                <img src="{{ Storage::url($image) }}" alt="Gallery photo" class="w-full h-24 object-cover rounded shadow-sm">
                                <form action="{{ route('admin.gallery.remove', $class) }}" method="POST" class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="image_path" value="{{ $image }}">
                                    <button type="submit" onclick="return confirm('Remove this photo?')" class="bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow">
                                        &times;
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 text-center">
                <p class="text-gray-500 dark:text-gray-400">No past classes yet. Gallery photos can be added once a class date has passed.</p>
            </div>
        @endforelse
    </div>
</x-admin-layout>
