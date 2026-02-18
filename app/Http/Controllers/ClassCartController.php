<?php

namespace App\Http\Controllers;

use App\Services\ClassCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
        $rawCart = $this->classCartService->get();
        Log::info('Cart page loaded', [
            'session_id' => Session::getId(),
            'user_id' => auth()->id(),
            'raw_cart_count' => count($rawCart),
            'raw_cart_keys' => array_keys($rawCart),
        ]);

        $cart = $this->classCartService->getWithClasses();
        $subtotal = $this->classCartService->subtotal();
        $count = $this->classCartService->count();

        // Validate availability
        $errors = $this->classCartService->validate();

        Log::info('Cart page after validate', [
            'cart_count' => $count,
            'errors' => $errors,
        ]);

        return view('class-cart.index', compact('cart', 'subtotal', 'count', 'errors'));
    }

    /**
     * Add class to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'art_class_id' => 'required|exists:art_classes,id',
        ]);

        try {
            $this->classCartService->add($validated['art_class_id']);

            Log::info('Class added to cart', [
                'session_id' => Session::getId(),
                'user_id' => auth()->id(),
                'art_class_id' => $validated['art_class_id'],
                'cart_count' => $this->classCartService->count(),
            ]);

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
