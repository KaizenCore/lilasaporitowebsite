<x-public-layout>
    <x-slot name="title">Checkout - {{ $class->title }} - FrizzBoss</x-slot>

    <x-slot name="head">
        <script src="https://js.stripe.com/v3/"></script>
    </x-slot>

    <!-- Back Button -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <a href="{{ route('classes.show', $class->slug) }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Class Details
        </a>
    </div>

    <!-- Checkout Form -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Complete Your Booking</h1>
            <p class="text-lg text-gray-600">You're almost there! Just one more step.</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

                    @if($class->image_path)
                    <div class="rounded-lg overflow-hidden mb-6">
                        <img src="{{ Storage::url($class->image_path) }}" alt="{{ $class->title }}" class="w-full h-40 object-cover">
                    </div>
                    @endif

                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $class->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ $class->short_description }}</p>

                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">{{ $class->class_date->format('l, F j, Y') }}</p>
                                <p class="text-gray-600">{{ $class->class_date->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            </svg>
                            <p class="text-gray-600">{{ $class->display_location }}</p>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-purple-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600">{{ $class->duration_minutes }} minutes</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6 space-y-3">
                        @if($class->is_party_event && $partyPackage)
                        <div class="flex justify-between text-gray-600">
                            <span>{{ ucfirst($partyPackage) }} Party Package</span>
                            <span>{{ $partyPackage === 'small' ? $class->formatted_small_party_price : $class->formatted_large_party_price }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Number of Kids</span>
                            <span>{{ $partyGuests }}</span>
                        </div>
                        @php
                            $includedGuests = $partyPackage === 'small' ? ($class->small_party_size ?? 6) : ($class->large_party_size ?? 12);
                            $extraGuests = max(0, $partyGuests - $includedGuests);
                        @endphp
                        @if($extraGuests > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>Extra Kids ({{ $extraGuests }} x {{ $class->formatted_additional_guest_price }})</span>
                            <span>${{ number_format(($extraGuests * $class->additional_guest_price_cents) / 100, 2) }}</span>
                        </div>
                        @endif
                        @if(!empty($selectedAddons) && !empty($class->party_addons))
                            @foreach($selectedAddons as $addonIdx)
                                @if(isset($class->party_addons[$addonIdx]))
                                    @php $addon = $class->party_addons[$addonIdx]; @endphp
                                    <div class="flex justify-between text-gray-600">
                                        <span>{{ $addon['name'] }} ({{ $partyGuests }} x ${{ number_format(($addon['price_cents'] ?? 0) / 100, 2) }})</span>
                                        <span>${{ number_format((($addon['price_cents'] ?? 0) * $partyGuests) / 100, 2) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @else
                        <div class="flex justify-between text-gray-600">
                            <span>Class Price</span>
                            <span>{{ $class->formatted_price }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-xl font-bold text-gray-900 pt-3 border-t border-gray-200">
                            <span>Total</span>
                            <span>${{ number_format($totalPriceCents / 100, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>

                    <!-- Error Display -->
                    <div id="error-message" class="hidden bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>

                    <form id="payment-form">
                        <!-- Customer Info -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>

                        <!-- Stripe Card Element -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                            <div id="card-element" class="px-4 py-3 border border-gray-300 rounded-lg bg-white"></div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submit-button" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition shadow-lg">
                            <span id="button-text">Pay ${{ number_format($totalPriceCents / 100, 2) }}</span>
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>

                        <p class="text-sm text-gray-500 mt-4 text-center">
                            Your payment information is secure and encrypted
                        </p>
                    </form>
                </div>

                <!-- Security Badges -->
                <div class="mt-6 flex items-center justify-center space-x-6 text-sm text-gray-500">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Secure Payment
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Powered by Stripe
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pass data to JavaScript -->
    <script>
        window.stripePublicKey = '{{ config('services.stripe.key') }}';
        window.artClassId = {{ $class->id }};
        window.createPaymentIntentUrl = '{{ route('checkout.payment-intent') }}';
        window.confirmPaymentUrl = '{{ route('checkout.confirm-payment') }}';
        window.csrfToken = '{{ csrf_token() }}';
        @if($class->is_party_event && $partyPackage)
        window.partyPackage = '{{ $partyPackage }}';
        window.partyGuests = {{ $partyGuests }};
        window.selectedAddons = @json($selectedAddons);
        @endif
    </script>
    @vite(['resources/js/checkout.js'])
</x-public-layout>
