<x-public-layout>
    <x-slot name="title">Painting Gallery</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Painting Gallery</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Browse our collection of paintings perfect for your party. Can't find what you're looking for? Request a custom design!
                </p>
            </div>

            <!-- Filter -->
            <div class="mb-8">
                <form method="GET" class="flex justify-center gap-4">
                    <a href="{{ route('parties.paintings') }}" class="px-4 py-2 rounded-full {{ !request('difficulty') ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        All
                    </a>
                    @foreach(['beginner', 'intermediate', 'advanced'] as $level)
                        <a href="{{ route('parties.paintings', ['difficulty' => $level]) }}"
                            class="px-4 py-2 rounded-full {{ request('difficulty') === $level ? 'bg-purple-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                            {{ ucfirst($level) }}
                        </a>
                    @endforeach
                </form>
            </div>

            @if($paintings->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($paintings as $painting)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                            <div class="aspect-square">
                                <img src="{{ asset('storage/' . $painting->image_path) }}" alt="{{ $painting->title }}" class="w-full h-full object-cover">
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-1">{{ $painting->title }}</h3>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="px-2 py-1 rounded bg-{{ $painting->difficulty_badge_color }}-100 text-{{ $painting->difficulty_badge_color }}-800">
                                        {{ ucfirst($painting->difficulty_level) }}
                                    </span>
                                    <span class="text-gray-500">{{ $painting->formatted_duration }}</span>
                                </div>
                                @if($painting->description)
                                    <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $painting->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $paintings->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No paintings available yet.</p>
                </div>
            @endif

            <!-- CTA -->
            <div class="mt-12 text-center">
                <p class="text-lg text-gray-600 mb-4">Found something you like?</p>
                <a href="{{ route('parties.inquire') }}" class="inline-block px-8 py-3 bg-purple-600 text-white rounded-full font-bold hover:bg-purple-700 transition">
                    Request a Quote
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
