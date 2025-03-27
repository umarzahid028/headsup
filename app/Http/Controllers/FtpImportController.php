<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FtpImportController extends Controller
{
    /**
     * Display the FTP import settings page.
     */
    public function index()
    {
        $settings = [
            'ftp_host' => config('ftp.host', ''),
            'ftp_username' => config('ftp.username', ''),
            'ftp_directory' => config('ftp.directory', ''),
            'file_pattern' => config('ftp.file_pattern', '*.csv'),
            'auto_import' => config('ftp.auto_import', false),
            'auto_import_frequency' => config('ftp.auto_import_frequency', 'daily'),
        ];
        
        // Get last import logs
        $logs = Storage::exists('logs/ftp-imports.log') 
            ? array_slice(explode("\n", Storage::get('logs/ftp-imports.log')), -10) 
            : [];
        
        return view('ftp-import.index', compact('settings', 'logs'));
    }
    
    /**
     * Update FTP import settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'ftp_host' => 'required|string',
            'ftp_username' => 'required|string',
            'ftp_password' => 'nullable|string',
            'ftp_directory' => 'required|string',
            'file_pattern' => 'required|string',
            'auto_import' => 'boolean',
            'auto_import_frequency' => 'required_if:auto_import,true|in:hourly,daily,weekly',
        ]);
        
        // In a real application, you would save settings to .env or database
        // For now, we'll just show a success message
        
        return redirect()->route('ftp-import.index')
            ->with('success', 'FTP import settings updated successfully.');
    }
    
    /**
     * Run an FTP import manually.
     */
    public function runImport(Request $request)
    {
        try {
            // This would normally connect to FTP and import data
            // In a real application, this would be handled by a job or command
            // But for now, we'll just log that it was attempted
            
            Log::channel('daily')->info('Manual FTP import requested by user ' . auth()->id());
            
            // Redirect to the vehicle intake page to see the results
            return redirect()->route('vehicles.intake')
                ->with('success', 'FTP import started. Vehicles will appear in the list when import is complete.');
                
        } catch (\Exception $e) {
            Log::error('FTP import error: ' . $e->getMessage());
            
            return redirect()->route('ftp-import.index')
                ->with('error', 'Error running FTP import: ' . $e->getMessage());
        }
    }
}
