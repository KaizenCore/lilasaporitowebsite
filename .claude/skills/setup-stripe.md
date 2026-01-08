# Setup Stripe Payment Integration Skill

This skill sets up Stripe payment processing for the FizzBoss booking platform.

## What This Skill Does

1. Install Stripe dependencies
2. Configure Stripe API keys
3. Create payment API routes
4. Set up webhook handling
5. Create payment flow components
6. Test payment integration

## Prerequisites

- Next.js project initialized
- Stripe account created (https://dashboard.stripe.com/register)

## Steps

### 1. Install Dependencies

```bash
npm install stripe @stripe/stripe-js @stripe/react-stripe-js
```

### 2. Add Environment Variables

Add to `.env`:
```env
STRIPE_PUBLIC_KEY=pk_test_your_public_key
STRIPE_SECRET_KEY=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret
```

Add to `.env.example`:
```env
STRIPE_PUBLIC_KEY=pk_test_...
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

### 3. Create Stripe Client

**lib/stripe.ts**:
```typescript
import Stripe from 'stripe'

export const stripe = new Stripe(process.env.STRIPE_SECRET_KEY!, {
  apiVersion: '2024-11-20.acacia',
  typescript: true,
})
```

**lib/stripe-client.ts**:
```typescript
import { loadStripe } from '@stripe/stripe-js'

export const getStripe = () => {
  return loadStripe(process.env.NEXT_PUBLIC_STRIPE_PUBLIC_KEY!)
}
```

### 4. Create Payment Intent API

**app/api/payments/create-intent/route.ts**:
```typescript
import { NextRequest, NextResponse } from 'next/server'
import { stripe } from '@/lib/stripe'
import { prisma } from '@/lib/db'
import { auth } from '@/lib/auth'
import { z } from 'zod'

const schema = z.object({
  classId: z.string(),
})

export async function POST(request: NextRequest) {
  try {
    const session = await auth()
    if (!session?.user) {
      return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })
    }

    const body = await request.json()
    const { classId } = schema.parse(body)

    // Get class details
    const classItem = await prisma.class.findUnique({
      where: { id: classId },
      include: {
        _count: { select: { tickets: true } }
      }
    })

    if (!classItem) {
      return NextResponse.json({ error: 'Class not found' }, { status: 404 })
    }

    if (classItem._count.tickets >= classItem.capacity) {
      return NextResponse.json({ error: 'Class is full' }, { status: 400 })
    }

    // Create payment intent
    const paymentIntent = await stripe.paymentIntents.create({
      amount: Math.round(Number(classItem.price) * 100), // Convert to cents
      currency: 'usd',
      metadata: {
        classId,
        userId: session.user.id,
        className: classItem.title,
      },
    })

    return NextResponse.json({
      clientSecret: paymentIntent.client_secret,
      amount: classItem.price,
    })
  } catch (error) {
    console.error('Payment intent error:', error)
    return NextResponse.json(
      { error: 'Failed to create payment intent' },
      { status: 500 }
    )
  }
}
```

### 5. Create Webhook Handler

**app/api/webhooks/stripe/route.ts**:
```typescript
import { NextRequest, NextResponse } from 'next/server'
import { headers } from 'next/headers'
import { stripe } from '@/lib/stripe'
import { prisma } from '@/lib/db'
import Stripe from 'stripe'
import { sendTicketEmail } from '@/lib/email'

export async function POST(request: NextRequest) {
  const body = await request.text()
  const signature = headers().get('stripe-signature')!

  let event: Stripe.Event

  try {
    event = stripe.webhooks.constructEvent(
      body,
      signature,
      process.env.STRIPE_WEBHOOK_SECRET!
    )
  } catch (error) {
    console.error('Webhook signature verification failed:', error)
    return NextResponse.json({ error: 'Invalid signature' }, { status: 400 })
  }

  // Handle the event
  switch (event.type) {
    case 'payment_intent.succeeded':
      const paymentIntent = event.data.object as Stripe.PaymentIntent
      await handleSuccessfulPayment(paymentIntent)
      break

    case 'payment_intent.payment_failed':
      const failedPayment = event.data.object as Stripe.PaymentIntent
      await handleFailedPayment(failedPayment)
      break

    default:
      console.log(`Unhandled event type: ${event.type}`)
  }

  return NextResponse.json({ received: true })
}

async function handleSuccessfulPayment(paymentIntent: Stripe.PaymentIntent) {
  const { classId, userId } = paymentIntent.metadata

  try {
    // Create ticket in transaction
    const ticket = await prisma.$transaction(async (tx) => {
      // Create ticket
      const newTicket = await tx.ticket.create({
        data: {
          userId,
          classId,
          paymentStatus: 'COMPLETED',
          paymentIntentId: paymentIntent.id,
          amountPaid: paymentIntent.amount / 100,
        },
        include: {
          user: true,
          class: true,
        },
      })

      // Create invoice
      await tx.invoice.create({
        data: {
          ticketId: newTicket.id,
          invoiceNumber: `INV-${Date.now()}`,
          amount: newTicket.amountPaid,
        },
      })

      return newTicket
    })

    // Send confirmation email
    await sendTicketEmail(
      ticket.user.email,
      ticket.class.title,
      ticket.ticketCode
    )
  } catch (error) {
    console.error('Failed to create ticket:', error)
  }
}

async function handleFailedPayment(paymentIntent: Stripe.PaymentIntent) {
  console.log('Payment failed:', paymentIntent.id)
  // Handle failed payment (send email, log, etc.)
}
```

### 6. Create Payment Component

**components/payment/checkout-form.tsx**:
```typescript
'use client'

import { useState } from 'react'
import {
  PaymentElement,
  useStripe,
  useElements,
} from '@stripe/react-stripe-js'
import { Button } from '@/components/ui/button'

interface CheckoutFormProps {
  amount: number
  onSuccess?: () => void
}

export function CheckoutForm({ amount, onSuccess }: CheckoutFormProps) {
  const stripe = useStripe()
  const elements = useElements()
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()

    if (!stripe || !elements) return

    setIsLoading(true)
    setError(null)

    const { error: submitError } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        return_url: `${window.location.origin}/tickets?success=true`,
      },
    })

    if (submitError) {
      setError(submitError.message || 'Payment failed')
      setIsLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <PaymentElement />

      {error && (
        <div className="text-sm text-red-600 bg-red-50 p-3 rounded">
          {error}
        </div>
      )}

      <Button
        type="submit"
        disabled={!stripe || isLoading}
        className="w-full"
      >
        {isLoading ? 'Processing...' : `Pay $${amount}`}
      </Button>
    </form>
  )
}
```

### 7. Create Payment Page

**app/(public)/classes/[id]/checkout/page.tsx**:
```typescript
import { Elements } from '@stripe/react-stripe-js'
import { getStripe } from '@/lib/stripe-client'
import { CheckoutForm } from '@/components/payment/checkout-form'

export default async function CheckoutPage({
  params,
}: {
  params: { id: string }
}) {
  const response = await fetch(
    `${process.env.NEXT_PUBLIC_APP_URL}/api/payments/create-intent`,
    {
      method: 'POST',
      body: JSON.stringify({ classId: params.id }),
    }
  )

  const { clientSecret, amount } = await response.json()

  return (
    <div className="max-w-md mx-auto p-8">
      <h1 className="text-2xl font-bold mb-6">Complete Your Booking</h1>

      <Elements stripe={getStripe()} options={{ clientSecret }}>
        <CheckoutForm amount={amount} />
      </Elements>
    </div>
  )
}
```

### 8. Set Up Stripe Webhook Locally

For local testing:

```bash
# Install Stripe CLI
# https://stripe.com/docs/stripe-cli

# Login
stripe login

# Forward webhooks to local server
stripe listen --forward-to localhost:3000/api/webhooks/stripe

# Copy the webhook signing secret (whsec_...) to .env
```

### 9. Test Payment Flow

Create test payment:

```bash
# Use Stripe test card
Card: 4242 4242 4242 4242
Expiry: Any future date
CVC: Any 3 digits
ZIP: Any 5 digits
```

Test webhook:
```bash
stripe trigger payment_intent.succeeded
```

## Email Setup (Resend)

**lib/email.ts**:
```typescript
import { Resend } from 'resend'

const resend = new Resend(process.env.RESEND_API_KEY)

export async function sendTicketEmail(
  email: string,
  className: string,
  ticketCode: string
) {
  await resend.emails.send({
    from: 'FizzBoss <noreply@yourdomain.com>',
    to: email,
    subject: `Your ticket for ${className}`,
    html: `
      <h1>Your Art Class Ticket</h1>
      <p>Thanks for booking ${className}!</p>
      <p><strong>Your ticket code:</strong> ${ticketCode}</p>
      <p>Show this code when you arrive at class.</p>
    `,
  })
}
```

## Production Checklist

- [ ] Switch to live Stripe keys
- [ ] Set up production webhook endpoint
- [ ] Configure Resend domain
- [ ] Test full payment flow
- [ ] Set up error monitoring
- [ ] Configure refund handling
- [ ] Add receipt generation

## Troubleshooting

**Webhook not working:**
- Verify STRIPE_WEBHOOK_SECRET is set
- Check Stripe CLI is forwarding
- Look at Stripe dashboard logs

**Payment fails:**
- Check Stripe dashboard for details
- Verify test card details
- Check browser console for errors

**Email not sending:**
- Verify RESEND_API_KEY
- Check email logs in Resend dashboard
- Verify sender domain
