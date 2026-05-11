<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Platform;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'current_version' => 'required|string',
        ]);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license || $license->status !== 'active') {
            return response()->json([
                'update_available' => false,
                'message' => 'Invalid or inactive license.'
            ], 403);
        }

        $platform = $license->platform;

        $updateAvailable = version_compare($platform->version, $request->current_version, '>');

        return response()->json([
            'update_available' => $updateAvailable,
            'latest_version' => $platform->version,
            'download_url' => $updateAvailable ? route('platform.download', ['license_key' => $license->license_key]) : null,
            'changelog' => $platform->description,
        ]);
    }
}
