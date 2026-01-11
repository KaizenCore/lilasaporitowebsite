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
        // Build request body with party params if present
        const requestBody = {
            art_class_id: window.artClassId
        };

        if (window.partyPackage) {
            requestBody.party_package = window.partyPackage;
            requestBody.party_guests = window.partyGuests;
            if (window.selectedAddons && window.selectedAddons.length > 0) {
                requestBody.selected_addons = window.selectedAddons;
            }
        }

        // Create payment intent on server
        const response = await fetch(window.createPaymentIntentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
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
            // Payment successful - confirm and create booking
            await confirmPaymentAndCreateBooking(paymentIntent.id);
        }
    } catch (error) {
        showError(error.message || 'An unexpected error occurred');
        setLoading(false);
    }
});

// Confirm payment and create booking
async function confirmPaymentAndCreateBooking(paymentIntentId) {
    try {
        const response = await fetch(window.confirmPaymentUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_intent_id: paymentIntentId,
                art_class_id: window.artClassId
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Booking created, redirect to success page
            window.location.href = `/checkout/success/${data.booking_id}`;
        } else {
            // Show error but payment was successful
            showError(data.error || 'Payment succeeded but booking creation failed. Please contact support.');
            setLoading(false);
        }
    } catch (error) {
        console.error('Error confirming payment:', error);
        showError('Payment succeeded but we could not confirm your booking. Please contact support.');
        setLoading(false);
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
