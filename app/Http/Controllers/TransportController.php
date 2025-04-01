<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
            ->orWhere('destination', 'like', "%{$search}%");
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
        ]);

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
        }

        Transport::create($validated);
        return redirect()->route('transports.index')
                         ->with('success', 'Transport created successfully.');
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
        ]);

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

        $transport->update($validated);
        return redirect()->route('transports.index')
                         ->with('success', 'Transport updated successfully.');
    }

    /**
     * Remove the specified transport from storage.
     */
    public function destroy(Transport $transport): RedirectResponse
    {
        $transport->delete();
        return redirect()->route('transports.index')
                         ->with('success', 'Transport removed successfully.');
    }
} 