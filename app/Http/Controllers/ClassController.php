<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
use App\Models\Review;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ArtClass::available();

        // Apply sorting
        $sortBy = $request->get('sort', 'date');

        if ($sortBy === 'price_low') {
            $query->orderBy('price_cents', 'asc');
        } elseif ($sortBy === 'price_high') {
            $query->orderBy('price_cents', 'desc');
        } else {
            $query->orderBy('class_date', 'asc');
        }

        $classes = $query->paginate(9)->withQueryString();

        return view('classes.index', compact('classes', 'sortBy'));
    }

    public function show($slug)
    {
        $class = ArtClass::where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedClasses = ArtClass::available()
            ->where('id', '!=', $class->id)
            ->limit(3)
            ->get();

        $reviews = Review::approved()
            ->forClass($class->id)
            ->with('user')
            ->latest()
            ->get();

        $userHasReviewed = auth()->check()
            ? Review::where('user_id', auth()->id())->where('art_class_id', $class->id)->exists()
            : false;

        return view('classes.show', compact('class', 'relatedClasses', 'reviews', 'userHasReviewed'));
    }
}
