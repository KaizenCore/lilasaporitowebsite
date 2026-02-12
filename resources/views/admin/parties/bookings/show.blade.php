<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('admin.parties.bookings.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Booking {{ $partyBooking->booking_number }}
                </h2>
                <span class="ml-3 px-3 py-1 text-sm rounded-full bg-{{ $partyBooking->status_badge_color }}-100 text-{{ $partyBooking->status_badge_color }}-800">
                    {{ $partyBooking->status_display }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Booking Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-medium">{{ $partyBooking->contact_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-medium">{{ $partyBooking->contact_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="font-medium">{{ $partyBooking->contact_phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Submitted</p>
                            <p class="font-medium">{{ $partyBooking->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Event Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Event Type</p>
                            <p class="font-medium">{{ $partyBooking->event_type_display }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Guest Count</p>
                            <p class="font-medium">{{ $partyBooking->guest_count }} people</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Preferred Date</p>
                            <p class="font-medium">{{ $partyBooking->preferred_date->format('l, F j, Y') }}</p>
                            @if($partyBooking->preferred_time)
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($partyBooking->preferred_time)->format('g:i A') }}</p>
                            @endif
                        </div>
                        @if($partyBooking->alternate_date)
                        <div>
                            <p class="text-sm text-gray-500">Alternate Date</p>
                            <p class="font-medium">{{ $partyBooking->alternate_date->format('l, F j, Y') }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="font-medium">{{ $partyBooking->location_type_display }}</p>
                            @if($partyBooking->full_customer_address)
                                <p class="text-sm text-gray-500">{{ $partyBooking->full_customer_address }}</p>
                            @endif
                        </div>
                        @if($partyBooking->honoree_name)
                        <div>
                            <p class="text-sm text-gray-500">Honoree</p>
                            <p class="font-medium">{{ $partyBooking->honoree_name }} @if($partyBooking->honoree_age)({{ $partyBooking->honoree_age }} years old)@endif</p>
                        </div>
                        @endif
                    </div>
                    @if($partyBooking->event_type === 'fundraiser')
                    <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="text-sm font-semibold text-green-800 mb-2">Fundraiser Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Organization</p>
                                <p class="font-medium">{{ $partyBooking->fundraiser_org_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Cause / Charity</p>
                                <p class="font-medium">{{ $partyBooking->fundraiser_cause }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Type</p>
                                <p class="font-medium">{{ $partyBooking->fundraiser_type === 'donated' ? 'Donated Time (Free)' : 'Fundraiser Event (Raise Money)' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($partyBooking->event_details)
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">Additional Details</p>
                        <p class="mt-1">{{ $partyBooking->event_details }}</p>
                    </div>
                    @endif
                </div>

                <!-- Painting Selection -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Painting Selection</h3>
                    @if($partyBooking->partyPainting)
                        <div class="flex items-start gap-4">
                            <img src="{{ asset('storage/' . $partyBooking->partyPainting->image_path) }}" class="w-24 h-24 object-cover rounded">
                            <div>
                                <p class="font-medium">{{ $partyBooking->partyPainting->title }}</p>
                                <p class="text-sm text-gray-500">{{ $partyBooking->partyPainting->difficulty_level }} - {{ $partyBooking->partyPainting->formatted_duration }}</p>
                            </div>
                        </div>
                    @elseif($partyBooking->wants_custom_painting)
                        <div class="bg-yellow-50 p-4 rounded">
                            <p class="font-medium text-yellow-800">Custom Painting Requested</p>
                            @if($partyBooking->custom_painting_description)
                                <p class="mt-2 text-sm">{{ $partyBooking->custom_painting_description }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500">No painting selected yet</p>
                    @endif
                </div>

                <!-- Selected Add-ons -->
                @if($partyBooking->selected_addon_ids && count($partyBooking->selected_addon_ids) > 0)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Selected Add-ons</h3>
                    <ul class="space-y-2">
                        @foreach($partyBooking->getSelectedAddons() as $addon)
                            <li class="flex justify-between">
                                <span>{{ $addon->name }}</span>
                                <span class="text-gray-500">{{ $addon->formatted_price }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>

            <!-- Right: Quote Builder & Actions -->
            <div class="space-y-6">
                <!-- Quote Builder -->
                @if($partyBooking->canSendQuote())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6" x-data="{
                    pricingConfigId: '',
                    guestCount: {{ $partyBooking->guest_count }},
                    selectedAddons: {{ json_encode($partyBooking->selected_addon_ids ?? []) }},
                    subtotal: 0,
                    addonsTotal: 0,
                    venueFee: {{ $partyBooking->location_type === 'lila_hosts' ? 1 : 0 }},
                    customPaintingFee: {{ $partyBooking->wants_custom_painting ? 1 : 0 }},
                    adjustment: 0,
                    depositPercent: 50
                }">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Build Quote</h3>
                    <form action="{{ route('admin.parties.bookings.quote', $partyBooking) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Pricing Config *</label>
                            <select name="party_pricing_config_id" x-model="pricingConfigId" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">Select...</option>
                                @foreach($pricingConfigs as $config)
                                    <option value="{{ $config->id }}" data-base="{{ $config->base_price_cents }}" data-venue="{{ $config->lila_venue_fee_cents }}" data-custom="{{ $config->custom_painting_fee_cents }}">
                                        {{ $config->name }} ({{ $config->pricing_type_display }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Guest Count</label>
                            <input type="number" name="guest_count" x-model="guestCount" min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Add-ons</label>
                            <div class="mt-2 space-y-2 max-h-40 overflow-y-auto">
                                @foreach($addons as $addon)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="selected_addon_ids[]" value="{{ $addon->id }}"
                                            {{ in_array($addon->id, $partyBooking->selected_addon_ids ?? []) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-purple-600">
                                        <span class="ml-2 text-sm">{{ $addon->name }} ({{ $addon->formatted_price }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="border-t pt-4 mb-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Subtotal</span>
                                <input type="number" name="quoted_subtotal_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300">
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Add-ons</span>
                                <input type="number" name="quoted_addons_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300">
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Venue Fee</span>
                                <input type="number" name="quoted_venue_fee_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300">
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Custom Painting</span>
                                <input type="number" name="quoted_custom_painting_fee_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300">
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Adjustment (+/-)</span>
                                <input type="number" name="quoted_adjustment_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300">
                            </div>
                            <div class="flex justify-between font-semibold border-t pt-2">
                                <span>Total</span>
                                <input type="number" name="quoted_total_cents" value="0" class="w-24 text-right text-sm rounded-md border-gray-300 font-semibold">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Deposit Required (cents)</label>
                            <input type="number" name="deposit_required_cents" value="0" min="0"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            <p class="text-xs text-gray-500 mt-1">Leave 0 for full payment upfront</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Quote Notes (visible to customer)</label>
                            <textarea name="quote_notes" rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Quote Expires In (days)</label>
                            <input type="number" name="quote_expires_days" value="7" min="1" max="30"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                        </div>

                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                            Send Quote
                        </button>
                    </form>
                </div>
                @endif

                <!-- Current Quote (if quoted) -->
                @if($partyBooking->status !== 'inquiry' && $partyBooking->quoted_total_cents)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quote Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>${{ number_format($partyBooking->quoted_subtotal_cents / 100, 2) }}</span>
                        </div>
                        @if($partyBooking->quoted_addons_cents)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Add-ons</span>
                            <span>${{ number_format($partyBooking->quoted_addons_cents / 100, 2) }}</span>
                        </div>
                        @endif
                        @if($partyBooking->quoted_venue_fee_cents)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Venue Fee</span>
                            <span>${{ number_format($partyBooking->quoted_venue_fee_cents / 100, 2) }}</span>
                        </div>
                        @endif
                        @if($partyBooking->quoted_custom_painting_fee_cents)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Custom Painting</span>
                            <span>${{ number_format($partyBooking->quoted_custom_painting_fee_cents / 100, 2) }}</span>
                        </div>
                        @endif
                        @if($partyBooking->quoted_adjustment_cents)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Adjustment</span>
                            <span>{{ $partyBooking->quoted_adjustment_cents >= 0 ? '+' : '' }}${{ number_format($partyBooking->quoted_adjustment_cents / 100, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between font-semibold border-t pt-2">
                            <span>Total</span>
                            <span>{{ $partyBooking->formatted_quoted_total }}</span>
                        </div>
                        @if($partyBooking->total_paid_cents > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Paid</span>
                            <span>{{ $partyBooking->formatted_total_paid }}</span>
                        </div>
                        @endif
                        @if($partyBooking->remaining_balance > 0)
                        <div class="flex justify-between text-orange-600">
                            <span>Balance Due</span>
                            <span>{{ $partyBooking->formatted_remaining_balance }}</span>
                        </div>
                        @endif
                    </div>
                    @if($partyBooking->quote_notes)
                    <div class="mt-4 p-3 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600">{{ $partyBooking->quote_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if($partyBooking->status === 'inquiry')
                            <form action="{{ route('admin.parties.bookings.decline', $partyBooking) }}" method="POST" onsubmit="return confirm('Decline this inquiry?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                                    Decline Inquiry
                                </button>
                            </form>
                        @endif

                        @if(in_array($partyBooking->status, ['quoted', 'accepted', 'deposit_paid']) || ($partyBooking->status === 'inquiry' && $partyBooking->event_type === 'fundraiser' && $partyBooking->fundraiser_type === 'donated'))
                            <form action="{{ route('admin.parties.bookings.confirm', $partyBooking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="confirmed_date" value="{{ $partyBooking->confirmed_date ?? $partyBooking->preferred_date }}">
                                <input type="hidden" name="confirmed_time" value="{{ $partyBooking->confirmed_time ?? $partyBooking->preferred_time }}">
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    @if($partyBooking->event_type === 'fundraiser' && $partyBooking->fundraiser_type === 'donated')
                                        Confirm Donated Event (No Payment)
                                    @else
                                        Manually Confirm (Bypass Payment)
                                    @endif
                                </button>
                            </form>
                        @endif

                        @if($partyBooking->status === 'confirmed')
                            <form action="{{ route('admin.parties.bookings.complete', $partyBooking) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                    Mark as Completed
                                </button>
                            </form>
                        @endif

                        @if($partyBooking->canCancel())
                            <form action="{{ route('admin.parties.bookings.cancel', $partyBooking) }}" method="POST" onsubmit="return confirm('Cancel this booking?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                                    Cancel Booking
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Admin Notes</h3>
                    <form action="{{ route('admin.parties.bookings.notes', $partyBooking) }}" method="POST">
                        @csrf
                        <textarea name="admin_notes" rows="3" placeholder="Internal notes..."
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">{{ $partyBooking->admin_notes }}</textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                            Save Notes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
