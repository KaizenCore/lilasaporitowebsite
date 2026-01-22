<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $cartService;
    protected $stripeService;

    public function __construct(CartService $cartService, StripeService $stripeService)
    {
        $this->cartService = $cartService;
        $this->stripeService = $stripeService;
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cart = $this->cartService->getWithProducts();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Validate stock
        $errors = $this->cartService->validateStock();
        if (!empty($errors)) {
            return redirect()->route('cart.index')->with('error', implode(' ', $errors));
        }

        $subtotal = $this->cartService->subtotal();
        $count = $this->cartService->count();

        return view('checkout.order', compact('cart', 'subtotal', 'count'));
    }

    /**
     * Create payment intent for order
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $cart = $this->cartService->getWithProducts();

            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            // Validate stock
            $errors = $this->cartService->validateStock();
            if (!empty($errors)) {
                return response()->json(['error' => implode(' ', $errors)], 400);
            }

            $subtotal = $this->cartService->subtotal();

            // Prepare cart data for metadata
            $cartItems = [];
            foreach ($cart as $productId => $item) {
                $cartItems[] = [
                    'product_id' => $productId,
                    'title' => $item['title'],
                    'quantity' => $item['quantity'],
                    'price_cents' => $item['price_cents'],
                ];
            }

            // Create payment intent
            $metadata = [
                'order_type' => 'store',
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'cart_items' => json_encode($cartItems),
                'item_count' => $this->cartService->count(),
            ];

            $paymentIntent = $this->stripeService->createPaymentIntent(
                $subtotal,
                'FrizzBoss Store Order',
                $metadata
            );

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show order confirmation page
     */
    public function success(Order $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.order-success', compact('order'));
    }

    /**
     * Check if order has been created for payment intent (used for polling)
     */
    public function checkOrderStatus($paymentIntentId)
    {
        $order = Order::whereHas('payment', function ($query) use ($paymentIntentId) {
            $query->where('stripe_payment_intent_id', $paymentIntentId);
        })->first();

        if ($order && $order->user_id === Auth::id()) {
            return response()->json([
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);
        }

        return response()->json(['order_id' => null], 404);
    }
}
