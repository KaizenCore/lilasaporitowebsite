<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    /**
     * Show the site settings form.
     */
    public function index()
    {
        $settings = [
            'about_title' => SiteSetting::get('about_title', 'Hello, I\'m Lila!'),
            'about_subtitle' => SiteSetting::get('about_subtitle', 'Artist, Teacher, Creative Spirit'),
            'about_bio' => SiteSetting::get('about_bio', ''),
            'about_photo' => SiteSetting::get('about_photo', ''),
            'teaching_philosophy' => SiteSetting::get('teaching_philosophy', ''),
            'why_take_classes' => SiteSetting::get('why_take_classes', ''),
            'cancellation_policy' => SiteSetting::get('cancellation_policy', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'about_title' => 'nullable|string|max:255',
            'about_subtitle' => 'nullable|string|max:255',
            'about_bio' => 'nullable|string',
            'about_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'teaching_philosophy' => 'nullable|string',
            'why_take_classes' => 'nullable|string',
            'cancellation_policy' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('about_photo')) {
            // Delete old photo if exists
            $oldPhoto = SiteSetting::get('about_photo');
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
            $path = $request->file('about_photo')->store('about', 'public');
            SiteSetting::set('about_photo', $path, 'image');
        }

        // Update text settings
        SiteSetting::set('about_title', $request->about_title ?? '', 'text');
        SiteSetting::set('about_subtitle', $request->about_subtitle ?? '', 'text');
        SiteSetting::set('about_bio', $request->about_bio ?? '', 'text');
        SiteSetting::set('teaching_philosophy', $request->teaching_philosophy ?? '', 'text');
        SiteSetting::set('why_take_classes', $request->why_take_classes ?? '', 'text');
        SiteSetting::set('cancellation_policy', $request->cancellation_policy ?? '', 'text');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Delete the about photo.
     */
    public function deletePhoto()
    {
        $photo = SiteSetting::get('about_photo');
        if ($photo) {
            Storage::disk('public')->delete($photo);
            SiteSetting::set('about_photo', '', 'image');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Photo deleted successfully.');
    }
}
