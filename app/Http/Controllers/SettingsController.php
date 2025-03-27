<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get current user
        $user = Auth::user();
        
        // You could also fetch global application settings here
        $settings = [
            'notification_preferences' => $user->notification_preferences ?? [],
            'default_view' => $user->default_view ?? 'list',
            'theme' => $user->theme ?? 'light',
        ];
        
        return view('settings.index', compact('settings', 'user'));
    }
}
