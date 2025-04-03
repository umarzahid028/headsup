<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Notifications\NewVehicleArrival;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$this->authorize('view vehicles');
        
        $vehicles = Vehicle::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $vehicles->where(function($query) use ($search) {
                $query->where('stock_number', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status')) {
            $vehicles->where('status', $request->input('status'));
        }
        
        $vehicles = $vehicles->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create vehicles');
        
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create vehicles');
        
        $validated = $request->validate([
            'stock_number' => 'required|unique:vehicles',
            'vin' => 'required|unique:vehicles',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'trim' => 'nullable|string',
            'date_in_stock' => 'nullable|date',
            'odometer' => 'nullable|integer',
            'exterior_color' => 'nullable|string',
            'interior_color' => 'nullable|string',
            'transmission' => 'nullable|string',
            'body_type' => 'nullable|string',
            'drive_train' => 'nullable|string',
            'engine' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'status' => 'nullable|string',
            'advertising_price' => 'nullable|numeric',
        ]);
        
        DB::beginTransaction();
        
        try {
            $vehicle = Vehicle::create($validated);
            
            // Get users with Admin role
            $admins = User::role('Admin')->get();
            
            // Get users with Sales Manager role
            $salesManagers = User::role('Sales Manager')->get();
            
            // Get users with Recon Manager role
            $reconManagers = User::role('Recon Manager')->get();
            
            // Combine all users to notify
            $managers = $admins->merge($salesManagers)->merge($reconManagers);
            
            if ($managers->isEmpty()) {
                \Log::warning("No Admins, Sales or Recon Managers found to notify about new vehicle {$vehicle->stock_number}");
            } else {
                foreach ($managers as $manager) {
                    $manager->notify(new NewVehicleArrival($vehicle));
                }
                \Log::info("Sent notifications about new vehicle {$vehicle->stock_number} to " . $managers->count() . " users");
            }
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Error creating vehicle: " . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        $validated = $request->validate([
            'stock_number' => 'required|string|max:255|unique:vehicles,stock_number,' . $id,
            'vin' => 'required|string|max:255|unique:vehicles,vin,' . $id,
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'trim' => 'nullable|string|max:255',
            'date_in_stock' => 'nullable|date',
            'odometer' => 'nullable|integer',
            'exterior_color' => 'nullable|string|max:255',
            'interior_color' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'body_type' => 'nullable|string|max:255',
            'drive_train' => 'nullable|string|max:255',
            'engine' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'transmission' => 'nullable|string|max:255',
            'transmission_type' => 'nullable|string|max:255',
            'advertising_price' => 'nullable|numeric',
        ]);
        
        DB::beginTransaction();
        
        try {
            $vehicle->update($validated);
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            $vehicle->delete();
            
            DB::commit();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle deleted successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the vehicle: ' . $e->getMessage());
        }
    }
} 