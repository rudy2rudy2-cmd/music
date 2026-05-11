<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function process(Request $request, $platform_id)
    {
        $platform = Platform::findOrFail($platform_id);
        $method = $request->input('payment_method', 'stripe');

        // Simulate redirecting to a payment gateway
        return view('payment_redirect', [
            'platform' => $platform,
            'method' => $method,
        ]);
    }

    public function success(Request $request, $platform_id)
    {
        $platform = Platform::findOrFail($platform_id);
        $user = Auth::user();

        // Create License
        $license = License::create([
            'platform_id' => $platform->id,
            'user_id' => $user->id,
            'license_key' => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
            'status' => 'active',
        ]);

        // Simulate sending email
        Log::info("Purchase Confirmation: User {$user->email} bought {$platform->name}. License: {$license->license_key}");

        return redirect()->route('dashboard')->with('success', "Payment successful! You now have access to {$platform->name}.");
    }
}
