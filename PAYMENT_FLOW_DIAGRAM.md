# Stripe Payment Flow - Visual Diagram

## Complete Payment Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         1. USER BROWSES CLASSES                         │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
                    User clicks "Book Your Spot Now"
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                    2. CHECKOUT PAGE (CheckoutController)                │
│  Route: GET /checkout/{class-slug}                                      │
│  ─────────────────────────────────────────────────────────────────────  │
│  ✓ Check class availability                                             │
│  ✓ Check user doesn't already have booking                              │
│  ✓ Display order summary                                                │
│  ✓ Show Stripe Elements card form                                       │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
                      User enters card details
                      User clicks "Pay Now"
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                  3. CREATE PAYMENT INTENT (AJAX)                        │
│  Route: POST /checkout/payment-intent                                   │
│  ─────────────────────────────────────────────────────────────────────  │
│  JavaScript (checkout.js)                                               │
│    │                                                                     │
│    ├─▶ Sends request to server with class ID                            │
│    │                                                                     │
│  Backend (CheckoutController::createPaymentIntent)                      │
│    │                                                                     │
│    ├─▶ Verify class availability                                        │
│    ├─▶ Check for duplicate bookings                                     │
│    ├─▶ Call StripeService::createPaymentIntent()                        │
│    │                                                                     │
│  StripeService                                                          │
│    │                                                                     │
│    ├─▶ Create PaymentIntent via Stripe API                              │
│    │   - amount: class price in cents                                   │
│    │   - description: "FrizzBoss - Class Title"                         │
│    │   - metadata: user_id, class_id, user_email                        │
│    │                                                                     │
│    └─▶ Return client_secret                                             │
│                                                                          │
│  Response: { "clientSecret": "pi_xxx_secret_xxx" }                      │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                  4. CONFIRM PAYMENT (Stripe.js)                         │
│  JavaScript (checkout.js)                                               │
│  ─────────────────────────────────────────────────────────────────────  │
│  stripe.confirmCardPayment(clientSecret, {                              │
│    payment_method: {                                                    │
│      card: cardElement                                                  │
│    }                                                                    │
│  })                                                                     │
│                                                                          │
│  ───▶ Stripe securely processes payment                                 │
│                                                                          │
│  ◀─── Returns: { paymentIntent: { status: 'succeeded' } }               │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                    ┌───────────────┴───────────────┐
                    │                               │
                    ▼                               ▼
┌──────────────────────────────┐    ┌──────────────────────────────────┐
│  5A. WEBHOOK (Background)    │    │  5B. FRONTEND (User sees)        │
│  Route: POST /webhook/stripe │    │  JavaScript polls for booking    │
│  ──────────────────────────  │    │  ──────────────────────────────  │
│                              │    │                                  │
│  Stripe sends webhook:       │    │  Polls: GET /my-bookings         │
│  payment_intent.succeeded    │    │    ?check_payment={pi_id}        │
│                              │    │                                  │
│  PaymentController           │    │  Waits for booking to be         │
│    │                         │    │  created by webhook              │
│    ├─▶ Verify signature      │    │                                  │
│    ├─▶ Extract metadata      │    │  Max 10 attempts, 1s apart       │
│    │                         │    │                                  │
│    ├─▶ Begin Transaction     │    │  Once found:                     │
│    │                         │    │    │                             │
│    ├─▶ Create Booking:       │    │    └─▶ Redirect to success      │
│    │   - user_id             │    │                                  │
│    │   - art_class_id        │    │                                  │
│    │   - ticket_code: FB-XXXX│◀───┼────────────────┐                │
│    │   - payment_status:     │    │                │                │
│    │     'completed'         │    │                │                │
│    │   - attendance_status:  │    │                │                │
│    │     'booked'            │    │                │                │
│    │                         │    │                │                │
│    ├─▶ Create Payment:       │    │                │                │
│    │   - booking_id          │    │                │                │
│    │   - stripe_payment_     │    │                │                │
│    │     intent_id           │    │                │                │
│    │   - amount_cents        │    │                │                │
│    │   - status: 'succeeded' │    │                │                │
│    │   - Calculate fees      │    │                │                │
│    │                         │    │                │                │
│    ├─▶ Commit Transaction    │    │                │                │
│    │                         │    │                │                │
│    └─▶ Log success           │    │                │                │
│                              │    │                │                │
│  Response: 200 OK            │    │                │                │
└──────────────────────────────┘    └────────────────┼────────────────┘
                                                     │
                                                     ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                      6. SUCCESS PAGE                                    │
│  Route: GET /checkout/success/{booking-id}                              │
│  ─────────────────────────────────────────────────────────────────────  │
│  CheckoutController::success                                            │
│    │                                                                     │
│    ├─▶ Verify booking belongs to authenticated user                     │
│    ├─▶ Load booking with artClass and payment                           │
│    └─▶ Display success page                                             │
│                                                                          │
│  Displays:                                                              │
│    ✓ Success icon and message                                           │
│    ✓ Ticket code (FB-XXXX) - large and prominent                        │
│    ✓ Class details (date, time, location)                               │
│    ✓ Payment summary                                                    │
│    ✓ Next steps instructions                                            │
│    ✓ Links to "My Bookings" and "Browse Classes"                        │
└─────────────────────────────────────────────────────────────────────────┘
```

## Error Handling Flows

### Payment Failed
```
User enters card → Payment fails at Stripe
                        │
                        ▼
            Show error message to user
                        │
                        ▼
            Allow user to retry with different card
```

### Webhook Failed
```
Payment succeeds → Webhook fails to create booking
                        │
                        ▼
            Logged in Stripe Dashboard + Laravel logs
                        │
                        ▼
            Frontend polling times out
                        │
                        ▼
            Fallback: Redirect to /my-bookings
                        │
                        ▼
            Admin manually reviews failed webhook
```

### Class Full During Payment
```
User starts checkout → Payment intent created
                        │
                        ▼
                Class fills up (other user books last spot)
                        │
                        ▼
                Webhook receives payment success
                        │
                        ▼
                Webhook still creates booking
                (Class can have overbooked state - admin reviews)
```

## Database Transaction Flow

```
Webhook receives payment_intent.succeeded
    │
    ├─▶ BEGIN TRANSACTION
    │
    ├─▶ Check for duplicate booking (by payment_intent_id)
    │   │
    │   ├─▶ If exists: COMMIT and return early
    │   │
    │   └─▶ If not exists: Continue
    │
    ├─▶ Create Booking record
    │   └─▶ Auto-generates ticket_code (FB-XXXX)
    │
    ├─▶ Create Payment record
    │   ├─▶ Link to booking
    │   ├─▶ Store Stripe IDs
    │   └─▶ Calculate fees
    │
    ├─▶ Save payment with fees
    │
    ├─▶ COMMIT TRANSACTION ✓
    │
    └─▶ Log success

If any step fails:
    └─▶ ROLLBACK TRANSACTION
        └─▶ Log error
```

## Security Checks

```
┌─────────────────────────────────┐
│  Checkout Access Check          │
│  ─────────────────────────────  │
│  ✓ User authenticated           │
│  ✓ Class exists & published     │
│  ✓ Class not in past            │
│  ✓ Class not full               │
│  ✓ User has no existing booking │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│  Payment Intent Creation        │
│  ─────────────────────────────  │
│  ✓ CSRF token validated         │
│  ✓ User authenticated           │
│  ✓ Class ID valid               │
│  ✓ Class still available        │
│  ✓ Double-check no duplicate    │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│  Webhook Processing             │
│  ─────────────────────────────  │
│  ✓ Stripe signature verified    │
│  ✓ Event type validated         │
│  ✓ Duplicate webhook prevented  │
│  ✓ Database transaction used    │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│  Success Page Access            │
│  ─────────────────────────────  │
│  ✓ User authenticated           │
│  ✓ Booking exists               │
│  ✓ Booking belongs to user      │
└─────────────────────────────────┘
```

## Key Components

### Frontend (checkout.js)
- Initializes Stripe.js
- Creates and mounts card Element
- Handles form submission
- Creates payment intent via AJAX
- Confirms payment with Stripe
- Polls for booking creation
- Redirects to success

### Backend (Controllers)
- **CheckoutController:** Manages checkout flow
- **PaymentController:** Processes webhooks

### Service Layer
- **StripeService:** Wraps Stripe API calls

### Models
- **Booking:** Auto-generates ticket codes
- **Payment:** Calculates Stripe fees

## Routes Summary

```
┌─────────────────────────────────────────────────────────────────┐
│  Route                          │  Auth  │  CSRF  │  Method    │
├─────────────────────────────────┼────────┼────────┼────────────┤
│  /checkout/{slug}               │   ✓    │   ✓    │  GET       │
│  /checkout/payment-intent       │   ✓    │   ✓    │  POST      │
│  /checkout/success/{booking}    │   ✓    │   ✓    │  GET       │
│  /webhook/stripe                │   ✗    │   ✗    │  POST      │
│  /my-bookings                   │   ✓    │   ✓    │  GET       │
└─────────────────────────────────────────────────────────────────┘
```

## Timeline Example

```
Time    Event
────────────────────────────────────────────────────────────────
0:00    User clicks "Book Your Spot Now"
0:01    Checkout page loads
0:15    User enters card details
0:16    User clicks "Pay Now"
0:17    AJAX creates payment intent
0:18    Stripe confirms payment
0:19    Webhook received (background)
0:20    Booking + Payment created
0:21    Frontend polls (attempt 1) - not found
0:22    Frontend polls (attempt 2) - FOUND!
0:23    Redirect to success page
0:24    Success page displays with ticket code
```

## Summary

This integration provides a **complete, secure, and production-ready** payment system with:
- Seamless user experience
- Robust error handling
- Webhook-based automation
- Security best practices
- Beautiful UI/UX
- Comprehensive logging
