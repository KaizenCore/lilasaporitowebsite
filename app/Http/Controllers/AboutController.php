<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $settings = [
            'about_title' => SiteSetting::get('about_title', 'Hello, I\'m Lila!'),
            'about_subtitle' => SiteSetting::get('about_subtitle', 'Artist, Teacher, Creative Spirit'),
            'about_bio' => SiteSetting::get('about_bio', ''),
            'about_photo' => SiteSetting::get('about_photo', ''),
            'teaching_philosophy' => SiteSetting::get('teaching_philosophy', ''),
            'why_take_classes' => SiteSetting::get('why_take_classes', ''),
        ];

        return view('about', compact('settings'));
    }
}
