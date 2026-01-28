<x-public-layout>
    <x-slot name="title">Request a Party Quote</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Request a Party Quote</h1>
                <p class="mt-2 text-gray-600">Fill out the form below and we'll send you a personalized quote within 24-48 hours.</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8" x-data="{
                step: 1,
                eventType: '{{ old('event_type', '') }}',
                locationType: '{{ old('location_type', 'customer_location') }}',
                guestCount: {{ old('guest_count', 6) }},
                wantsCustomPainting: {{ old('wants_custom_painting', false) ? 'true' : 'false' }},
                selectedPaintingId: '{{ old('party_painting_id', '') }}'
            }">
                <form action="{{ route('parties.inquire.store') }}" method="POST">
                    @csrf

                    <!-- Step Indicator -->
                    <div class="flex justify-center mb-8">
                        <div class="flex items-center space-x-4">
                            @for($i = 1; $i <= 4; $i++)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition"
                                        :class="step >= {{ $i }} ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500'">
                                        {{ $i }}
                                    </div>
                                    @if($i < 4)
                                        <div class="w-12 h-1 mx-2" :class="step > {{ $i }} ? 'bg-purple-600' : 'bg-gray-200'"></div>
                                    @endif
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Step 1: Event Details -->
                    <div x-show="step === 1" x-transition>
                        <h2 class="text-xl font-semibold mb-6">Event Details</h2>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Event Type *</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach([
                                    'birthday' => 'Birthday Party',
                                    'corporate' => 'Corporate Event',
                                    'bridal_shower' => 'Bridal Shower',
                                    'bachelorette' => 'Bachelorette',
                                    'team_building' => 'Team Building',
                                    'other' => 'Other',
                                ] as $value => $label)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="event_type" value="{{ $value }}" x-model="eventType" class="sr-only peer">
                                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('event_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Date *</label>
                                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" required min="{{ now()->addDays(7)->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                @error('preferred_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Time</label>
                                <input type="time" name="preferred_time" value="{{ old('preferred_time', '14:00') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Date</label>
                                <input type="date" name="alternate_date" value="{{ old('alternate_date') }}" min="{{ now()->addDays(7)->format('Y-m-d') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alternate Time</label>
                                <input type="time" name="alternate_time" value="{{ old('alternate_time') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <div x-show="eventType === 'birthday'" class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Birthday Child's Name</label>
                                <input type="text" name="honoree_name" value="{{ old('honoree_name') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Age Turning</label>
                                <input type="number" name="honoree_age" value="{{ old('honoree_age') }}" min="1" max="120"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Party Details -->
                    <div x-show="step === 2" x-transition>
                        <h2 class="text-xl font-semibold mb-6">Party Details</h2>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Number of Guests *</label>
                            <input type="number" name="guest_count" x-model="guestCount" required min="1" max="100"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <p class="mt-1 text-sm text-gray-500">Minimum {{ $pricingConfig->minimum_guests ?? 4 }} guests required</p>
                            @error('guest_count') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="location_type" value="lila_hosts" x-model="locationType" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                        <div class="font-medium">Lila's Studio</div>
                                        <div class="text-sm text-gray-500">Up to {{ $pricingConfig->lila_venue_max_capacity ?? 8 }} guests</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="location_type" value="customer_location" x-model="locationType" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-lg peer-checked:border-purple-600 peer-checked:bg-purple-50 hover:border-purple-300 transition">
                                        <div class="font-medium">Your Location</div>
                                        <div class="text-sm text-gray-500">We come to you!</div>
                                    </div>
                                </label>
                            </div>
                            <p x-show="locationType === 'lila_hosts' && guestCount > {{ $pricingConfig->lila_venue_max_capacity ?? 8 }}" class="mt-2 text-sm text-red-600">
                                Lila's studio can only accommodate {{ $pricingConfig->lila_venue_max_capacity ?? 8 }} guests. Please select "Your Location" for larger parties.
                            </p>
                        </div>

                        <div x-show="locationType === 'customer_location'" class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <input type="text" name="customer_address" value="{{ old('customer_address') }}"
                                    :required="locationType === 'customer_location'"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                                    <input type="text" name="customer_city" value="{{ old('customer_city') }}"
                                        :required="locationType === 'customer_location'"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State/Province *</label>
                                    <input type="text" name="customer_state" value="{{ old('customer_state') }}"
                                        :required="locationType === 'customer_location'"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">ZIP/Postal *</label>
                                    <input type="text" name="customer_zip" value="{{ old('customer_zip') }}"
                                        :required="locationType === 'customer_location'"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Details</label>
                            <textarea name="event_details" rows="3" placeholder="Tell us about your event, any special requests, themes, etc."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('event_details') }}</textarea>
                        </div>
                    </div>

                    <!-- Step 3: Painting Selection -->
                    <div x-show="step === 3" x-transition>
                        <h2 class="text-xl font-semibold mb-6">Choose a Painting</h2>

                        <div class="mb-6">
                            <label class="flex items-center mb-4">
                                <input type="checkbox" name="wants_custom_painting" value="1" x-model="wantsCustomPainting"
                                    class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">I want a custom painting design (additional fee may apply)</span>
                            </label>

                            <div x-show="wantsCustomPainting" class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Describe your custom painting idea</label>
                                <textarea name="custom_painting_description" rows="3" placeholder="Describe what you'd like..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ old('custom_painting_description') }}</textarea>
                            </div>
                        </div>

                        <div x-show="!wantsCustomPainting">
                            <p class="text-sm text-gray-600 mb-4">Select a painting from our gallery (or leave blank to decide later):</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                                @foreach($paintings as $painting)
                                    <label class="cursor-pointer group">
                                        <input type="radio" name="party_painting_id" value="{{ $painting->id }}" x-model="selectedPaintingId" class="sr-only peer">
                                        <div class="border-2 rounded-lg overflow-hidden peer-checked:border-purple-600 peer-checked:ring-2 peer-checked:ring-purple-200 hover:border-purple-300 transition">
                                            <img src="{{ asset('storage/' . $painting->image_path) }}" alt="{{ $painting->title }}" class="w-full h-32 object-cover">
                                            <div class="p-2">
                                                <p class="text-sm font-medium truncate">{{ $painting->title }}</p>
                                                <p class="text-xs text-gray-500">{{ ucfirst($painting->difficulty_level) }}</p>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Contact & Review -->
                    <div x-show="step === 4" x-transition>
                        <h2 class="text-xl font-semibold mb-6">Contact Information</h2>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
                                <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name ?? '') }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                @error('contact_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="contact_phone" value="{{ old('contact_phone') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email ?? '') }}" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            @error('contact_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if($addons->count() > 0)
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Optional Add-ons</label>
                            <div class="flex flex-wrap">
                                @foreach($addons as $addon)
                                    <div class="flex items-center mr-4 mb-2">
                                        <input type="checkbox" name="addon_ids[]" value="{{ $addon->id }}"
                                            {{ in_array($addon->id, old('addon_ids', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                        <span class="ml-2 text-sm">{{ $addon->name }} ({{ $addon->formatted_price }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="bg-purple-50 rounded-lg p-4 mb-6">
                            <p class="text-sm text-purple-800">
                                <strong>What happens next?</strong> We'll review your inquiry and send you a personalized quote within 24-48 hours. You can then accept the quote and pay to confirm your booking.
                            </p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-8 pt-6 border-t">
                        <button type="button" x-show="step > 1" @click="step--"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Back
                        </button>
                        <div x-show="step === 1"></div>

                        <button type="button" x-show="step < 4" @click="step++"
                            class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Continue
                        </button>

                        <button type="submit" x-show="step === 4"
                            class="px-8 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold">
                            Submit Inquiry
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-public-layout>
