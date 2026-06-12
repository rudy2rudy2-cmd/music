<?php

namespace App\Http\Controllers;

use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $licenses = License::where('user_id', Auth::id())->with('platform')->get();
        return view('dashboard', compact('licenses'));
    }
}
