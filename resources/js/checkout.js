// Initialize Stripe
const stripe = Stripe(window.stripePublicKey);

// Create card element
const elements = stripe.elements();
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#32325d',
            fontFamily: '"Figtree", sans-serif',
            '::placeholder': {
                color: '#aab7c4'
            },
            padding: '12px'
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    }
});

// Mount the card element
cardElement.mount('#card-element');

// Handle real-time validation errors
cardElement.on('change', (event) => {
    const errorElement = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    if (event.error) {
        showError(event.error.message);
    } else {
        hideError();
    }
});

// Handle form submission
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const buttonText = document.getElementById('button-text');
const spinner = document.getElementById('spinner');

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Disable submit button
    setLoading(true);
    hideError();

    try {
        // Create payment intent on server
        const response = await fetch(window.createPaymentIntentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                art_class_id: window.artClassId
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Failed to create payment intent');
        }

        // Confirm payment with Stripe
        const { error, paymentIntent } = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: cardElement,
            }
        });

        if (error) {
            // Payment failed
            showError(error.message);
            setLoading(false);
        } else if (paymentIntent.status === 'succeeded') {
            // Payment successful - wait a moment for webhook to process
            // Then redirect or poll for booking creation
            setTimeout(() => {
                pollForBooking(paymentIntent.id);
            }, 2000);
        }
    } catch (error) {
        showError(error.message || 'An unexpected error occurred');
        setLoading(false);
    }
});

// Poll for booking creation (after webhook processes)
async function pollForBooking(paymentIntentId, attempts = 0) {
    const maxAttempts = 10;

    try {
        // Try to find the booking by checking user's bookings
        const response = await fetch(`/my-bookings?check_payment=${paymentIntentId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            }
        });

        if (response.ok) {
            const data = await response.json();

            if (data.booking_id) {
                // Booking found, redirect to success page
                window.location.href = `/checkout/success/${data.booking_id}`;
                return;
            }
        }

        // If not found and haven't exceeded attempts, try again
        if (attempts < maxAttempts) {
            setTimeout(() => {
                pollForBooking(paymentIntentId, attempts + 1);
            }, 1000);
        } else {
            // Fallback: redirect to bookings page with success message
            window.location.href = '/my-bookings?payment_success=1';
        }
    } catch (error) {
        console.error('Error polling for booking:', error);

        if (attempts < maxAttempts) {
            setTimeout(() => {
                pollForBooking(paymentIntentId, attempts + 1);
            }, 1000);
        } else {
            window.location.href = '/my-bookings?payment_success=1';
        }
    }
}

// Helper functions
function setLoading(loading) {
    if (loading) {
        submitButton.disabled = true;
        buttonText.classList.add('hidden');
        spinner.classList.remove('hidden');
    } else {
        submitButton.disabled = false;
        buttonText.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
}

function showError(message) {
    const errorElement = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    errorText.textContent = message;
    errorElement.classList.remove('hidden');

    // Scroll to error
    errorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideError() {
    const errorElement = document.getElementById('error-message');
    errorElement.classList.add('hidden');
}
