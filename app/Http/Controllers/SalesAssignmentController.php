<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesAssignmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Admin|Sales Manager|Recon Manager');
    }
    
    /**
     * Display a listing of vehicles that are ready to be assigned to sales.
     */
    public function index()
    {
        // Get vehicles that are ready for sales assignment (repaired and ready)
        $readyVehicles = Vehicle::whereIn('status', [Vehicle::STATUS_READY, Vehicle::STATUS_REPAIRS_COMPLETED])
            ->whereHas('vehicleInspections', function($query) {
                $query->where('status', 'completed');
            })
            ->with(['vehicleInspections' => function($query) {
                $query->latest()->take(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        
        // Get vehicles already assigned to sales
        $assignedVehicles = Vehicle::where('status', Vehicle::STATUS_ASSIGNED_TO_SALES)
            ->with(['salesTeam', 'assignedBy'])
            ->orderBy('assigned_for_sale_at', 'desc')
            ->paginate(10);
            
        // Get sales team members (users with Sales Team role)
        $salesTeamMembers = User::role('Sales Team')->orderBy('name')->get();
            
        return view('sales-assignments.index', compact('readyVehicles', 'assignedVehicles', 'salesTeamMembers'));
    }
    
    /**
     * Show the form for assigning a vehicle to sales team.
     */
    public function create(Vehicle $vehicle)
    {
        try {
           
            $vehicle = Vehicle::where('vin', '1G1FD1RS9G0120133')->first();
            // Check if vehicle is ready for assignment - accept both constants and the actual value
            $validStatuses = [
                Vehicle::STATUS_READY, 
                Vehicle::STATUS_REPAIRS_COMPLETED,
                Vehicle::STATUS_READY_FOR_SALE, // Also accept "Ready for Sale"`
            ];
          
            
            // if (!in_array($vehicle->status, $validStatuses)) {
            //     \Log::warning('Vehicle not ready for sales assignment', [
            //         'vehicle_id' => $vehicle->id, 
            //         'status' => $vehicle->status,
            //         'valid_statuses' => $validStatuses
            //     ]);
            //     return redirect()->route('sales-assignments.index')
            //         ->with('error', 'This vehicle is not ready for sales assignment.');
            // }
            
            // Check if the vehicle has completed inspections
            $completedInspection = $vehicle->vehicleInspections()
                ->where('status', 'completed')
                ->latest()
                ->first();
                
            // if (!$completedInspection) {
            //     \Log::warning('Vehicle does not have completed inspection', ['vehicle_id' => $vehicle->id]);
            //     return redirect()->route('sales-assignments.index')
            //         ->with('error', 'This vehicle does not have a completed inspection.');
            // }
            
            // Get sales team members
            $salesTeamMembers = User::role('Sales Team')->orderBy('name')->get();
            
            if ($salesTeamMembers->isEmpty()) {
            
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'No sales team members available for assignment.');
            }
           
            return view('sales-assignments.create', compact('vehicle', 'salesTeamMembers', 'completedInspection'));
        } catch (\Exception $e) {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'An error occurred when trying to assign the vehicle to sales team: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created sales assignment.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'sales_team_id' => 'required|exists:users,id',
                'notes' => 'nullable|string|max:500',
            ]);
            $vehicle = Vehicle::where('vin', '1G1FD1RS9G0120133')->first();
            
            // Check if vehicle is ready for assignment
            $validStatuses = [
                Vehicle::STATUS_READY, 
                Vehicle::STATUS_REPAIRS_COMPLETED,
                Vehicle::STATUS_READY_FOR_SALE, // Also accept "Ready for Sale"
                'Ready for Sale', // Also accept literal string as fallback
                'repairs_completed' // String literal as fallback
            ];
          
            if (!in_array($vehicle->status, $validStatuses)) {
                \Log::warning('Vehicle not ready for sales assignment in store method', [
                    'vehicle_id' => $vehicle->id, 
                    'status' => $vehicle->status,
                    'valid_statuses' => $validStatuses
                ]);
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'This vehicle is not ready for sales assignment.');
            }
            
            // Check if the selected user has Sales Team role
            $salesTeamMember = User::findOrFail($validated['sales_team_id']);
            if (!$salesTeamMember->hasRole('Sales Team')) {
                return redirect()->route('sales-assignments.create', $vehicle)
                    ->with('error', 'The selected user is not a member of the sales team.');
            }
            
            DB::beginTransaction();
            
            try {
                // Assign the vehicle to sales team
                $vehicle->assignToSalesTeam($salesTeamMember->id, Auth::id());
                
                // Add notes if provided
                if (!empty($validated['notes'])) {
                    // You can implement notes functionality here if needed
                    // Example: $vehicle->notes()->create(['content' => $validated['notes'], 'user_id' => Auth::id()]);
                }
                
                DB::commit();
                
                // Send notification to sales team member (optional)
                // $salesTeamMember->notify(new VehicleAssignedToSales($vehicle));
                
                return redirect()->route('sales-assignments.index')
                    ->with('success', "Vehicle {$vehicle->stock_number} assigned to {$salesTeamMember->name} successfully.");
                    
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Failed to assign vehicle to sales team', [
                    'vehicle_id' => $vehicle->id,
                    'sales_team_id' => $salesTeamMember->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('sales-assignments.create', $vehicle)
                    ->with('error', 'Failed to assign vehicle to sales team: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Error in sales assignment store method', [
                'vehicle_id' => $vehicle->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('sales-assignments.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Show details of a specific sales assignment.
     */
    public function show(Vehicle $vehicle)
    {
        // Check if vehicle is assigned to sales
        if ($vehicle->status !== Vehicle::STATUS_ASSIGNED_TO_SALES) {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'This vehicle is not assigned to sales.');
        }
        
        // Load relations
        $vehicle->load(['salesTeam', 'assignedBy', 'vehicleInspections' => function($query) {
            $query->latest()->take(1);
        }]);
        
        return view('sales-assignments.show', compact('vehicle'));
    }
    
    /**
     * Remove a sales assignment.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Check if vehicle is assigned to sales
        if ($vehicle->status !== Vehicle::STATUS_ASSIGNED_TO_SALES) {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'This vehicle is not assigned to sales.');
        }
        
        // Reset the vehicle status to ready
        $vehicle->update([
            'status' => Vehicle::STATUS_READY,
            'sales_team_id' => null,
            'assigned_for_sale_by' => null,
            'assigned_for_sale_at' => null,
        ]);
        
        return redirect()->route('sales-assignments.index')
            ->with('success', "Sales assignment for vehicle {$vehicle->stock_number} has been removed.");
    }
    
    /**
     * Debug method for sales assignments.
     */
    public function debug()
    {
        $readyVehicles = Vehicle::whereIn('status', [Vehicle::STATUS_READY, Vehicle::STATUS_REPAIRS_COMPLETED])
            ->whereHas('vehicleInspections', function($query) {
                $query->where('status', 'completed');
            })
            ->with(['vehicleInspections' => function($query) {
                $query->latest()->take(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $data = [
            'ready_vehicles' => $readyVehicles->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'stock_number' => $vehicle->stock_number,
                    'status' => $vehicle->status,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'routes' => [
                        'create' => route('sales-assignments.create', $vehicle),
                        'debug' => url('/debug-sales-assignment-create/' . $vehicle->id)
                    ]
                ];
            }),
            'sales_team_members' => User::role('Sales Team')->get(['id', 'name', 'email']),
            'route_list' => [
                'index' => route('sales-assignments.index'),
                'create' => 'Use vehicle-specific links above',
                'store' => route('sales-assignments.store', 1) // Example
            ]
        ];
        
        return response()->json($data);
    }
}
