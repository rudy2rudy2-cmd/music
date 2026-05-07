<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $platforms = Platform::where('is_active', true)->get();
        $siteName = Setting::get('site_name', 'My Platform Store');
        $headerColor = Setting::get('header_color', '#ffffff');
        $footerColor = Setting::get('footer_color', '#f3f4f6');
        $logo = Setting::get('logo');

        return view('home', compact('platforms', 'siteName', 'headerColor', 'footerColor', 'logo'));
    }
}
