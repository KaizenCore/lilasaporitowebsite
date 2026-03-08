<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;

class GalleryController extends Controller
{
    public function index()
    {
        // Get past/completed classes that have gallery images or a main image
        $classes = ArtClass::where(function ($query) {
                $query->where('status', 'completed')
                    ->orWhere(function ($q) {
                        $q->where('status', 'published')
                            ->where('class_date', '<', now());
                    });
            })
            ->where(function ($query) {
                $query->whereNotNull('gallery_images')
                    ->orWhereNotNull('image_path');
            })
            ->orderBy('class_date', 'desc')
            ->get();

        return view('gallery.index', compact('classes'));
    }
}
