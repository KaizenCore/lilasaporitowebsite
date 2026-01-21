<x-public-layout>
    <x-slot name="title">Book a Private Party</x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Host Your Own Paint Party!</h1>
            <p class="text-xl md:text-2xl text-purple-100 max-w-3xl mx-auto mb-8">
                Birthday parties, corporate events, bridal showers, and more. Let's create something amazing together!
            </p>
            <a href="{{ route('parties.inquire') }}" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-purple-50 transition shadow-lg">
                Request a Quote
            </a>
        </div>
    </div>

    <!-- How It Works -->
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">1</span>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Submit Inquiry</h3>
                    <p class="text-gray-600">Tell us about your event, preferred date, and guest count.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">2</span>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Choose a Painting</h3>
                    <p class="text-gray-600">Browse our gallery or request a custom design.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Get Your Quote</h3>
                    <p class="text-gray-600">We'll send you a personalized quote within 24-48 hours.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-2xl font-bold text-purple-600">4</span>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">Party Time!</h3>
                    <p class="text-gray-600">Confirm your booking and get ready for a great time!</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Types -->
    <div class="py-16 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Perfect For Any Occasion</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach([
                    ['name' => 'Birthday Parties', 'icon' => '🎂'],
                    ['name' => 'Corporate Events', 'icon' => '🏢'],
                    ['name' => 'Bridal Showers', 'icon' => '💒'],
                    ['name' => 'Bachelorette', 'icon' => '🥂'],
                    ['name' => 'Team Building', 'icon' => '🤝'],
                    ['name' => 'Custom Events', 'icon' => '🎨'],
                ] as $event)
                    <div class="bg-white rounded-lg p-6 text-center shadow-sm hover:shadow-md transition">
                        <div class="text-4xl mb-2">{{ $event['icon'] }}</div>
                        <div class="font-medium text-gray-900">{{ $event['name'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Paintings Preview -->
    @if($paintings->count() > 0)
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Popular Paintings</h2>
                <a href="{{ route('parties.paintings') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                    View All &rarr;
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($paintings as $painting)
                    <div class="group">
                        <div class="aspect-square rounded-lg overflow-hidden shadow-md">
                            <img src="{{ asset('storage/' . $painting->image_path) }}" alt="{{ $painting->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition">
                        </div>
                        <p class="mt-2 text-sm font-medium text-gray-900 truncate">{{ $painting->title }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Location Options -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Choose Your Venue</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="text-4xl mb-4">🏠</div>
                    <h3 class="text-xl font-bold mb-2">At Lila's Studio</h3>
                    <p class="text-gray-600 mb-4">Perfect for smaller gatherings up to 8 guests. All supplies provided in a creative, inspiring space.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>Up to 8 guests</li>
                        <li>All materials included</li>
                        <li>Cozy, creative atmosphere</li>
                    </ul>
                </div>
                <div class="bg-white rounded-xl p-8 shadow-lg">
                    <div class="text-4xl mb-4">📍</div>
                    <h3 class="text-xl font-bold mb-2">At Your Location</h3>
                    <p class="text-gray-600 mb-4">We come to you! Perfect for larger groups or when you want the party at your preferred venue.</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li>No guest limit</li>
                        <li>We bring all supplies</li>
                        <li>Your space, your rules</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Info -->
    @if($defaultPricing)
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Pricing</h2>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                @if($defaultPricing->pricing_type === 'flat_per_person')
                    Starting at <span class="font-bold text-purple-600">{{ $defaultPricing->formatted_base_price }}</span> per person
                    (minimum {{ $defaultPricing->minimum_guests }} guests)
                @else
                    Custom pricing based on your event needs
                @endif
            </p>
            <a href="{{ route('parties.inquire') }}" class="inline-block bg-purple-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-purple-700 transition">
                Get Your Custom Quote
            </a>
        </div>
    </div>
    @endif

    <!-- CTA -->
    <div class="py-16 bg-gradient-to-r from-purple-600 to-pink-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Plan Your Party?</h2>
            <p class="text-xl text-purple-100 mb-8">Submit your inquiry today and let's create something amazing together!</p>
            <a href="{{ route('parties.inquire') }}" class="inline-block bg-white text-purple-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-purple-50 transition shadow-lg">
                Start Your Inquiry
            </a>
        </div>
    </div>
</x-public-layout>
