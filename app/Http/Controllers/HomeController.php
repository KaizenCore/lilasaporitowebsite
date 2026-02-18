<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredClasses = ArtClass::available()
            ->orderBy('class_date', 'asc')
            ->limit(3)
            ->get();

        $reviews = Review::approved()
            ->with(['user', 'artClass'])
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact('featuredClasses', 'reviews'));
    }
}
