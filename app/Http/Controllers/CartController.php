<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart
     */
    public function index()
    {
        $cart = $this->cartService->getWithProducts();
        $subtotal = $this->cartService->subtotal();
        $count = $this->cartService->count();

        // Validate stock
        $errors = $this->cartService->validateStock();

        return view('cart.index', compact('cart', 'subtotal', 'count', 'errors'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        try {
            $quantity = $validated['quantity'] ?? 1;
            $this->cartService->add($validated['product_id'], $quantity);

            return redirect()->back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        try {
            $this->cartService->update($validated['product_id'], $validated['quantity']);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'count' => $this->cartService->count(),
                    'subtotal' => $this->cartService->subtotal(),
                ]);
            }

            return redirect()->route('cart.index')->with('success', 'Cart updated!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
        try {
            $this->cartService->remove($productId);

            return redirect()->route('cart.index')->with('success', 'Product removed from cart!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $this->cartService->clear();

        return redirect()->route('store.index')->with('success', 'Cart cleared!');
    }
}
