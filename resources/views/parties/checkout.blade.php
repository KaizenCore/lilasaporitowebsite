<x-public-layout>
    <x-slot name="title">Checkout - {{ $partyBooking->booking_number }}</x-slot>

    <div class="py-12 bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50 min-h-screen">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-purple-600 text-white px-6 py-6 text-center">
                    <p class="text-purple-200 text-sm mb-1">Complete Your Booking</p>
                    <h1 class="text-2xl font-bold">{{ $partyBooking->booking_number }}</h1>
                </div>

                <div class="p-6" x-data="checkoutForm()">
                    <!-- Quote Summary -->
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quote Summary</h2>

                        <div class="bg-gray-50 rounded-lg p-4 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Event Type</span>
                                <span class="font-medium">{{ $partyBooking->event_type_display }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date</span>
                                <span class="font-medium">{{ ($partyBooking->confirmed_date ?? $partyBooking->preferred_date)->format('M j, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Guests</span>
                                <span class="font-medium">{{ $partyBooking->guest_count }} people</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Location</span>
                                <span class="font-medium">{{ $partyBooking->location_type_display }}</span>
                            </div>

                            @if($partyBooking->partyPainting)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Painting</span>
                                    <span class="font-medium">{{ $partyBooking->partyPainting->title }}</span>
                                </div>
                            @elseif($partyBooking->wants_custom_painting)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Painting</span>
                                    <span class="font-medium">Custom Design</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="mb-6 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Price Breakdown</h2>

                        <div class="space-y-2 text-sm">
                            @if($partyBooking->quoted_subtotal_cents)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Base Price</span>
                                    <span>${{ number_format($partyBooking->quoted_subtotal_cents / 100, 2) }}</span>
                                </div>
                            @endif

                            @if($partyBooking->quoted_addons_cents)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Add-ons</span>
                                    <span>${{ number_format($partyBooking->quoted_addons_cents / 100, 2) }}</span>
                                </div>
                            @endif

                            @if($partyBooking->quoted_venue_fee_cents)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Venue Fee</span>
                                    <span>${{ number_format($partyBooking->quoted_venue_fee_cents / 100, 2) }}</span>
                                </div>
                            @endif

                            @if($partyBooking->quoted_custom_painting_fee_cents)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Custom Painting Fee</span>
                                    <span>${{ number_format($partyBooking->quoted_custom_painting_fee_cents / 100, 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between font-bold text-lg border-t pt-3 mt-3">
                                <span>Total</span>
                                <span>{{ $partyBooking->formatted_quoted_total }}</span>
                            </div>
                        </div>

                        @if($partyBooking->quote_notes)
                            <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                                <p class="text-sm text-purple-800">{{ $partyBooking->quote_notes }}</p>
                            </div>
                        @endif

                        @if($partyBooking->quote_expires_at)
                            <p class="mt-3 text-xs text-gray-500">
                                Quote valid until {{ $partyBooking->quote_expires_at->format('F j, Y') }}
                            </p>
                        @endif
                    </div>

                    <!-- Payment Form -->
                    <div class="border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>

                        <form @submit.prevent="submitPayment" id="payment-form">
                            <!-- Card Element -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Details</label>
                                <div id="card-element" class="p-3 border rounded-lg bg-white"></div>
                                <div id="card-errors" class="mt-2 text-sm text-red-600" x-text="cardError"></div>
                            </div>

                            <!-- Billing Name -->
                            <div class="mb-4">
                                <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Name on Card</label>
                                <input type="text" id="billing_name" x-model="billingName" required
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ $partyBooking->contact_name }}">
                            </div>

                            <!-- Terms -->
                            <div class="mb-6">
                                <label class="flex items-start gap-2">
                                    <input type="checkbox" x-model="agreedToTerms" required class="mt-1">
                                    <span class="text-sm text-gray-600">
                                        I agree to the booking terms and understand that this payment confirms my party reservation.
                                    </span>
                                </label>
                            </div>

                            <!-- Error Display -->
                            <div x-show="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-700" x-text="error"></p>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" :disabled="processing || !agreedToTerms"
                                class="w-full py-3 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!processing">Pay {{ $partyBooking->formatted_quoted_total }}</span>
                                <span x-show="processing" class="flex items-center justify-center gap-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </form>
                    </div>

                    <!-- Back Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('parties.booking.show', $partyBooking) }}" class="text-sm text-purple-600 hover:text-purple-800">
                            &larr; Back to Booking Details
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500 flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Secured by Stripe. Your payment info is encrypted.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function checkoutForm() {
            return {
                stripe: null,
                cardElement: null,
                billingName: '{{ $partyBooking->contact_name }}',
                agreedToTerms: false,
                processing: false,
                error: null,
                cardError: null,

                init() {
                    this.stripe = Stripe('{{ config('services.stripe.key') }}');
                    const elements = this.stripe.elements();

                    this.cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#374151',
                                '::placeholder': { color: '#9CA3AF' }
                            },
                            invalid: {
                                color: '#EF4444',
                                iconColor: '#EF4444'
                            }
                        }
                    });

                    this.cardElement.mount('#card-element');

                    this.cardElement.on('change', (event) => {
                        this.cardError = event.error ? event.error.message : null;
                    });
                },

                async submitPayment() {
                    if (this.processing) return;

                    this.processing = true;
                    this.error = null;

                    try {
                        // Create payment intent
                        const intentResponse = await fetch('{{ route('parties.checkout.payment-intent', $partyBooking) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const intentData = await intentResponse.json();

                        if (!intentResponse.ok) {
                            throw new Error(intentData.error || 'Failed to create payment');
                        }

                        // Confirm payment with Stripe
                        const { error, paymentIntent } = await this.stripe.confirmCardPayment(
                            intentData.clientSecret,
                            {
                                payment_method: {
                                    card: this.cardElement,
                                    billing_details: {
                                        name: this.billingName,
                                        email: '{{ $partyBooking->contact_email }}'
                                    }
                                }
                            }
                        );

                        if (error) {
                            throw new Error(error.message);
                        }

                        if (paymentIntent.status === 'succeeded') {
                            window.location.href = '{{ route('parties.checkout.success', $partyBooking) }}';
                        } else {
                            throw new Error('Payment was not completed. Please try again.');
                        }

                    } catch (err) {
                        this.error = err.message;
                        this.processing = false;
                    }
                }
            };
        }
    </script>
    @endpush
</x-public-layout>
