<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use App\Services\RecurringClassService;
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
            ->withCount(['bookings' => function ($query) {
                $query->where('attendance_status', '!=', 'cancelled');
            }])
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'class_date' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:30|max:480',
            'price_cents' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'location_public' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,cancelled',
            // Party pricing fields
            'is_party_event' => 'nullable|boolean',
            'small_party_price_cents' => 'nullable|integer|min:0',
            'small_party_size' => 'nullable|integer|min:1',
            'large_party_price_cents' => 'nullable|integer|min:0',
            'large_party_size' => 'nullable|integer|min:1',
            'additional_guest_price_cents' => 'nullable|integer|min:0',
            'max_party_size' => 'nullable|integer|min:1',
            'party_addons' => 'nullable|array',
            'party_addons.*.name' => 'nullable|string|max:255',
            'party_addons.*.price_cents' => 'nullable|integer|min:0',
            'party_addons.*.description' => 'nullable|string|max:500',
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

        // Process party addons - filter out empty ones
        $partyAddons = null;
        if ($request->boolean('is_party_event') && !empty($validated['party_addons'])) {
            $partyAddons = array_values(array_filter($validated['party_addons'], function ($addon) {
                return !empty($addon['name']) && isset($addon['price_cents']);
            }));
        }

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
            'location_public' => $validated['location_public'] ?? null,
            'status' => $validated['status'],
            'is_party_event' => $request->boolean('is_party_event'),
            'small_party_price_cents' => $validated['small_party_price_cents'] ?? null,
            'small_party_size' => $validated['small_party_size'] ?? null,
            'large_party_price_cents' => $validated['large_party_price_cents'] ?? null,
            'large_party_size' => $validated['large_party_size'] ?? null,
            'additional_guest_price_cents' => $validated['additional_guest_price_cents'] ?? null,
            'max_party_size' => $validated['max_party_size'] ?? null,
            'party_addons' => $partyAddons,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'remove_gallery_images' => 'nullable|array',
            'class_date' => 'required|date',
            'duration_minutes' => 'required|integer|min:30|max:480',
            'price_cents' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'location_public' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,cancelled',
            // Party pricing fields
            'is_party_event' => 'nullable|boolean',
            'small_party_price_cents' => 'nullable|integer|min:0',
            'small_party_size' => 'nullable|integer|min:1',
            'large_party_price_cents' => 'nullable|integer|min:0',
            'large_party_size' => 'nullable|integer|min:1',
            'additional_guest_price_cents' => 'nullable|integer|min:0',
            'max_party_size' => 'nullable|integer|min:1',
            'party_addons' => 'nullable|array',
            'party_addons.*.name' => 'nullable|string|max:255',
            'party_addons.*.price_cents' => 'nullable|integer|min:0',
            'party_addons.*.description' => 'nullable|string|max:500',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Only delete old file if no other class references it
            if ($class->image_path) {
                $sharedCount = ArtClass::where('image_path', $class->image_path)
                    ->where('id', '!=', $class->id)
                    ->count();
                if ($sharedCount === 0) {
                    Storage::disk('public')->delete($class->image_path);
                }
            }

            $validated['image_path'] = $request->file('image')->store('class-images', 'public');
        }

        // Handle gallery images
        $currentGallery = $class->gallery_images ? json_decode($class->gallery_images, true) : [];

        // Remove selected gallery images
        if ($request->has('remove_gallery_images')) {
            foreach ($request->remove_gallery_images as $path) {
                // Only delete file if no other class references it in gallery_images
                $sharedGallery = ArtClass::where('id', '!=', $class->id)
                    ->where('gallery_images', 'like', '%' . str_replace(['%', '_'], ['\\%', '\\_'], $path) . '%')
                    ->count();
                if ($sharedGallery === 0) {
                    Storage::disk('public')->delete($path);
                }
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

        // Handle party event fields
        $validated['is_party_event'] = $request->boolean('is_party_event');

        // Process party addons - filter out empty ones
        if ($request->boolean('is_party_event') && !empty($validated['party_addons'])) {
            $validated['party_addons'] = array_values(array_filter($validated['party_addons'], function ($addon) {
                return !empty($addon['name']) && isset($addon['price_cents']);
            }));
        } else {
            $validated['party_addons'] = null;
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
        // Check if class has active (non-cancelled) confirmed bookings
        if ($class->bookings()
            ->where('payment_status', 'completed')
            ->where('attendance_status', '!=', 'cancelled')
            ->count() > 0) {
            return redirect()
                ->route('admin.classes.index')
                ->with('error', 'Cannot delete class with confirmed bookings. Cancel the class instead.');
        }

        // Only delete image file if no other class references it
        if ($class->image_path) {
            $sharedCount = ArtClass::where('image_path', $class->image_path)
                ->where('id', '!=', $class->id)
                ->count();
            if ($sharedCount === 0) {
                Storage::disk('public')->delete($class->image_path);
            }
        }

        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Art class deleted successfully!');
    }

    /**
     * Show the form for generating recurring classes.
     */
    public function showRecurringForm(ArtClass $class)
    {
        return view('admin.classes.recurring', compact('class'));
    }

    /**
     * Preview dates for recurring class generation (AJAX).
     */
    public function previewRecurring(Request $request, ArtClass $class, RecurringClassService $recurringService)
    {
        $validated = $request->validate([
            'recurrence_type' => 'required|in:weekly,biweekly,custom',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'time' => 'required|date_format:H:i',
            'days_of_week' => 'required_if:recurrence_type,custom|array',
            'days_of_week.*' => 'integer|min:1|max:7',
        ]);

        $dates = $recurringService->previewDates([
            'recurrence_type' => $validated['recurrence_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'time' => $validated['time'],
            'days_of_week' => $validated['days_of_week'] ?? [],
        ]);

        return response()->json([
            'dates' => $dates,
            'count' => count($dates),
        ]);
    }

    /**
     * Generate recurring classes from template.
     */
    public function generateRecurring(Request $request, ArtClass $class, RecurringClassService $recurringService)
    {
        $validated = $request->validate([
            'recurrence_type' => 'required|in:weekly,biweekly,custom',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'time' => 'required|date_format:H:i',
            'days_of_week' => 'required_if:recurrence_type,custom|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'series_name' => 'nullable|string|max:255',
        ]);

        $createdClasses = $recurringService->generate($class, [
            'recurrence_type' => $validated['recurrence_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'time' => $validated['time'],
            'days_of_week' => $validated['days_of_week'] ?? [],
            'series_name' => $validated['series_name'] ?? null,
        ]);

        return redirect()
            ->route('admin.classes.index')
            ->with('success', count($createdClasses) . ' recurring classes created successfully! They are saved as drafts for review.');
    }
}
