<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::query();
        
        // Apply filters from request
        if ($request->has('status')) {
            if ($request->status === 'frontline') {
                $query->where('is_frontline_ready', true);
            } elseif ($request->status === 'archived') {
                $query->where('is_archived', true);
            } elseif ($request->status === 'sold') {
                $query->where('is_sold', true);
            } else {
                $query->where('current_stage', $request->status);
            }
        } else {
            // Default to show active (not archived) vehicles
            $query->where('is_archived', false)
                  ->where('is_sold', false);
        }
        
        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vin', 'like', "%{$search}%")
                  ->orWhere('stock_number', 'like', "%{$search}%")
                  ->orWhere('make', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%");
            });
        }
        
        // Stage filter
        if ($request->has('stage')) {
            $query->where('current_stage', $request->stage);
        }
        
        // Sort vehicles
        $query->orderBy($request->get('sort', 'created_at'), $request->get('direction', 'desc'));
        
        $vehicles = $query->paginate(15)->withQueryString();
        $stages = WorkflowStage::orderBy('order')->get();
        
        return view('vehicles.index', compact('vehicles', 'stages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only sales_manager and admin can create vehicles
        if (!Auth::user()->role === 'admin' && !Auth::user()->role === 'sales_manager') {
            return redirect()->route('vehicles.index')
                ->with('error', 'You do not have permission to add a new vehicle.');
        }
        
        $transporters = User::where('role', 'transporter')->where('is_active', true)->get();
        
        return view('vehicles.create', compact('transporters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only sales_manager and admin can create vehicles
        if (!Auth::user()->role === 'admin' && !Auth::user()->role === 'sales_manager') {
            return redirect()->route('vehicles.index')
                ->with('error', 'You do not have permission to add a new vehicle.');
        }
        
        $validated = $request->validate([
            'vin' => 'required|string|unique:vehicles,vin',
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'stock_number' => 'nullable|string|max:255|unique:vehicles,stock_number',
            'color' => 'nullable|string|max:255',
            'trim' => 'nullable|string|max:255',
            'mileage' => 'nullable|integer',
            'purchased_from' => 'nullable|string|max:255',
            'purchase_location' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'is_arbitrable' => 'nullable|boolean',
            'transporter_id' => 'nullable|exists:users,id'
        ]);
        
        // Set default stage to intake
        $validated['current_stage'] = 'intake';
        $validated['stage_updated_at'] = now();
        
        if ($request->has('transporter_id')) {
            $validated['transport_assigned_at'] = now();
        }
        
        $vehicle = Vehicle::create($validated);
        
        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle created successfully. The intake process has been started.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['tasks.workflowStage', 'tasks.vendor', 'tasks.assignedUser']);
        $stages = WorkflowStage::orderBy('order')->get();
        $currentStage = WorkflowStage::where('slug', $vehicle->current_stage)->first();
        
        return view('vehicles.show', compact('vehicle', 'stages', 'currentStage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        // Only sales_manager and admin can edit vehicles
        if (!Auth::user()->role === 'admin' && !Auth::user()->role === 'sales_manager') {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'You do not have permission to edit this vehicle.');
        }
        
        $transporters = User::where('role', 'transporter')->where('is_active', true)->get();
        
        return view('vehicles.edit', compact('vehicle', 'transporters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Only sales_manager and admin can update vehicles
        if (!Auth::user()->role === 'admin' && !Auth::user()->role === 'sales_manager') {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'You do not have permission to update this vehicle.');
        }
        
        $validated = $request->validate([
            'vin' => 'required|string|unique:vehicles,vin,' . $vehicle->id,
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'stock_number' => 'nullable|string|max:255|unique:vehicles,stock_number,' . $vehicle->id,
            'color' => 'nullable|string|max:255',
            'trim' => 'nullable|string|max:255',
            'mileage' => 'nullable|integer',
            'purchased_from' => 'nullable|string|max:255',
            'purchase_location' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'is_arbitrable' => 'nullable|boolean',
            'transporter_id' => 'nullable|exists:users,id'
        ]);
        
        // Handle transporter assignment
        if ($request->has('transporter_id') && $vehicle->transporter_id != $request->transporter_id) {
            $validated['transport_assigned_at'] = now();
        }
        
        $vehicle->update($validated);
        
        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle information updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Only admin can delete vehicles
        if (!Auth::user()->role === 'admin') {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'You do not have permission to delete vehicles.');
        }
        
        // We don't actually delete vehicles, just archive them
        $vehicle->update([
            'is_archived' => true
        ]);
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle has been archived.');
    }
    
    /**
     * Update the workflow stage of a vehicle.
     */
    public function updateStage(Request $request, Vehicle $vehicle)
    {
        // Only sales_manager and admin can update stages
        if (!Auth::user()->role === 'admin' && !Auth::user()->role === 'sales_manager') {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'You do not have permission to update vehicle stages.');
        }
        
        $validated = $request->validate([
            'stage' => 'required|exists:workflow_stages,slug',
        ]);
        
        // Check if we're moving to frontline stage
        if ($validated['stage'] === 'frontline') {
            $vehicle->update([
                'current_stage' => $validated['stage'],
                'stage_updated_at' => now(),
                'is_frontline_ready' => true
            ]);
        } else {
            $vehicle->update([
                'current_stage' => $validated['stage'],
                'stage_updated_at' => now()
            ]);
        }
        
        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle stage updated successfully.');
    }
}
