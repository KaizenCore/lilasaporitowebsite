<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyPainting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartyPaintingController extends Controller
{
    /**
     * Display a listing of the paintings.
     */
    public function index()
    {
        $paintings = PartyPainting::withCount('partyBookings')
            ->orderBy('sort_order', 'asc')
            ->orderBy('title', 'asc')
            ->paginate(15);

        return view('admin.parties.paintings.index', compact('paintings'));
    }

    /**
     * Show the form for creating a new painting.
     */
    public function create()
    {
        return view('admin.parties.paintings.create');
    }

    /**
     * Store a newly created painting in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:party_paintings,slug',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'required|integer|min:30|max:480',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('party-paintings', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        PartyPainting::create($validated);

        return redirect()->route('admin.parties.paintings.index')
            ->with('success', 'Painting added successfully.');
    }

    /**
     * Show the form for editing the specified painting.
     */
    public function edit(PartyPainting $painting)
    {
        return view('admin.parties.paintings.edit', compact('painting'));
    }

    /**
     * Update the specified painting in storage.
     */
    public function update(Request $request, PartyPainting $painting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:party_paintings,slug,' . $painting->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'required|integer|min:30|max:480',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($painting->image_path) {
                Storage::disk('public')->delete($painting->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('party-paintings', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');

        $painting->update($validated);

        return redirect()->route('admin.parties.paintings.index')
            ->with('success', 'Painting updated successfully.');
    }

    /**
     * Remove the specified painting from storage.
     */
    public function destroy(PartyPainting $painting)
    {
        // Check if painting is used in active bookings
        $activeBookings = $painting->partyBookings()
            ->whereNotIn('status', ['completed', 'cancelled', 'declined'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->route('admin.parties.paintings.index')
                ->with('error', 'Cannot delete painting that is selected in active bookings.');
        }

        // Delete image if exists
        if ($painting->image_path) {
            Storage::disk('public')->delete($painting->image_path);
        }

        $painting->delete();

        return redirect()->route('admin.parties.paintings.index')
            ->with('success', 'Painting deleted successfully.');
    }
}
