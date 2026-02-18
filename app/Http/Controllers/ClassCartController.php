<?php

namespace App\Http\Controllers;

use App\Services\ClassCartService;
use Illuminate\Http\Request;

class ClassCartController extends Controller
{
    protected $classCartService;

    public function __construct(ClassCartService $classCartService)
    {
        $this->classCartService = $classCartService;
    }

    /**
     * Display class cart
     */
    public function index()
    {
        $cart = $this->classCartService->getWithClasses();
        $subtotal = $this->classCartService->subtotal();
        $count = $this->classCartService->count();

        // Validate availability
        $errors = $this->classCartService->validate();

        return view('class-cart.index', compact('cart', 'subtotal', 'count', 'errors'));
    }

    /**
     * Add class to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'art_class_id' => 'required|exists:art_classes,id',
            'quantity' => 'sometimes|integer|min:1|max:10',
        ]);

        try {
            $this->classCartService->add($validated['art_class_id'], $validated['quantity'] ?? 1);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'count' => $this->classCartService->count(),
                    'message' => 'Class added to cart!',
                ]);
            }

            return redirect()->back()->with('success', 'Class added to cart!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 400);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update quantity for a class in cart
     */
    public function updateQuantity(Request $request, $artClassId)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        try {
            $this->classCartService->updateQuantity($artClassId, $validated['quantity']);
            return redirect()->route('class-cart.index')->with('success', 'Quantity updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove class from cart
     */
    public function remove($artClassId)
    {
        try {
            $this->classCartService->remove($artClassId);

            return redirect()->route('class-cart.index')->with('success', 'Class removed from cart!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $this->classCartService->clear();

        return redirect()->route('classes.index')->with('success', 'Cart cleared!');
    }

    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        return response()->json([
            'count' => $this->classCartService->count(),
        ]);
    }
}
