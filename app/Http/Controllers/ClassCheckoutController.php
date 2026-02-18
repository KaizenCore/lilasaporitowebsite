<?php

namespace App\Http\Controllers;

use App\Models\ClassBookingOrder;
use App\Services\ClassCartService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassCheckoutController extends Controller
{
    protected $classCartService;
    protected $stripeService;

    public function __construct(ClassCartService $classCartService, StripeService $stripeService)
    {
        $this->middleware('auth');
        $this->classCartService = $classCartService;
        $this->stripeService = $stripeService;
    }

    /**
     * Show checkout page for classes
     */
    public function checkout()
    {
        $cart = $this->classCartService->getWithClasses();

        if (empty($cart)) {
            return redirect()->route('class-cart.index')->with('error', 'Your class cart is empty');
        }

        // Validate availability
        $errors = $this->classCartService->validate();
        if (!empty($errors)) {
            return redirect()->route('class-cart.index')->with('error', implode(' ', $errors));
        }

        $subtotal = $this->classCartService->subtotal();
        $count = $this->classCartService->count();

        return view('class-checkout.index', compact('cart', 'subtotal', 'count'));
    }

    /**
     * Create payment intent for class order
     */
    public function createPaymentIntent(Request $request)
    {
        try {
            $cart = $this->classCartService->getWithClasses();

            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            // Validate availability
            $errors = $this->classCartService->validate();
            if (!empty($errors)) {
                return response()->json(['error' => implode(' ', $errors)], 400);
            }

            $subtotal = $this->classCartService->subtotal();

            // Prepare cart data for metadata
            $cartItems = [];
            foreach ($cart as $classId => $item) {
                $cartItems[] = [
                    'art_class_id' => $classId,
                    'title' => $item['title'],
                    'class_date' => $item['class_date'],
                    'price_cents' => $item['price_cents'],
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }

            // Create payment intent
            $metadata = [
                'order_type' => 'class_booking',
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email,
                'cart_items' => json_encode($cartItems),
                'class_count' => $this->classCartService->count(),
            ];

            $paymentIntent = $this->stripeService->createPaymentIntent(
                $subtotal,
                'FrizzBoss Class Booking',
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
    public function success(ClassBookingOrder $order)
    {
        // Verify order belongs to current user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Load bookings with their art classes
        $order->load('bookings.artClass');

        return view('class-checkout.success', compact('order'));
    }

    /**
     * Check if order has been created for payment intent (used for polling)
     */
    public function checkOrderStatus($paymentIntentId)
    {
        $order = ClassBookingOrder::whereHas('payment', function ($query) use ($paymentIntentId) {
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
