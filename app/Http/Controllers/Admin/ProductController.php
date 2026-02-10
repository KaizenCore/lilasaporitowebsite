<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category', 'creator');

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->latest()->paginate(15);
        $categories = ProductCategory::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price_cents' => 'required|integer|min:0',
            'compare_at_price_cents' => 'nullable|integer|min:0',
            'product_type' => 'required|in:physical,digital,class_package',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'digital_file' => 'required_if:product_type,digital|file|max:51200|mimes:pdf,zip,png,jpg,jpeg,gif,mp3,mp4,wav,mov,psd,ai,eps,svg', // 50MB max
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'weight_grams' => 'nullable|integer|min:0',
            'requires_shipping' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        // Convert price from dollars to cents
        $validated['price_cents'] = (int) ($validated['price_cents']);
        if (isset($validated['compare_at_price_cents'])) {
            $validated['compare_at_price_cents'] = (int) ($validated['compare_at_price_cents']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images upload
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery_images'] = json_encode($galleryPaths);
        }

        // Handle digital file upload
        if ($request->hasFile('digital_file')) {
            $validated['digital_file_path'] = $request->file('digital_file')->store('downloads', 'private');
        }

        // Set created_by
        $validated['created_by'] = Auth::id();

        // Set requires_shipping based on product_type if not set
        if (!isset($validated['requires_shipping'])) {
            $validated['requires_shipping'] = $validated['product_type'] === 'physical';
        }

        // Convert boolean checkbox values
        $validated['is_featured'] = $request->has('is_featured');
        $validated['requires_shipping'] = $request->has('requires_shipping');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'creator', 'orderItems.order');
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:product_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price_cents' => 'required|integer|min:0',
            'compare_at_price_cents' => 'nullable|integer|min:0',
            'product_type' => 'required|in:physical,digital,class_package',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'remove_gallery_images' => 'nullable|array',
            'digital_file' => 'nullable|file|max:51200|mimes:pdf,zip,png,jpg,jpeg,gif,mp3,mp4,wav,mov,psd,ai,eps,svg',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'weight_grams' => 'nullable|integer|min:0',
            'requires_shipping' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        // Convert price from dollars to cents
        $validated['price_cents'] = (int) ($validated['price_cents']);
        if (isset($validated['compare_at_price_cents'])) {
            $validated['compare_at_price_cents'] = (int) ($validated['compare_at_price_cents']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Handle gallery images
        $currentGallery = $product->gallery_images ? json_decode($product->gallery_images, true) : [];

        // Remove selected gallery images
        if ($request->has('remove_gallery_images')) {
            foreach ($request->remove_gallery_images as $path) {
                Storage::disk('public')->delete($path);
                $currentGallery = array_filter($currentGallery, fn($img) => $img !== $path);
            }
        }

        // Add new gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $currentGallery[] = $image->store('products/gallery', 'public');
            }
        }

        $validated['gallery_images'] = !empty($currentGallery) ? json_encode(array_values($currentGallery)) : null;

        // Handle digital file upload
        if ($request->hasFile('digital_file')) {
            // Delete old file if exists
            if ($product->digital_file_path) {
                Storage::disk('private')->delete($product->digital_file_path);
            }
            $validated['digital_file_path'] = $request->file('digital_file')->store('downloads', 'private');
        }

        // Convert boolean checkbox values
        $validated['is_featured'] = $request->has('is_featured');
        $validated['requires_shipping'] = $request->has('requires_shipping');

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if product has orders
        if ($product->orderItems()->count() > 0) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Cannot delete product with existing orders. Use archive instead.');
        }

        // Delete image if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Delete digital file if exists
        if ($product->digital_file_path) {
            Storage::disk('private')->delete($product->digital_file_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
