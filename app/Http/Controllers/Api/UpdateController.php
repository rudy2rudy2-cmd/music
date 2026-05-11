<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'platform_name' => 'required|string',
            'current_version' => 'required|string',
        ]);

        $platform = Platform::where('name', $request->platform_name)->first();

        if (!$platform) {
            return response()->json(['update_available' => false, 'message' => 'Platform not found.'], 404);
        }

        $updateAvailable = version_compare($platform->version, $request->current_version, '>');

        return response()->json([
            'update_available' => $updateAvailable,
            'latest_version' => $platform->version,
            'download_url' => $updateAvailable ? route('platform.download', ['license_key' => 'UPDATE_TOKEN']) : null,
            'changelog' => $platform->description,
        ]);
    }
}
