<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    public function index()
    {
        return view('auth.2fa');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        if ($google2fa->verifyKey($user->google2fa_secret, $request->one_time_password)) {
            $request->session()->put('2fa_verified', true);
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['one_time_password' => 'Invalid authentication code.']);
    }
}
