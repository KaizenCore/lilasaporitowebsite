<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartyAddon;
use Illuminate\Http\Request;

class PartyAddonController extends Controller
{
    /**
     * Display a listing of addons.
     */
    public function index()
    {
        $addons = PartyAddon::orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('admin.parties.addons.index', compact('addons'));
    }

    /**
     * Show the form for creating a new addon.
     */
    public function create()
    {
        return view('admin.parties.addons.create');
    }

    /**
     * Store a newly created addon in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price_cents' => 'required|integer|min:0',
            'is_per_person' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_per_person'] = $request->boolean('is_per_person');
        $validated['is_active'] = $request->boolean('is_active');

        PartyAddon::create($validated);

        return redirect()->route('admin.parties.addons.index')
            ->with('success', 'Add-on created successfully.');
    }

    /**
     * Show the form for editing the specified addon.
     */
    public function edit(PartyAddon $addon)
    {
        return view('admin.parties.addons.edit', compact('addon'));
    }

    /**
     * Update the specified addon in storage.
     */
    public function update(Request $request, PartyAddon $addon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'price_cents' => 'required|integer|min:0',
            'is_per_person' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_per_person'] = $request->boolean('is_per_person');
        $validated['is_active'] = $request->boolean('is_active');

        $addon->update($validated);

        return redirect()->route('admin.parties.addons.index')
            ->with('success', 'Add-on updated successfully.');
    }

    /**
     * Remove the specified addon from storage.
     */
    public function destroy(PartyAddon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.parties.addons.index')
            ->with('success', 'Add-on deleted successfully.');
    }
}
