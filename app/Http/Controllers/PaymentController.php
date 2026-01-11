<?php

namespace App\Http\Controllers;

use App\Mail\BookingConfirmation;
use App\Models\ArtClass;
use App\Models\Booking;
use App\Models\ClassBookingOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\ClassCartService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');

        try {
            // Verify webhook signature
            $event = $this->stripeService->verifyWebhookSignature($payload, $signature);
        } catch (\Exception $e) {
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled webhook event type', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        Log::info('Payment intent succeeded', [
            'payment_intent_id' => $paymentIntent->id,
            'metadata' => $paymentIntent->metadata,
        ]);

        DB::beginTransaction();

        try {
            $metadata = $paymentIntent->metadata;

            // Check order type
            if (isset($metadata->order_type)) {
                if ($metadata->order_type === 'store') {
                    $this->createStoreOrder($paymentIntent);
                } elseif ($metadata->order_type === 'class_booking') {
                    $this->createMultiClassBooking($paymentIntent);
                } else {
                    $this->createClassBooking($paymentIntent);
                }
            } else {
                $this->createClassBooking($paymentIntent);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process payment from webhook', [
                'payment_intent_id' => $paymentIntent->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Create a class booking from payment intent
     */
    protected function createClassBooking($paymentIntent)
    {
        $metadata = $paymentIntent->metadata;

        // Get the art class
        $artClass = ArtClass::findOrFail($metadata->art_class_id);

        // Check if booking already exists (prevent duplicates)
        $existingBooking = Booking::whereHas('payment', function ($query) use ($paymentIntent) {
            $query->where('stripe_payment_intent_id', $paymentIntent->id);
        })->first();

        if ($existingBooking) {
            Log::info('Booking already exists for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'booking_id' => $existingBooking->id,
            ]);
            return;
        }

        // Create the booking
        $booking = Booking::create([
            'user_id' => $metadata->user_id,
            'art_class_id' => $metadata->art_class_id,
            'payment_status' => 'completed',
            'attendance_status' => 'booked',
        ]);

        // Create the payment record
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
            'stripe_customer_id' => $paymentIntent->customer ?? null,
            'amount_cents' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
            'status' => 'succeeded',
            'metadata' => [
                'class_title' => $metadata->class_title,
                'class_date' => $metadata->class_date,
                'user_email' => $metadata->user_email,
            ],
        ]);

        // Calculate Stripe fees
        $payment->calculateStripeFee();
        $payment->save();

        Log::info('Booking and payment created successfully', [
            'booking_id' => $booking->id,
            'payment_id' => $payment->id,
            'ticket_code' => $booking->ticket_code,
        ]);

        // Send confirmation email
        try {
            $booking->load('artClass', 'user');
            Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
            Log::info('Booking confirmation email sent via webhook', ['booking_id' => $booking->id]);
        } catch (\Throwable $e) {
            Log::error('Failed to send booking confirmation email via webhook', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create multiple class bookings from payment intent (multi-class cart checkout)
     */
    protected function createMultiClassBooking($paymentIntent)
    {
        $metadata = $paymentIntent->metadata;

        // Check if order already exists (prevent duplicates)
        $existingOrder = ClassBookingOrder::whereHas('payment', function ($query) use ($paymentIntent) {
            $query->where('stripe_payment_intent_id', $paymentIntent->id);
        })->first();

        if ($existingOrder) {
            Log::info('Class booking order already exists for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $existingOrder->id,
            ]);
            return;
        }

        // Parse cart items from metadata
        $cartItems = json_decode($metadata->cart_items, true);

        // Create the class booking order
        $order = ClassBookingOrder::create([
            'user_id' => $metadata->user_id,
            'email' => $metadata->user_email,
            'total_amount_cents' => $paymentIntent->amount,
            'subtotal_cents' => $paymentIntent->amount,
            'payment_status' => 'completed',
        ]);

        // Create individual bookings for each class
        $bookings = [];
        foreach ($cartItems as $item) {
            $artClass = ArtClass::find($item['art_class_id']);

            if (!$artClass) {
                Log::warning('Art class not found for booking', [
                    'art_class_id' => $item['art_class_id'],
                    'order_id' => $order->id,
                ]);
                continue;
            }

            // Create booking for this class
            $booking = Booking::create([
                'user_id' => $metadata->user_id,
                'art_class_id' => $item['art_class_id'],
                'class_booking_order_id' => $order->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ]);

            $bookings[] = $booking;

            Log::info('Booking created for multi-class order', [
                'booking_id' => $booking->id,
                'ticket_code' => $booking->ticket_code,
                'art_class_id' => $item['art_class_id'],
            ]);
        }

        // Create the payment record
        $payment = Payment::create([
            'class_booking_order_id' => $order->id,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
            'stripe_customer_id' => $paymentIntent->customer ?? null,
            'amount_cents' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
            'status' => 'succeeded',
            'metadata' => [
                'order_number' => $order->order_number,
                'class_count' => $metadata->class_count,
            ],
        ]);

        // Calculate Stripe fees
        $payment->calculateStripeFee();
        $payment->save();

        Log::info('Multi-class booking order created successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'booking_count' => count($bookings),
            'payment_id' => $payment->id,
        ]);

        // Clear the class cart for this user
        try {
            app(ClassCartService::class)->clear();
        } catch (\Throwable $e) {
            Log::warning('Could not clear class cart (may be called from webhook)', [
                'error' => $e->getMessage(),
            ]);
        }

        // Send confirmation emails for each booking
        foreach ($bookings as $booking) {
            try {
                $booking->load('artClass', 'user');
                Mail::to($booking->user->email)->send(new BookingConfirmation($booking));
                Log::info('Booking confirmation email sent for multi-class order', ['booking_id' => $booking->id]);
            } catch (\Throwable $e) {
                Log::error('Failed to send booking confirmation email for multi-class order', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Create a store order from payment intent
     */
    protected function createStoreOrder($paymentIntent)
    {
        $metadata = $paymentIntent->metadata;

        // Check if order already exists (prevent duplicates)
        $existingOrder = Order::whereHas('payment', function ($query) use ($paymentIntent) {
            $query->where('stripe_payment_intent_id', $paymentIntent->id);
        })->first();

        if ($existingOrder) {
            Log::info('Order already exists for payment intent', [
                'payment_intent_id' => $paymentIntent->id,
                'order_id' => $existingOrder->id,
            ]);
            return;
        }

        // Parse cart items from metadata
        $cartItems = json_decode($metadata->cart_items, true);

        // Create the order
        $order = Order::create([
            'user_id' => $metadata->user_id,
            'email' => $metadata->user_email,
            'total_amount_cents' => $paymentIntent->amount,
            'subtotal_cents' => $paymentIntent->amount,
            'payment_status' => 'completed',
            'fulfillment_status' => 'unfulfilled',
        ]);

        // Create order items and process stock
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                Log::warning('Product not found for order item', [
                    'product_id' => $item['product_id'],
                    'order_id' => $order->id,
                ]);
                continue;
            }

            // Create order item
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_title' => $item['title'],
                'product_type' => $product->product_type,
                'quantity' => $item['quantity'],
                'price_cents' => $item['price_cents'],
                'total_cents' => $item['price_cents'] * $item['quantity'],
            ]);

            // Decrement stock for physical products
            if ($product->product_type === 'physical' && !is_null($product->stock_quantity)) {
                $product->decrementStock($item['quantity']);
            }

            // Generate download URL for digital products
            if ($product->product_type === 'digital') {
                $token = \Illuminate\Support\Str::random(64);
                $orderItem->update(['digital_download_url' => $token]);
            }
        }

        // Create the payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
            'stripe_customer_id' => $paymentIntent->customer ?? null,
            'amount_cents' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'payment_method' => $paymentIntent->payment_method_types[0] ?? 'card',
            'status' => 'succeeded',
            'metadata' => [
                'order_number' => $order->order_number,
                'item_count' => $metadata->item_count,
            ],
        ]);

        // Calculate Stripe fees
        $payment->calculateStripeFee();
        $payment->save();

        Log::info('Order and payment created successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_id' => $payment->id,
        ]);

        // Here you could dispatch an event to send confirmation email
        // event(new OrderConfirmed($order));
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        Log::warning('Payment intent failed', [
            'payment_intent_id' => $paymentIntent->id,
            'error' => $paymentIntent->last_payment_error->message ?? 'Unknown error',
            'metadata' => $paymentIntent->metadata,
        ]);

        // Find existing payment record if any and mark as failed
        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->markAsFailed($paymentIntent->last_payment_error->message ?? 'Payment failed');
        }
    }
}
