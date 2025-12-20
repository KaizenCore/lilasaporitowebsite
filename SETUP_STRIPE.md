# Quick Setup Guide - Stripe Payment Integration

## Prerequisites
- Stripe account (sign up at https://stripe.com)
- stripe/stripe-php package installed (already done)
- Node.js and npm installed

## Step-by-Step Setup

### 1. Get Your Stripe Keys

1. Log into [Stripe Dashboard](https://dashboard.stripe.com)
2. Click on "Developers" in the left menu
3. Click "API keys"
4. You'll see two keys:
   - **Publishable key** (starts with `pk_test_`)
   - **Secret key** (starts with `sk_test_`) - Click "Reveal" to see it

### 2. Update Environment Variables

Add these to your `.env` file:

```env
STRIPE_KEY=pk_test_YOUR_PUBLISHABLE_KEY_HERE
STRIPE_SECRET=sk_test_YOUR_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE
```

**Note:** The webhook secret will be obtained in step 4.

### 3. Build Frontend Assets

Run these commands to compile the checkout JavaScript:

```bash
npm install
npm run build
```

Or for development (with auto-reload):
```bash
npm run dev
```

### 4. Setup Stripe Webhook

#### For Production:

1. Go to [Stripe Webhooks](https://dashboard.stripe.com/webhooks)
2. Click "Add endpoint"
3. Enter your webhook URL: `https://your-domain.com/webhook/stripe`
4. Select "Events on your account"
5. Choose these events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
6. Click "Add endpoint"
7. Click on the newly created endpoint
8. Click "Reveal" under "Signing secret"
9. Copy the webhook secret (starts with `whsec_`)
10. Add it to your `.env` file as `STRIPE_WEBHOOK_SECRET`

#### For Local Testing:

Install Stripe CLI:
```bash
# macOS
brew install stripe/stripe-cli/stripe

# Windows
scoop bucket add stripe https://github.com/stripe/scoop-stripe-cli.git
scoop install stripe

# Or download from: https://github.com/stripe/stripe-cli/releases
```

Login and forward webhooks:
```bash
stripe login
stripe listen --forward-to http://localhost:8000/webhook/stripe
```

The CLI will output a webhook signing secret - use this in your `.env` file.

### 5. Clear Config Cache

After updating `.env`, clear Laravel's config cache:

```bash
php artisan config:clear
```

### 6. Test the Integration

1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. If testing locally, start Stripe webhook forwarding in another terminal:
   ```bash
   stripe listen --forward-to http://localhost:8000/webhook/stripe
   ```

3. Create a test class (or use existing one)

4. Navigate to the class details page

5. Click "Book Your Spot Now"

6. Use a test card:
   - Card Number: `4242 4242 4242 4242`
   - Expiry: Any future date (e.g., 12/25)
   - CVC: Any 3 digits (e.g., 123)
   - ZIP: Any 5 digits (e.g., 12345)

7. Submit payment

8. You should be redirected to success page with ticket code!

## Verification Checklist

After setup, verify:

- [ ] Stripe keys are in `.env` file
- [ ] Assets built successfully (`npm run build`)
- [ ] Config cache cleared (`php artisan config:clear`)
- [ ] Webhook endpoint configured in Stripe dashboard
- [ ] Webhook secret in `.env` file
- [ ] Test payment completes successfully
- [ ] Booking appears in "My Bookings"
- [ ] Success page shows ticket code
- [ ] Payment record created in database

## Test Card Numbers

**Successful payments:**
- `4242 4242 4242 4242` - Visa
- `5555 5555 5555 4444` - Mastercard
- `3782 822463 10005` - American Express

**Authentication required (3D Secure):**
- `4000 0025 0000 3155`

**Declined payments:**
- `4000 0000 0000 9995` - Insufficient funds
- `4000 0000 0000 9987` - Lost card

For all test cards:
- **Expiry:** Any future date
- **CVC:** Any 3 digits (4 for Amex)
- **ZIP:** Any 5 digits

## Common Issues

### Issue: "Stripe key not found"
**Solution:**
- Check `.env` file has `STRIPE_KEY` and `STRIPE_SECRET`
- Run `php artisan config:clear`
- Restart Laravel server

### Issue: "Webhook signature verification failed"
**Solution:**
- Verify `STRIPE_WEBHOOK_SECRET` in `.env`
- Ensure webhook secret matches Stripe dashboard
- For local testing, use Stripe CLI's webhook secret

### Issue: Payment succeeds but no booking created
**Solution:**
- Check webhook is configured correctly
- Verify webhook URL is accessible
- Check Laravel logs: `storage/logs/laravel.log`
- Ensure webhook secret is correct

### Issue: JavaScript errors on checkout page
**Solution:**
- Run `npm run build`
- Clear browser cache
- Check browser console for errors
- Verify assets compiled successfully

## Going Live

When ready for production:

1. Switch to live API keys in Stripe Dashboard
2. Update `.env` with live keys (start with `pk_live_` and `sk_live_`)
3. Create new webhook endpoint with production URL
4. Update `STRIPE_WEBHOOK_SECRET` with live webhook secret
5. Test with real card (charge yourself $1 to verify)
6. Set up proper error monitoring
7. Configure email notifications for customers

## Next Steps

Once payment integration is working:

1. Set up email notifications for booking confirmations
2. Add refund functionality for cancellations
3. Create admin dashboard for payment reports
4. Add payment history to user profile
5. Implement reminder emails before class

## Support

- **Stripe Documentation:** https://stripe.com/docs
- **Stripe Support:** https://support.stripe.com
- **Laravel Documentation:** https://laravel.com/docs

For detailed information, see `STRIPE_INTEGRATION_GUIDE.md`
