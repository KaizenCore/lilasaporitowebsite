<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display all products
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->published()
            ->inStock();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by product type
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Get products with pagination
        $products = $query->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get categories for filtering
        $categories = ProductCategory::withCount('products')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        // Get featured products if on main page
        $featuredProducts = Product::published()
            ->inStock()
            ->featured()
            ->limit(4)
            ->get();

        return view('store.index', compact('products', 'categories', 'featuredProducts'));
    }

    /**
     * Show individual product
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->published()
            ->with('category')
            ->firstOrFail();

        // Get related products from same category
        $relatedProducts = Product::published()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('store.show', compact('product', 'relatedProducts'));
    }

    /**
     * Show products by category
     */
    public function category($slug)
    {
        $category = ProductCategory::where('slug', $slug)->firstOrFail();

        $products = Product::published()
            ->inStock()
            ->where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = ProductCategory::withCount('products')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('store.category', compact('category', 'products', 'categories'));
    }
}
