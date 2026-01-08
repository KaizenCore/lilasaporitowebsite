<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    /**
     * Display a listing of all art classes.
     */
    public function index()
    {
        $classes = ArtClass::with('creator')
            ->withCount('bookings')
            ->orderBy('class_date', 'desc')
            ->paginate(15);

        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new art class.
     */
    public function create()
    {
        return view('admin.classes.create');
    }

    /**
     * Store a newly created art class in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'materials_included' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'class_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:30|max:480',
            'price_cents' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('class-images', 'public');
        }

        // Handle gallery images upload
        $galleryImages = null;
        if ($request->hasFile('gallery_images')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryPaths[] = $image->store('class-images/gallery', 'public');
            }
            $galleryImages = json_encode($galleryPaths);
        }

        // Convert price from dollars to cents if needed
        $priceCents = $validated['price_cents'];

        // Create the class
        $artClass = ArtClass::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'short_description' => $validated['short_description'] ?? null,
            'materials_included' => $validated['materials_included'] ?? null,
            'image_path' => $imagePath,
            'gallery_images' => $galleryImages,
            'class_date' => $validated['class_date'],
            'duration_minutes' => $validated['duration_minutes'],
            'price_cents' => $priceCents,
            'capacity' => $validated['capacity'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Art class created successfully!');
    }

    /**
     * Display the specified art class.
     */
    public function show(ArtClass $class)
    {
        $class->load(['bookings.user', 'bookings.payment']);

        return view('admin.classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified art class.
     */
    public function edit(ArtClass $class)
    {
        return view('admin.classes.edit', compact('class'));
    }

    /**
     * Update the specified art class in storage.
     */
    public function update(Request $request, ArtClass $class)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'materials_included' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_gallery_images' => 'nullable|array',
            'class_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:30|max:480',
            'price_cents' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'status' => 'required|in:draft,published,cancelled',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($class->image_path) {
                Storage::disk('public')->delete($class->image_path);
            }

            $validated['image_path'] = $request->file('image')->store('class-images', 'public');
        }

        // Handle gallery images
        $currentGallery = $class->gallery_images ? json_decode($class->gallery_images, true) : [];

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
                $currentGallery[] = $image->store('class-images/gallery', 'public');
            }
        }

        $validated['gallery_images'] = !empty($currentGallery) ? json_encode(array_values($currentGallery)) : null;

        // Update slug if title changed
        if ($validated['title'] !== $class->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Update the class
        $class->update($validated);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Art class updated successfully!');
    }

    /**
     * Remove the specified art class from storage.
     */
    public function destroy(ArtClass $class)
    {
        // Check if class has bookings
        if ($class->bookings()->where('payment_status', 'completed')->count() > 0) {
            return redirect()
                ->route('admin.classes.index')
                ->with('error', 'Cannot delete class with confirmed bookings. Cancel the class instead.');
        }

        // Delete image if exists
        if ($class->image_path) {
            Storage::disk('public')->delete($class->image_path);
        }

        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Art class deleted successfully!');
    }
}
