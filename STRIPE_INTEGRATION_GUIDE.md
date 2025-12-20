# Stripe Payment Integration Guide - FrizzBoss

This guide explains how the complete Stripe payment integration works for the FrizzBoss booking system.

## Overview

The integration provides a seamless payment experience with:
- Secure checkout using Stripe Elements
- Real-time payment processing
- Webhook-based booking creation
- Automatic ticket code generation
- Beautiful confirmation pages

## Setup Instructions

### 1. Environment Configuration

Add your Stripe keys to the `.env` file:

```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

**Getting your keys:**
- Log into [Stripe Dashboard](https://dashboard.stripe.com)
- Navigate to Developers > API keys
- Copy your Publishable key and Secret key
- For webhook secret, see section 3 below

### 2. Install Dependencies

The Stripe PHP SDK should already be installed. If not:

```bash
composer require stripe/stripe-php
```

### 3. Configure Stripe Webhook

**Important:** Webhooks are required for creating bookings after successful payment.

1. Go to [Stripe Webhooks](https://dashboard.stripe.com/webhooks)
2. Click "Add endpoint"
3. Enter your webhook URL: `https://yourdomain.com/webhook/stripe`
4. Select events to listen for:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copy the "Signing secret" and add it to `.env` as `STRIPE_WEBHOOK_SECRET`

**Testing locally with Stripe CLI:**
```bash
stripe listen --forward-to http://localhost:8000/webhook/stripe
```

### 4. Build Assets

Compile the checkout JavaScript:

```bash
npm install
npm run build
```

For development:
```bash
npm run dev
```

## Architecture

### Files Created

#### Controllers
- `app/Http/Controllers/CheckoutController.php` - Handles checkout flow
- `app/Http/Controllers/PaymentController.php` - Processes webhooks

#### Services
- `app/Services/StripeService.php` - Stripe API wrapper

#### Views
- `resources/views/checkout/show.blade.php` - Checkout page
- `resources/views/checkout/success.blade.php` - Confirmation page

#### JavaScript
- `resources/js/checkout.js` - Stripe Elements integration

#### Configuration
- `config/services.php` - Stripe config added
- `routes/web.php` - Routes added
- `vite.config.js` - Build config updated

## Payment Flow

### Step 1: User Initiates Checkout
- User clicks "Book Your Spot Now" on class details page
- System checks:
  - Class availability (not full, not past)
  - User doesn't already have a booking
- Redirects to `/checkout/{class-slug}`

### Step 2: Checkout Page
- Displays class summary and order details
- Shows Stripe Elements card input form
- User enters payment information

### Step 3: Payment Intent Creation
- On form submit, JavaScript calls `/checkout/payment-intent`
- `CheckoutController@createPaymentIntent` creates payment intent via Stripe API
- Returns client secret to frontend

### Step 4: Payment Confirmation
- JavaScript uses Stripe.js to confirm payment with card
- Stripe processes payment securely
- On success, payment status becomes "succeeded"

### Step 5: Webhook Processing
- Stripe sends `payment_intent.succeeded` webhook
- `PaymentController@webhook` receives and verifies signature
- Creates Booking and Payment records in database
- Ticket code auto-generated (FB-XXXX format)
- Stripe fees calculated automatically

### Step 6: Success Page
- JavaScript polls for booking creation
- Redirects to `/checkout/success/{booking}`
- Displays:
  - Large ticket code
  - Class details
  - Payment summary
  - Next steps instructions

## Security Features

### CSRF Protection
- All authenticated routes use CSRF tokens
- Webhook route explicitly excludes CSRF (verified via signature)

### Webhook Signature Verification
```php
$event = $this->stripeService->verifyWebhookSignature($payload, $signature);
```

### Authorization Checks
- Checkout requires authentication
- Success page verifies booking ownership
- Double-checks for duplicate bookings

### Data Validation
- Class availability verified before payment
- Payment intent metadata includes user/class info
- Booking creation uses database transactions

## Error Handling

### Frontend Errors
- Card validation errors shown in real-time
- Payment failures displayed with clear messages
- Network errors handled gracefully

### Backend Errors
- Stripe API errors logged and caught
- Webhook processing errors logged
- Database transactions prevent partial data

### Edge Cases
- Duplicate booking prevention
- Class full during checkout
- Webhook duplicate prevention
- Payment polling timeout handling

## Testing

### Test Card Numbers

Use Stripe test cards (never use real cards in test mode):

**Success:**
- `4242 4242 4242 4242` - Visa

**Requires authentication:**
- `4000 0025 0000 3155` - 3D Secure

**Declined:**
- `4000 0000 0000 9995` - Insufficient funds

**Expiry:** Any future date
**CVC:** Any 3 digits
**ZIP:** Any 5 digits

### Testing Workflow

1. Create a test class in admin panel
2. Navigate to class details page
3. Click "Book Your Spot Now"
4. Enter test card details
5. Submit payment
6. Verify booking creation
7. Check success page displays

### Webhook Testing

Using Stripe CLI:
```bash
# Install Stripe CLI
stripe login

# Forward webhooks to local
stripe listen --forward-to http://localhost:8000/webhook/stripe

# Trigger test webhook
stripe trigger payment_intent.succeeded
```

## Database Records

### Booking Record
```php
- user_id: User who made booking
- art_class_id: Class being booked
- ticket_code: Auto-generated (FB-XXXX)
- payment_status: 'completed'
- attendance_status: 'booked'
```

### Payment Record
```php
- booking_id: Related booking
- stripe_payment_intent_id: Stripe PI ID
- stripe_charge_id: Stripe charge ID
- amount_cents: Total amount
- status: 'succeeded'
- stripe_fee_cents: Calculated fee
- net_amount_cents: Amount minus fees
- metadata: Additional info
```

## Routes

### Public Routes
```php
GET  /classes/{slug}              # Class details
```

### Authenticated Routes
```php
GET  /checkout/{class:slug}       # Checkout page
POST /checkout/payment-intent     # Create payment intent (AJAX)
GET  /checkout/success/{booking}  # Success page
GET  /my-bookings                 # User's bookings
```

### Webhook Route (No Auth, No CSRF)
```php
POST /webhook/stripe              # Stripe webhook handler
```

## Customization

### Changing Currency
Edit `StripeService.php`:
```php
'currency' => 'usd', // Change to 'cad', 'eur', etc.
```

### Adjusting Stripe Fee Calculation
Edit `Payment.php` model:
```php
public function calculateStripeFee()
{
    // Modify fee calculation
    $percentageFee = (int) ($this->amount_cents * 0.029);
    $fixedFee = 30;
    // ...
}
```

### Customizing Email Notifications
Add event listener in `PaymentController@handlePaymentIntentSucceeded`:
```php
// After booking creation
event(new BookingConfirmed($booking));
```

## Troubleshooting

### "Payment intent creation failed"
- Check Stripe API keys in `.env`
- Verify internet connection
- Check Laravel logs: `storage/logs/laravel.log`

### Webhook not receiving events
- Verify webhook URL is publicly accessible
- Check webhook secret matches Stripe dashboard
- Review webhook logs in Stripe dashboard
- Ensure route excludes CSRF middleware

### Booking not created after payment
- Check webhook is configured correctly
- Verify webhook secret in `.env`
- Check application logs for errors
- Ensure database migrations ran

### JavaScript errors
- Run `npm run build`
- Clear browser cache
- Check browser console for errors
- Verify Stripe.js loaded correctly

## Production Checklist

- [ ] Replace test API keys with live keys
- [ ] Configure production webhook endpoint
- [ ] Test with real card in test mode
- [ ] Enable webhook signature verification
- [ ] Set up error monitoring (Sentry, etc.)
- [ ] Configure email notifications
- [ ] Test full checkout flow
- [ ] Verify SSL certificate on domain
- [ ] Review Stripe Dashboard settings
- [ ] Set up refund policy

## Support

For issues with:
- **Stripe API:** [Stripe Support](https://support.stripe.com)
- **Integration:** Check Laravel logs and Stripe Dashboard
- **Payments:** Review transaction in Stripe Dashboard

## References

- [Stripe Documentation](https://stripe.com/docs)
- [Stripe Elements](https://stripe.com/docs/stripe-js)
- [Stripe Webhooks](https://stripe.com/docs/webhooks)
- [Testing Stripe](https://stripe.com/docs/testing)
