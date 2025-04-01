<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    /**
     * Display a listing of the transports.
     */
    public function index(Request $request): View
    {
        $query = Transport::with('vehicle')->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('stock_number', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            })
            ->orWhere('transporter_name', 'like', "%{$search}%")
            ->orWhere('destination', 'like', "%{$search}%")
            ->orWhere('batch_id', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $transports = $query->paginate(10);
        return view('transports.index', compact('transports'));
    }

    /**
     * Show the form for creating a new transport.
     */
    public function create(): View
    {
        $vehicles = Vehicle::where('status', '!=', 'sold')
                          ->where(function($query) {
                              $query->whereNull('transport_status')
                                    ->orWhere('transport_status', '!=', 'in_transit');
                          })
                          ->orderBy('stock_number')
                          ->get();
        $transporters = Transporter::where('is_active', true)
                                 ->orderBy('name')
                                 ->get();
        return view('transports.create', compact('vehicles', 'transporters'));
    }

    /**
     * Store a newly created transport in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'transporter_name' => 'nullable|string|max:255',
            'transporter_phone' => 'nullable|string|max:255',
            'transporter_email' => 'nullable|string|email|max:255',
            'notes' => 'nullable|string',
            'batch_name' => 'nullable|string|max:255',
            'generate_qr' => 'nullable|boolean',
            'gate_passes' => 'nullable|array',
            'gate_passes.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // If transporter_id is selected, clear manual transporter fields
        if (!empty($validated['transporter_id'])) {
            $validated['transporter_name'] = null;
            $validated['transporter_phone'] = null;
            $validated['transporter_email'] = null;
        }

        // Generate batch number
        $batchNumber = 'B-' . date('ymd') . '-' . strtoupper(Str::random(4));
        
        // Get common transport data
        $transportData = [
            'batch_id' => $batchNumber,
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'pickup_date' => $validated['pickup_date'],
            'delivery_date' => $validated['delivery_date'],
            'status' => $validated['status'],
            'transporter_id' => $validated['transporter_id'],
            'transporter_name' => $validated['transporter_name'],
            'transporter_phone' => $validated['transporter_phone'],
            'transporter_email' => $validated['transporter_email'],
            'notes' => $validated['notes'],
            'batch_name' => $validated['batch_name'],
        ];

        // Generate QR Code if requested
        if ($request->generate_qr) {
            // Create full absolute URL for the tracking page
            $qrUrl = url("/track/{$batchNumber}");
            $qrPath = 'qrcodes/' . $batchNumber . '.png';
            $qrCode = QrCode::format('png')->size(300)->generate($qrUrl);
            Storage::disk('public')->put($qrPath, $qrCode);
            $transportData['qr_code_path'] = $qrPath;
        }

        // Create a transport entry for each selected vehicle
        foreach ($validated['vehicle_ids'] as $vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            
            // Create transport record
            $transport = new Transport($transportData);
            $transport->vehicle_id = $vehicleId;
            $transport->save();
            
            // Handle gate pass upload if provided
            if ($request->hasFile("gate_passes.{$vehicleId}")) {
                $file = $request->file("gate_passes.{$vehicleId}");
                $path = $file->store('gate-passes', 'public');
                $transport->gate_pass_path = $path;
                $transport->save();
            }
            
            // Update vehicle transport status
            if ($request->status == 'in_transit') {
                $vehicle->update(['transport_status' => 'in_transit']);
            }
        }

        return redirect()->route('transports.index')
                         ->with('success', count($validated['vehicle_ids']) . ' vehicles added to transport batch ' . $batchNumber);
    }

    /**
     * Display the specified transport.
     */
    public function show(Transport $transport): View
    {
        return view('transports.show', compact('transport'));
    }

    /**
     * Show the form for editing the specified transport.
     */
    public function edit(Transport $transport): View
    {
        $vehicles = Vehicle::where('status', '!=', 'sold')
                          ->orderBy('stock_number')
                          ->get();
        $transporters = Transporter::where('is_active', true)
                                 ->orderBy('name')
                                 ->get();
        return view('transports.edit', compact('transport', 'vehicles', 'transporters'));
    }

    /**
     * Update the specified transport in storage.
     */
    public function update(Request $request, Transport $transport): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'transporter_name' => 'nullable|string|max:255',
            'transporter_phone' => 'nullable|string|max:255',
            'transporter_email' => 'nullable|string|email|max:255',
            'notes' => 'nullable|string',
            'gate_pass' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'additional_vehicle_ids' => 'nullable|array',
            'additional_vehicle_ids.*' => 'exists:vehicles,id',
            'remove_vehicle_ids' => 'nullable|array',
            'remove_vehicle_ids.*' => 'exists:transports,id',
        ]);

        DB::beginTransaction();
        
        try {
            // If transporter_id is selected, clear manual transporter fields
            if (!empty($validated['transporter_id'])) {
                $validated['transporter_name'] = null;
                $validated['transporter_phone'] = null;
                $validated['transporter_email'] = null;
            }

            // Update vehicle transport status
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            if ($request->status == 'in_transit') {
                $vehicle->update(['transport_status' => 'in_transit']);
            } elseif ($request->status == 'delivered') {
                $vehicle->update(['transport_status' => 'delivered']);
            } elseif ($request->status == 'cancelled') {
                $vehicle->update(['transport_status' => null]);
            }

            // Handle gate pass upload if provided
            if ($request->hasFile('gate_pass')) {
                // Delete old file if exists
                if ($transport->gate_pass_path) {
                    Storage::disk('public')->delete($transport->gate_pass_path);
                }
                
                $file = $request->file('gate_pass');
                $path = $file->store('gate-passes', 'public');
                $validated['gate_pass_path'] = $path;
            }

            // Update the transport record
            $transport->update($validated);
            
            // Handle batch management - add vehicles
            if ($request->has('additional_vehicle_ids') && is_array($request->additional_vehicle_ids)) {
                // Get transport data for new vehicles to reuse
                $transportData = [
                    'batch_id' => $transport->batch_id,
                    'batch_name' => $transport->batch_name,
                    'origin' => $transport->origin,
                    'destination' => $transport->destination,
                    'pickup_date' => $transport->pickup_date,
                    'delivery_date' => $transport->delivery_date,
                    'status' => $transport->status,
                    'transporter_id' => $transport->transporter_id,
                    'transporter_name' => $transport->transporter_name,
                    'transporter_phone' => $transport->transporter_phone,
                    'transporter_email' => $transport->transporter_email,
                    'notes' => $transport->notes,
                    'qr_code_path' => $transport->qr_code_path,
                ];
                
                foreach ($request->additional_vehicle_ids as $vehicleId) {
                    $newVehicle = Vehicle::findOrFail($vehicleId);
                    
                    // Create new transport record for this vehicle in the same batch
                    $newTransport = new Transport($transportData);
                    $newTransport->vehicle_id = $vehicleId;
                    $newTransport->save();
                    
                    // Update vehicle status if needed
                    if ($transport->status == 'in_transit') {
                        $newVehicle->update(['transport_status' => 'in_transit']);
                    }
                }
            }
            
            // Handle batch management - remove vehicles
            if ($request->has('remove_vehicle_ids') && is_array($request->remove_vehicle_ids)) {
                foreach ($request->remove_vehicle_ids as $transportId) {
                    $transportToRemove = Transport::find($transportId);
                    
                    if ($transportToRemove && $transportToRemove->batch_id === $transport->batch_id) {
                        // Update vehicle status
                        if ($transportToRemove->vehicle) {
                            $transportToRemove->vehicle->update(['transport_status' => null]);
                        }
                        
                        // Delete gate pass file if exists
                        if ($transportToRemove->gate_pass_path) {
                            Storage::disk('public')->delete($transportToRemove->gate_pass_path);
                        }
                        
                        // Delete transport record
                        $transportToRemove->delete();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('transports.index')
                             ->with('success', 'Transport updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update transport: ' . $e->getMessage()]);
        }
    }

    /**
     * Display transports by batch ID.
     */
    public function showBatch(string $batchId): View
    {
        $transports = Transport::where('batch_id', $batchId)
                              ->with('vehicle')
                              ->get();
                              
        if ($transports->isEmpty()) {
            abort(404, 'Batch not found');
        }
        
        // Use the first transport to get common batch data
        $batchData = $transports->first();
        
        return view('transports.batch', compact('transports', 'batchData', 'batchId'));
    }

    /**
     * Track batch via QR code - public access without header/footer.
     */
    public function trackBatch(string $batchId): View
    {
        $transports = Transport::where('batch_id', $batchId)
                              ->with('vehicle')
                              ->get();
                              
        if ($transports->isEmpty()) {
            abort(404, 'Batch not found');
        }
        
        // Use the first transport to get common batch data
        $batchData = $transports->first();
        
        return view('transports.track', compact('transports', 'batchData', 'batchId'));
    }

    /**
     * Remove the specified transport from storage.
     */
    public function destroy(Transport $transport): RedirectResponse
    {
        // Delete gate pass file if exists
        if ($transport->gate_pass_path) {
            Storage::disk('public')->delete($transport->gate_pass_path);
        }
        
        $transport->delete();
        return redirect()->route('transports.index')
                         ->with('success', 'Transport removed successfully.');
    }
} 