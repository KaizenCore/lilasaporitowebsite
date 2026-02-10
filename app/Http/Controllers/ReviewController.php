<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
            'art_class_id' => 'nullable|exists:art_classes,id',
        ]);

        $exists = Review::where('user_id', $request->user()->id)
            ->where('art_class_id', $validated['art_class_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted a review for this class.');
        }

        Review::create([
            'user_id' => $request->user()->id,
            'art_class_id' => $validated['art_class_id'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Thank you! Your review has been submitted and will appear once approved.');
    }
}
