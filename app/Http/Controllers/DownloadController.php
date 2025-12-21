<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Download a digital product file
     */
    public function download($token)
    {
        // Find the order item by download URL token
        $orderItem = OrderItem::where('digital_download_url', $token)
            ->where('product_type', 'digital')
            ->firstOrFail();

        // Verify the order belongs to the current user
        if ($orderItem->order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to download');
        }

        // Check if product and digital file still exist
        if (!$orderItem->product || !$orderItem->product->digital_file_path) {
            abort(404, 'Digital file not found');
        }

        // Increment download count
        $orderItem->increment('download_count');

        // Get the file path
        $filePath = storage_path('app/private/downloads/' . $orderItem->product->digital_file_path);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        // Get file extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Generate download filename
        $downloadName = $orderItem->product->title . '.' . $extension;

        // Stream the file for download
        return response()->download($filePath, $downloadName);
    }
}
