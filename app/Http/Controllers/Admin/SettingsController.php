<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'permission:edit users']);
    }

    /**
     * Display the settings page.
     */
    public function index()
    {
        $settings = Cache::get('system_settings', [
            'company_name' => config('app.name'),
            'notification_email' => config('mail.from.address'),
            'enable_notifications' => true,
            'enable_auto_assignments' => false,
        ]);

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'notification_email' => 'required|email|max:255',
            'enable_notifications' => 'boolean',
            'enable_auto_assignments' => 'boolean',
        ]);

        Cache::forever('system_settings', $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
