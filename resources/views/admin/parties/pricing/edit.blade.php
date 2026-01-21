<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('admin.parties.pricing.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Pricing: {{ $pricing->name }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6" x-data="{ pricingType: '{{ old('pricing_type', $pricing->pricing_type) }}', tiers: {{ json_encode(old('tier_pricing', $pricing->tier_pricing ?? [['min' => 4, 'max' => 6, 'price_cents' => 3500]])) }} }">
                <form action="{{ route('admin.parties.pricing.update', $pricing) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $pricing->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" id="description" rows="2"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $pricing->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pricing Type *</label>
                        <div class="flex gap-4">
                            <label class="flex items-center">
                                <input type="radio" name="pricing_type" value="flat_per_person" x-model="pricingType" class="mr-2">
                                <span>Flat Per Person</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pricing_type" value="tiered" x-model="pricingType" class="mr-2">
                                <span>Tiered Pricing</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pricing_type" value="custom_quote" x-model="pricingType" class="mr-2">
                                <span>Custom Quote</span>
                            </label>
                        </div>
                    </div>

                    <div x-show="pricingType === 'flat_per_person'" class="mb-4">
                        <label for="base_price_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price Per Person (cents) *</label>
                        <input type="number" name="base_price_cents" id="base_price_cents" value="{{ old('base_price_cents', $pricing->base_price_cents) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500">Enter in cents (e.g., 3500 = $35.00)</p>
                    </div>

                    <div x-show="pricingType === 'tiered'" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tier Pricing</label>
                        <template x-for="(tier, index) in tiers" :key="index">
                            <div class="flex gap-2 mb-2 items-center">
                                <input type="number" :name="'tier_pricing[' + index + '][min]'" x-model="tier.min" placeholder="Min" class="w-20 rounded-md border-gray-300 text-sm">
                                <span>-</span>
                                <input type="number" :name="'tier_pricing[' + index + '][max]'" x-model="tier.max" placeholder="Max" class="w-20 rounded-md border-gray-300 text-sm">
                                <span>guests:</span>
                                <input type="number" :name="'tier_pricing[' + index + '][price_cents]'" x-model="tier.price_cents" placeholder="Price (cents)" class="w-32 rounded-md border-gray-300 text-sm">
                                <span class="text-sm text-gray-500">/person</span>
                                <button type="button" @click="tiers.splice(index, 1)" class="text-red-600 hover:text-red-800">&times;</button>
                            </div>
                        </template>
                        <button type="button" @click="tiers.push({min: '', max: '', price_cents: ''})" class="text-sm text-purple-600 hover:text-purple-800">+ Add Tier</button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="minimum_guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Guests *</label>
                            <input type="number" name="minimum_guests" id="minimum_guests" value="{{ old('minimum_guests', $pricing->minimum_guests) }}" required min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label for="maximum_guests" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Guests</label>
                            <input type="number" name="maximum_guests" id="maximum_guests" value="{{ old('maximum_guests', $pricing->maximum_guests) }}" min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    <div class="border-t pt-4 mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Lila-Hosted Parties</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="lila_venue_fee_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Venue Fee (cents)</label>
                                <input type="number" name="lila_venue_fee_cents" id="lila_venue_fee_cents" value="{{ old('lila_venue_fee_cents', $pricing->lila_venue_fee_cents) }}" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label for="lila_venue_max_capacity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Max Capacity *</label>
                                <input type="number" name="lila_venue_max_capacity" id="lila_venue_max_capacity" value="{{ old('lila_venue_max_capacity', $pricing->lila_venue_max_capacity) }}" required min="1" max="50"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="custom_painting_fee_cents" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Painting Fee (cents)</label>
                        <input type="number" name="custom_painting_fee_cents" id="custom_painting_fee_cents" value="{{ old('custom_painting_fee_cents', $pricing->custom_painting_fee_cents) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pricing->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $pricing->is_default) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Set as Default</span>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.parties.pricing.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Update Config</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
