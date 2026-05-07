<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function download(Request $request, $license_key)
    {
        $license = License::where('license_key', $license_key)->firstOrFail();

        // Security check:
        // 1. Must be active
        // 2. If user is logged in, it must be their license
        // 3. If no user is logged in (API request from installer),
        // we could potentially check for a secret token or just rely on the key being secret.
        // For simplicity, we check if it's the owner or if it's an API verification context.

        if ($license->status !== 'active') {
            abort(403, 'License is not active.');
        }

        if (Auth::check() && $license->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this license.');
        }

        $platform = $license->platform;

        if (!$platform->zip_path || !Storage::exists($platform->zip_path)) {
            // Log this as it might be a configuration error
            \Log::error("Download failed: ZIP file not found for platform " . $platform->name);
            abort(404, 'Platform file not found on server.');
        }

        return Storage::download($platform->zip_path, $platform->name . '.zip');
    }
}
