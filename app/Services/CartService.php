<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

class CartService
{
    const CART_SESSION_KEY = 'shopping_cart';

    /**
     * Add product to cart
     */
    public function add($productId, $quantity = 1)
    {
        $product = Product::published()->inStock()->findOrFail($productId);

        // Check stock for physical products
        if ($product->product_type === 'physical' && !is_null($product->stock_quantity)) {
            $currentQuantity = $this->getProductQuantity($productId);
            if (($currentQuantity + $quantity) > $product->stock_quantity) {
                throw new \Exception('Not enough stock available');
            }
        }

        $cart = $this->get();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price_cents' => $product->price_cents,
                'product_type' => $product->product_type,
                'image_path' => $product->image_path,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart[$productId];
    }

    /**
     * Update product quantity in cart
     */
    public function update($productId, $quantity)
    {
        $cart = $this->get();

        if (!isset($cart[$productId])) {
            throw new \Exception('Product not in cart');
        }

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        // Check stock for physical products
        $product = Product::find($productId);
        if ($product && $product->product_type === 'physical' && !is_null($product->stock_quantity)) {
            if ($quantity > $product->stock_quantity) {
                throw new \Exception('Not enough stock available');
            }
        }

        $cart[$productId]['quantity'] = $quantity;
        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart[$productId];
    }

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
        $cart = $this->get();
        unset($cart[$productId]);
        Session::put(self::CART_SESSION_KEY, $cart);

        return true;
    }

    /**
     * Get cart contents
     */
    public function get()
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Get cart with full product details
     */
    public function getWithProducts()
    {
        $cart = $this->get();
        $productIds = array_keys($cart);

        if (empty($productIds)) {
            return [];
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($cart as $productId => &$item) {
            if (isset($products[$productId])) {
                $product = $products[$productId];
                $item['product'] = $product;

                // Update price if it changed
                $item['price_cents'] = $product->price_cents;

                // Check if still in stock
                $item['in_stock'] = !$product->is_out_of_stock;

                // Check if still published
                $item['is_available'] = $product->status === 'published';
            } else {
                $item['is_available'] = false;
            }
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart;
    }

    /**
     * Get total item count
     */
    public function count()
    {
        $cart = $this->get();
        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Get subtotal in cents
     */
    public function subtotal()
    {
        $cart = $this->get();
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price_cents'] * $item['quantity'];
        }

        return $total;
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    /**
     * Validate cart stock availability
     */
    public function validateStock()
    {
        $cart = $this->getWithProducts();
        $errors = [];

        foreach ($cart as $productId => $item) {
            if (!$item['is_available']) {
                $errors[] = "{$item['title']} is no longer available";
                $this->remove($productId);
                continue;
            }

            if (!$item['in_stock']) {
                $errors[] = "{$item['title']} is out of stock";
                $this->remove($productId);
                continue;
            }

            // Check quantity for physical products
            if (isset($item['product']) && $item['product']->product_type === 'physical') {
                $product = $item['product'];
                if (!is_null($product->stock_quantity) && $item['quantity'] > $product->stock_quantity) {
                    $errors[] = "Only {$product->stock_quantity} of {$item['title']} available";

                    // Update to available quantity
                    if ($product->stock_quantity > 0) {
                        $this->update($productId, $product->stock_quantity);
                    } else {
                        $this->remove($productId);
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Get current quantity of a product in cart
     */
    protected function getProductQuantity($productId)
    {
        $cart = $this->get();
        return $cart[$productId]['quantity'] ?? 0;
    }
}
