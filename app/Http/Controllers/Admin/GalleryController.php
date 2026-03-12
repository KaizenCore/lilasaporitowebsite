<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $classes = ArtClass::where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere(function ($q) {
                        $q->where('status', 'published')
                            ->where('class_date', '<', now());
                    });
            })
            ->orderBy('class_date', 'desc')
            ->get();

        return view('admin.gallery.index', compact('classes'));
    }

    public function upload(Request $request, ArtClass $class)
    {
        $request->validate([
            'gallery_images' => 'required|array|min:1',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $currentGallery = $class->gallery_images ? json_decode($class->gallery_images, true) : [];

        foreach ($request->file('gallery_images') as $image) {
            $currentGallery[] = $image->store('class-images/gallery', 'public');
        }

        $class->update([
            'gallery_images' => json_encode(array_values($currentGallery)),
        ]);

        return back()->with('success', 'Photos uploaded!');
    }

    public function removeImage(Request $request, ArtClass $class)
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $currentGallery = $class->gallery_images ? json_decode($class->gallery_images, true) : [];
        $imagePath = $request->image_path;

        $currentGallery = array_filter($currentGallery, fn($img) => $img !== $imagePath);

        // Only delete file if no other class uses it
        $shared = ArtClass::where('id', '!=', $class->id)
            ->where('gallery_images', 'like', '%' . str_replace(['%', '_'], ['\\%', '\\_'], $imagePath) . '%')
            ->count();

        if ($shared === 0) {
            Storage::disk('public')->delete($imagePath);
        }

        $class->update([
            'gallery_images' => !empty($currentGallery) ? json_encode(array_values($currentGallery)) : null,
        ]);

        return back()->with('success', 'Photo removed.');
    }
}
