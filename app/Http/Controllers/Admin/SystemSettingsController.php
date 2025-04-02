<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        return view('admin.system-settings.index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request, GeneralSettings $settings)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email'],
            'timezone' => ['required', 'string'],
            'date_format' => ['required', 'string'],
            'time_format' => ['required', 'string'],
        ]);

        foreach ($validated as $key => $value) {
            $settings->$key = $value;
        }
        
        $settings->save();

        return back()->with('success', 'System settings updated successfully.');
    }
} 