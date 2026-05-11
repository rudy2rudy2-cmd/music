<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\License;
use App\Mail\PurchaseConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    public function process(Request $request, $platform_id)
    {
        $platform = Platform::findOrFail($platform_id);
        $method = $request->input('payment_method', 'stripe');

        try {
            if ($method === 'stripe') {
                return $this->handleStripe($platform);
            } elseif ($method === 'paypal') {
                return $this->handlePayPal($platform);
            }
        } catch (\Exception $e) {
            Log::error("Payment process error: " . $e->getMessage());
            return back()->with('error', 'Error processing payment. Please try again.');
        }

        return back()->with('error', 'Invalid payment method selected.');
    }

    protected function handleStripe(Platform $platform)
    {
        Stripe::setApiKey(config('stripe.stripe.secret'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $platform->name,
                    ],
                    'unit_amount' => $platform->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.success', ['platform_id' => $platform->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('platforms.show', $platform->id),
            'customer_email' => Auth::user()->email,
        ]);

        return redirect()->away($session->url);
    }

    protected function handlePayPal(Platform $platform)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.success.paypal', ['platform_id' => $platform->id]),
                "cancel_url" => route('platforms.show', $platform->id),
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $platform->price
                    ]
                ]
            ]
        ]);

        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
        }

        return redirect()->route('platforms.show', $platform->id)->with('error', 'Something went wrong with PayPal.');
    }

    public function success(Request $request, $platform_id)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('platforms.show', $platform_id)->with('error', 'Invalid session.');
        }

        Stripe::setApiKey(config('stripe.stripe.secret'));

        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return redirect()->route('platforms.show', $platform_id)->with('error', 'Payment not completed.');
            }

            $platform = Platform::findOrFail($platform_id);
            return $this->fulfillOrder($platform);

        } catch (\Exception $e) {
            Log::error("Stripe Verification Error: " . $e->getMessage());
            return redirect()->route('platforms.show', $platform_id)->with('error', 'Error verifying payment.');
        }
    }

    public function successPayPal(Request $request, $platform_id)
    {
        $token = $request->get('token');
        if (!$token) {
             return redirect()->route('platforms.show', $platform_id)->with('error', 'Invalid PayPal token.');
        }

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $platform = Platform::findOrFail($platform_id);
            return $this->fulfillOrder($platform);
        }

        return redirect()->route('platforms.show', $platform_id)->with('error', 'PayPal payment failed.');
    }

    protected function fulfillOrder(Platform $platform)
    {
        $user = Auth::user();

        // Create License if not already exists (idempotency)
        $license = License::where('platform_id', $platform->id)->where('user_id', $user->id)->first();

        if (!$license) {
            $license = License::create([
                'platform_id' => $platform->id,
                'user_id' => $user->id,
                'license_key' => strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
                'status' => 'active',
            ]);

            // Send confirmation email
            try {
                Mail::to($user->email)->send(new PurchaseConfirmation($license));
            } catch (\Exception $e) {
                Log::error("Failed to send purchase confirmation email to {$user->email}: " . $e->getMessage());
            }
        }

        Log::info("Purchase Confirmation: User {$user->email} bought {$platform->name}. License: {$license->license_key}");

        return redirect()->route('dashboard')->with('success', "Payment successful! You now have access to {$platform->name}.");
    }
}
