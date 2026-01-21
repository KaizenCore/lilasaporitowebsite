<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Party Paintings Gallery
            </h2>
            <a href="{{ route('admin.parties.paintings.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                Add New Painting
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($paintings->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($paintings as $painting)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden shadow">
                                <div class="aspect-w-16 aspect-h-12">
                                    <img src="{{ asset('storage/' . $painting->image_path) }}" alt="{{ $painting->title }}" class="w-full h-48 object-cover">
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $painting->title }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $painting->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $painting->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ Str::limit($painting->description, 80) }}</p>
                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                        <span class="px-2 py-1 rounded bg-{{ $painting->difficulty_badge_color }}-100 text-{{ $painting->difficulty_badge_color }}-800">
                                            {{ ucfirst($painting->difficulty_level) }}
                                        </span>
                                        <span>{{ $painting->formatted_duration }}</span>
                                        <span>{{ $painting->party_bookings_count ?? 0 }} bookings</span>
                                    </div>
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.parties.paintings.edit', $painting) }}" class="text-purple-600 hover:text-purple-800 text-sm">Edit</a>
                                        <form action="{{ route('admin.parties.paintings.destroy', $painting) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $paintings->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 text-lg mb-4">No paintings added yet.</p>
                        <a href="{{ route('admin.parties.paintings.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                            Add Your First Painting
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
