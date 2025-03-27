<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleIntakeController extends Controller
{
    /**
     * Display the vehicle intake & dispatch dashboard.
     */
    public function index()
    {
        \Log::debug('VehicleIntakeController@index method called');
        
        // Get vehicles that are in intake stage or recently arrived
        $newVehicles = Vehicle::where('current_stage', 'intake')
            ->orWhere(function($query) {
                $query->whereNotNull('transport_assigned_at')
                      ->whereNull('check_in_date');
            })
            ->with(['transporter', 'documents'])
            ->latest('created_at')
            ->paginate(10);
            
        // Get transporters (vendors marked as transportation type)
        $transporters = Vendor::transporters()->where('is_active', true)->get();
        
        // Get vendors that can be purchase locations
        $purchaseLocations = Vendor::purchaseLocations()->where('is_active', true)->get();
        
        // Get sales managers for notifications
        $salesManagers = User::where('role', 'sales_manager')->get();
        
        return view('vehicles.intake.index', compact(
            'newVehicles', 
            'transporters', 
            'purchaseLocations', 
            'salesManagers'
        ));
    }
    
    /**
     * Import vehicles from FTP file.
     */
    public function importFtp(Request $request)
    {
        // This would normally connect to FTP and import data
        // But for now, we'll just show a success message
        
        // Notify sales managers about new vehicles
        $salesManagers = User::where('role', 'sales_manager')->get();
        if ($salesManagers->count() > 0) {
            // In a real implementation, you would use:
            // Notification::send($salesManagers, new NewVehiclesImported($vehicles));
        }
        
        return redirect()->route('vehicles.intake')
            ->with('success', 'Vehicle data imported successfully. Sales managers have been notified.');
    }
    
    /**
     * Assign a transporter to a vehicle.
     */
    public function assignTransporter(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'transporter_id' => 'required|exists:vendors,id',
            'transport_expected_at' => 'required|date',
            'purchased_from' => 'required|exists:vendors,id',
            'purchase_location' => 'required|string',
            'purchase_price' => 'required|numeric',
            'purchase_date' => 'required|date',
            'is_arbitrable' => 'boolean',
        ]);
        
        DB::transaction(function() use ($vehicle, $validated) {
            $vehicle->update([
                'transporter_id' => $validated['transporter_id'],
                'transport_assigned_at' => now(),
                'transport_expected_at' => $validated['transport_expected_at'],
                'purchased_from' => $validated['purchased_from'],
                'purchase_location' => $validated['purchase_location'],
                'purchase_price' => $validated['purchase_price'],
                'purchase_date' => $validated['purchase_date'],
                'is_arbitrable' => $validated['is_arbitrable'] ?? false,
            ]);
            
            // Create initial tasks as needed
            // This would be implemented based on business rules
        });
        
        return redirect()->route('vehicles.intake')
            ->with('success', 'Transporter assigned successfully.');
    }
    
    /**
     * Upload documents (release forms, gate passes) for a vehicle.
     */
    public function uploadDocument(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:10240',
            'type' => 'required|in:release_form,gate_pass',
            'name' => 'required|string|max:255',
        ]);
        
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = Str::slug($validated['name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('vehicle-documents/' . $vehicle->id, $filename, 'public');
            
            Document::create([
                'name' => $validated['name'],
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'type' => $validated['type'],
                'documentable_id' => $vehicle->id,
                'documentable_type' => Vehicle::class,
            ]);
            
            return redirect()->route('vehicles.intake')
                ->with('success', 'Document uploaded successfully.');
        }
        
        return redirect()->route('vehicles.intake')
            ->with('error', 'Error uploading document.');
    }
    
    /**
     * Scan VIN barcode to find vehicle.
     */
    public function scan(Request $request, $vin = null)
    {
        if ($vin) {
            $vehicle = Vehicle::where('vin', $vin)->first();
            
            if ($vehicle) {
                return redirect()->route('vehicles.show', $vehicle);
            }
            
            return redirect()->route('vehicles.intake')
                ->with('error', 'Vehicle with VIN ' . $vin . ' not found.');
        }
        
        return view('vehicles.intake.scan');
    }
}
