<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - FrizzBoss Store</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="font-sans antialiased bg-gradient-to-br from-purple-50 via-pink-50 to-orange-50">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
                        FrizzBoss
                    </a>
                </div>
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-gray-700 hover:text-purple-600 font-medium transition">Home</a>
                    <a href="{{ route('store.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Store</a>
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-purple-600 font-medium transition">Cart</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Back Button -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <a href="{{ route('cart.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Cart
        </a>
    </div>

    <!-- Checkout Form -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Checkout</h1>
            <p class="text-lg text-gray-600">Complete your order securely with Stripe</p>
        </div>

        <div class="grid lg:grid-cols-5 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Order Summary</h2>

                    <div class="space-y-4 mb-6">
                        @foreach($cart as $item)
                            <div class="flex gap-3">
                                @if($item['image_path'])
                                    <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['title'] }}" class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <span class="text-xs text-gray-400">No img</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item['title'] }}</h3>
                                    <p class="text-sm text-gray-600">Qty: {{ $item['quantity'] }}</p>
                                    <p class="text-sm font-semibold text-gray-900">${{ number_format(($item['price_cents'] * $item['quantity']) / 100, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-200 pt-4 space-y-2 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $count }} items)</span>
                            <span>${{ number_format($subtotal / 100, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>$0.00</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>$0.00</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span>${{ number_format($subtotal / 100, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>

                    <form id="payment-form">
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Information</label>
                            <div id="card-element" class="p-4 border border-gray-300 rounded-lg bg-white"></div>
                            <div id="card-errors" class="text-red-600 text-sm mt-2"></div>
                        </div>

                        <div class="mb-6">
                            <label for="cardholder-name" class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <input type="text" id="cardholder-name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="John Doe">
                        </div>

                        <div id="payment-message" class="hidden mb-4 p-4 rounded-lg"></div>

                        <button type="submit" id="submit-button"
                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-lg hover:from-purple-700 hover:to-pink-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span id="button-text">Pay ${{ number_format($subtotal / 100, 2) }}</span>
                            <span id="spinner" class="hidden">
                                <svg class="animate-spin inline h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>

                        <p class="text-center text-sm text-gray-500 mt-4">
                            <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Secure checkout powered by Stripe
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script>
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        let elements, cardElement;

        // Initialize Stripe Elements
        elements = stripe.elements();
        cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Figtree", sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });
        cardElement.mount('#card-element');

        // Handle real-time validation errors
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const buttonText = document.getElementById('button-text');
        const spinner = document.getElementById('spinner');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Disable submit button and show spinner
            submitButton.disabled = true;
            buttonText.classList.add('hidden');
            spinner.classList.remove('hidden');

            try {
                // Create payment intent
                const response = await fetch('{{ route('checkout.order.payment-intent') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                });

                const data = await response.json();

                if (data.error) {
                    showMessage(data.error, 'error');
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                    return;
                }

                // Confirm the payment
                const result = await stripe.confirmCardPayment(data.clientSecret, {
                    payment_method: {
                        card: cardElement,
                        billing_details: {
                            name: document.getElementById('cardholder-name').value
                        }
                    }
                });

                if (result.error) {
                    showMessage(result.error.message, 'error');
                    submitButton.disabled = false;
                    buttonText.classList.remove('hidden');
                    spinner.classList.add('hidden');
                } else {
                    // Payment successful! Poll for order creation
                    await pollForOrder(result.paymentIntent.id);
                }
            } catch (error) {
                showMessage('An unexpected error occurred.', 'error');
                submitButton.disabled = false;
                buttonText.classList.remove('hidden');
                spinner.classList.add('hidden');
            }
        });

        async function pollForOrder(paymentIntentId, attempts = 0) {
            if (attempts >= 10) {
                showMessage('Order processing is taking longer than expected. Please check your orders page.', 'warning');
                setTimeout(() => {
                    window.location.href = '{{ route('store.index') }}';
                }, 3000);
                return;
            }

            try {
                const response = await fetch(`/api/check-order/${paymentIntentId}`);

                if (response.ok) {
                    const data = await response.json();
                    if (data.order_id) {
                        window.location.href = `/checkout/order/success/${data.order_id}`;
                        return;
                    }
                }
            } catch (error) {
                console.error('Error checking order:', error);
            }

            // Try again in 1 second
            setTimeout(() => pollForOrder(paymentIntentId, attempts + 1), 1000);
        }

        function showMessage(messageText, type = 'error') {
            const messageContainer = document.getElementById('payment-message');
            messageContainer.classList.remove('hidden', 'bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700', 'bg-yellow-100', 'text-yellow-700');

            if (type === 'error') {
                messageContainer.classList.add('bg-red-100', 'text-red-700');
            } else if (type === 'success') {
                messageContainer.classList.add('bg-green-100', 'text-green-700');
            } else if (type === 'warning') {
                messageContainer.classList.add('bg-yellow-100', 'text-yellow-700');
            }

            messageContainer.textContent = messageText;
        }
    </script>
</body>
</html>
