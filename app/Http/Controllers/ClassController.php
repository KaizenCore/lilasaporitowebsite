<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
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

        $classes = $query->paginate(9);

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

        return view('classes.show', compact('class', 'relatedClasses'));
    }
}
