<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class PolicyController extends Controller
{
    public function index()
    {
        $cancellationPolicy = SiteSetting::get('cancellation_policy', '');

        return view('policy.index', [
            'cancellationPolicy' => $cancellationPolicy,
        ]);
    }
}
