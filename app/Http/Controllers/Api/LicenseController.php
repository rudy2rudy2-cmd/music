<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'domain' => 'nullable|string',
            'action' => 'nullable|string', // 'activate' or 'verify'
        ]);

        $license = License::where('license_key', $request->license_key)->first();

        if (!$license) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid license key.',
            ], 404);
        }

        if ($license->status !== 'active') {
            return response()->json([
                'valid' => false,
                'message' => 'License is ' . $license->status . '.',
            ], 403);
        }

        if ($license->expires_at && $license->expires_at->isPast()) {
            $license->update(['status' => 'expired']);
            return response()->json([
                'valid' => false,
                'message' => 'License has expired.',
            ], 403);
        }

        $domain = $this->sanitizeDomain($request->domain);

        if ($request->action === 'activate') {
            if (!$license->domain) {
                $license->update([
                    'domain' => $domain,
                    'activated_at' => now(),
                ]);
            } elseif ($license->domain !== $domain) {
                return response()->json([
                    'valid' => false,
                    'message' => 'License is already activated on: ' . $license->domain,
                ], 403);
            }
        } else {
            // Standard verification
            if ($license->domain && $domain && $license->domain !== $domain) {
                 return response()->json([
                    'valid' => false,
                    'message' => 'Domain mismatch. License bound to: ' . $license->domain,
                ], 403);
            }
        }

        return response()->json([
            'valid' => true,
            'message' => 'License verified successfully.',
            'platform' => $license->platform->name,
            'version' => $license->platform->version,
            'expires_at' => $license->expires_at ? $license->expires_at->toDateTimeString() : null,
        ]);
    }

    protected function sanitizeDomain($domain)
    {
        if (!$domain) return null;
        $domain = strtolower($domain);
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        return explode('/', $domain)[0];
    }
}
