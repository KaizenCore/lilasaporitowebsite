<?php

namespace App\Http\Controllers;

use App\Models\ArtClass;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredClasses = ArtClass::available()
            ->limit(3)
            ->get();

        return view('home', compact('featuredClasses'));
    }
}
