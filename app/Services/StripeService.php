<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Exception;
use Illuminate\Support\Facades\Log;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create a payment intent for a booking
     *
     * @param int $amount Amount in cents
     * @param string $description Payment description
     * @param array $metadata Additional metadata
     * @return PaymentIntent
     * @throws Exception
     */
    public function createPaymentIntent($amount, $description, $metadata = [])
    {
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'description' => $description,
                'metadata' => $metadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            return $paymentIntent;
        } catch (Exception $e) {
            Log::error('Stripe Payment Intent Creation Failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'metadata' => $metadata,
            ]);

            throw new Exception('Failed to create payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a payment intent by ID
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws Exception
     */
    public function retrievePaymentIntent($paymentIntentId)
    {
        try {
            return PaymentIntent::retrieve($paymentIntentId);
        } catch (Exception $e) {
            Log::error('Stripe Payment Intent Retrieval Failed', [
                'error' => $e->getMessage(),
                'payment_intent_id' => $paymentIntentId,
            ]);

            throw new Exception('Failed to retrieve payment intent: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return \Stripe\Event
     * @throws Exception
     */
    public function verifyWebhookSignature($payload, $signature)
    {
        try {
            return \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (Exception $e) {
            Log::error('Stripe Webhook Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Invalid webhook signature');
        }
    }
}
