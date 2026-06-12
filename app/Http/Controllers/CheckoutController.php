<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout(Request $request, $platform_id)
    {
        $platform = Platform::findOrFail($platform_id);
        return view('checkout', compact('platform'));
    }

    public function process(Request $request, $platform_id)
    {
        $platform = Platform::findOrFail($platform_id);

        // Mock payment processing
        // In reality, you'd integrate Stripe/PayPal here

        $license = License::create([
            'platform_id' => $platform->id,
            'user_id' => Auth::id(),
            'license_key' => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
            'status' => 'active',
        ]);

        return redirect()->route('dashboard')->with('success', 'Platform purchased successfully!');
    }
}
