<?php

namespace App\Http\Controllers;

use App\Models\TimeBasedAlert;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimeBasedAlertController extends Controller
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
        $this->authorize('view alerts');
        
        $alerts = TimeBasedAlert::query()
            ->with('alertable', 'creator');
        
        // Apply filters
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $alerts->where('is_active', true);
            } elseif ($status === 'resolved') {
                $alerts->where('is_active', false);
            }
        } else {
            // Default to active alerts
            $alerts->where('is_active', true);
        }
        
        if ($request->has('type')) {
            $alerts->where('alert_type', $request->input('type'));
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $alerts->where('name', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        }
        
        $alerts = $alerts->latest()
            ->paginate(15)
            ->withQueryString();
        
        return view('alerts.index', compact('alerts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create alerts');
        
        $vehicleId = $request->input('vehicle_id');
        $vehicle = null;
        
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
        }
        
        return view('alerts.create', compact('vehicle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create alerts');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'alert_type' => 'required|string|in:vehicle_stage,task_deadline,custom',
            'alertable_id' => 'required|integer',
            'alertable_type' => 'required|string|in:App\\Models\\Vehicle,App\\Models\\Task',
            'warning_threshold' => 'required|integer|min:1',
            'critical_threshold' => 'required|integer|gt:warning_threshold',
            'notes' => 'nullable|string',
        ]);
        
        // Set created_by
        $validated['created_by'] = Auth::id();
        $validated['is_active'] = true;
        
        $alert = TimeBasedAlert::create($validated);
        
        // Check if this is a vehicle alert
        if ($validated['alertable_type'] === 'App\\Models\\Vehicle') {
            $vehicle = Vehicle::find($validated['alertable_id']);
            
            if ($vehicle) {
                // Record in vehicle timeline
                $vehicle->recordTimelineEvent(
                    'alert_created',
                    null,
                    $validated['name'],
                    'Time-based alert created: ' . $validated['name']
                );
            }
        }
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alert created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TimeBasedAlert $alert)
    {
        $this->authorize('view alerts');
        
        $alert->load('alertable', 'creator');
        
        return view('alerts.show', compact('alert'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeBasedAlert $alert)
    {
        $this->authorize('edit alerts');
        
        return view('alerts.edit', compact('alert'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeBasedAlert $alert)
    {
        $this->authorize('edit alerts');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'warning_threshold' => 'required|integer|min:1',
            'critical_threshold' => 'required|integer|gt:warning_threshold',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);
        
        // If alert is being resolved, set the resolved_at timestamp
        if ($alert->is_active && (!isset($validated['is_active']) || !$validated['is_active'])) {
            $validated['resolved_at'] = now();
        }
        
        $alert->update($validated);
        
        // Check if this is a vehicle alert
        if ($alert->alertable_type === 'App\\Models\\Vehicle') {
            $vehicle = Vehicle::find($alert->alertable_id);
            
            if ($vehicle) {
                // Record in vehicle timeline
                $vehicle->recordTimelineEvent(
                    'alert_updated',
                    null,
                    $alert->name,
                    'Time-based alert updated: ' . $alert->name
                );
            }
        }
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alert updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeBasedAlert $alert)
    {
        $this->authorize('delete alerts');
        
        // Check if this is a vehicle alert
        if ($alert->alertable_type === 'App\\Models\\Vehicle') {
            $vehicle = Vehicle::find($alert->alertable_id);
            
            if ($vehicle) {
                // Record in vehicle timeline
                $vehicle->recordTimelineEvent(
                    'alert_deleted',
                    null,
                    $alert->name,
                    'Time-based alert deleted: ' . $alert->name
                );
            }
        }
        
        $alert->delete();
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alert deleted successfully.');
    }
    
    /**
     * Resolve an alert.
     */
    public function resolve(TimeBasedAlert $alert)
    {
        $this->authorize('edit alerts');
        
        $alert->resolve();
        
        // Check if this is a vehicle alert
        if ($alert->alertable_type === 'App\\Models\\Vehicle') {
            $vehicle = Vehicle::find($alert->alertable_id);
            
            if ($vehicle) {
                // Record in vehicle timeline
                $vehicle->recordTimelineEvent(
                    'alert_resolved',
                    null,
                    $alert->name,
                    'Time-based alert resolved: ' . $alert->name
                );
            }
        }
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alert resolved successfully.');
    }
    
    /**
     * Create default vehicle stage alerts.
     */
    public function createDefaultVehicleAlerts(Vehicle $vehicle)
    {
        $this->authorize('create alerts');
        
        // Create standard alerts for different vehicle stages
        $defaultAlerts = [
            [
                'name' => 'Vehicle in New Arrival stage',
                'alert_type' => 'vehicle_stage',
                'warning_threshold' => 24, // 24 hours
                'critical_threshold' => 48, // 48 hours
                'notes' => 'Alert when vehicle stays in New Arrival stage for too long',
            ],
            [
                'name' => 'Vehicle in Arbitration stage',
                'alert_type' => 'vehicle_stage',
                'warning_threshold' => 72, // 3 days
                'critical_threshold' => 120, // 5 days
                'notes' => 'Alert when vehicle stays in Arbitration stage for too long',
            ],
            [
                'name' => 'Vehicle in Recon stage',
                'alert_type' => 'vehicle_stage',
                'warning_threshold' => 48, // 2 days
                'critical_threshold' => 96, // 4 days
                'notes' => 'Alert when vehicle stays in Recon stage for too long',
            ],
        ];
        
        $createdCount = 0;
        
        foreach ($defaultAlerts as $alertData) {
            // Check if alert already exists
            $existingAlert = TimeBasedAlert::where('alertable_type', 'App\\Models\\Vehicle')
                ->where('alertable_id', $vehicle->id)
                ->where('name', $alertData['name'])
                ->exists();
                
            if (!$existingAlert) {
                TimeBasedAlert::create([
                    'name' => $alertData['name'],
                    'alert_type' => $alertData['alert_type'],
                    'alertable_type' => 'App\\Models\\Vehicle',
                    'alertable_id' => $vehicle->id,
                    'warning_threshold' => $alertData['warning_threshold'],
                    'critical_threshold' => $alertData['critical_threshold'],
                    'notes' => $alertData['notes'],
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);
                
                $createdCount++;
            }
        }
        
        if ($createdCount > 0) {
            // Record in vehicle timeline
            $vehicle->recordTimelineEvent(
                'alerts_created',
                null,
                $createdCount . ' alerts',
                'Created ' . $createdCount . ' default time-based alerts'
            );
        }
        
        return redirect()->route('alerts.index', ['alertable_type' => 'App\\Models\\Vehicle', 'alertable_id' => $vehicle->id])
            ->with('success', $createdCount . ' default alerts created successfully.');
    }
}
