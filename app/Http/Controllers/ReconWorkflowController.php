<?php

namespace App\Http\Controllers;

use App\Models\ReconWorkflow;
use App\Models\Vehicle;
use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReconWorkflowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = ReconWorkflow::with(['vehicle', 'startedBy']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $workflows = $query->latest('updated_at')->paginate(10);
        
        return view('recon.workflows.index', compact('workflows', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get vehicles that don't have an active workflow
        $vehicles = Vehicle::whereNotIn('id', function($query) {
            $query->select('vehicle_id')
                ->from('recon_workflows')
                ->whereIn('status', ['in_progress', 'on_hold']);
        })->get();
        
        return view('recon.workflows.create', compact('vehicles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'notes' => 'nullable|string',
        ]);
        
        // Check if vehicle already has an active workflow
        $existingWorkflow = ReconWorkflow::where('vehicle_id', $validated['vehicle_id'])
            ->whereIn('status', ['in_progress', 'on_hold'])
            ->first();
            
        if ($existingWorkflow) {
            return redirect()->route('recon.workflows.index')
                ->with('error', 'This vehicle already has an active reconditioning workflow.');
        }
        
        DB::transaction(function() use ($validated) {
            // Create the workflow
            $workflow = ReconWorkflow::create([
                'vehicle_id' => $validated['vehicle_id'],
                'status' => 'in_progress',
                'started_by' => Auth::id(),
                'started_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);
            
            // Create inspection items for each category
            $categories = InspectionCategory::where('is_active', true)->get();
            
            foreach ($categories as $category) {
                // Create default inspection items based on category
                $this->createDefaultInspectionItems($workflow, $category);
            }
            
            // Update the workflow with the total number of items
            $totalItems = $workflow->inspectionItems()->count();
            $workflow->update(['total_items' => $totalItems]);
            
            // Update vehicle status
            Vehicle::where('id', $validated['vehicle_id'])->update(['current_stage' => 'recon']);
        });
        
        return redirect()->route('recon.workflows.index')
            ->with('success', 'Reconditioning workflow created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReconWorkflow $workflow)
    {
        $workflow->load(['vehicle', 'startedBy', 'completedBy', 'inspectionItems.category', 'inspectionItems.photos', 'inspectionItems.assignedVendor']);
        
        // Group inspection items by category
        $itemsByCategory = $workflow->inspectionItems->groupBy('category_id');
        
        // Get all active categories
        $categories = InspectionCategory::where('is_active', true)->orderBy('order')->get();
        
        // Get vendors for assignments
        $vendors = Vendor::where('is_active', true)
            ->where(function($query) {
                $query->where('type', 'service')
                    ->orWhere('type', 'parts');
            })
            ->get();
        
        return view('recon.workflows.show', compact('workflow', 'itemsByCategory', 'categories', 'vendors'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReconWorkflow $workflow)
    {
        return view('recon.workflows.edit', compact('workflow'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReconWorkflow $workflow)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,on_hold,completed',
            'notes' => 'nullable|string',
        ]);
        
        // If status is changing to completed, validate that all items are completed
        if ($validated['status'] === 'completed' && $workflow->status !== 'completed') {
            $pendingItems = $workflow->inspectionItems()->where('is_completed', false)->count();
            
            if ($pendingItems > 0) {
                return redirect()->route('recon.workflows.show', $workflow)
                    ->with('error', "Cannot complete workflow. There are still {$pendingItems} pending inspection items.");
            }
            
            $validated['completed_by'] = Auth::id();
            $validated['completed_at'] = now();
            
            // Update vehicle status
            $workflow->vehicle()->update(['current_stage' => 'frontline']);
        }
        
        $workflow->update($validated);
        
        return redirect()->route('recon.workflows.show', $workflow)
            ->with('success', 'Workflow updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReconWorkflow $workflow)
    {
        // Only allow deletion if the workflow has no completed items
        if ($workflow->completed_items > 0) {
            return redirect()->route('recon.workflows.index')
                ->with('error', 'Cannot delete a workflow with completed items.');
        }
        
        $workflow->delete();
        
        return redirect()->route('recon.workflows.index')
            ->with('success', 'Workflow deleted successfully.');
    }
    
    /**
     * Create default inspection items for a category.
     */
    private function createDefaultInspectionItems(ReconWorkflow $workflow, InspectionCategory $category)
    {
        $vehicle = $workflow->vehicle;
        
        switch ($category->slug) {
            case 'test-drive':
                $items = [
                    ['name' => 'Transmission', 'description' => 'Check for smooth shifting and proper operation'],
                    ['name' => 'Engine Performance', 'description' => 'Check for smooth acceleration, no misfires'],
                    ['name' => 'Suspension', 'description' => 'Check for unusual noises, bouncing, pulling'],
                    ['name' => '4x4 System', 'description' => 'Test 4x4 engagement and operation (if applicable)'],
                    ['name' => 'Steering', 'description' => 'Check for tightness, alignment, no pulling'],
                ];
                break;
                
            case 'feature-check':
                $items = [
                    ['name' => 'Exterior Lights', 'description' => 'Check all exterior lights for proper operation'],
                    ['name' => 'Wipers', 'description' => 'Check wiper operation and condition'],
                    ['name' => 'Horn', 'description' => 'Check horn operation'],
                    ['name' => 'AC/Heat', 'description' => 'Check AC and heater operation'],
                    ['name' => 'Windows & Locks', 'description' => 'Check all windows and door locks'],
                    ['name' => 'Radio & Entertainment', 'description' => 'Check radio, speakers, and entertainment features'],
                    ['name' => 'Navigation', 'description' => 'Check navigation system if equipped'],
                    ['name' => 'Parking Sensors', 'description' => 'Check parking sensors if equipped'],
                    ['name' => 'Backup Camera', 'description' => 'Check backup camera if equipped'],
                ];
                break;
                
            case 'tires':
                $items = [
                    ['name' => 'Front Left Tire', 'description' => 'Check tread depth and condition'],
                    ['name' => 'Front Right Tire', 'description' => 'Check tread depth and condition'],
                    ['name' => 'Rear Left Tire', 'description' => 'Check tread depth and condition'],
                    ['name' => 'Rear Right Tire', 'description' => 'Check tread depth and condition'],
                    ['name' => 'Spare Tire', 'description' => 'Check presence and condition of spare tire'],
                ];
                break;
                
            case 'brakes':
                $items = [
                    ['name' => 'Front Brakes', 'description' => 'Check pad thickness and rotor condition'],
                    ['name' => 'Rear Brakes', 'description' => 'Check pad/shoe thickness and rotor/drum condition'],
                    ['name' => 'Parking Brake', 'description' => 'Check parking brake operation'],
                    ['name' => 'Brake Lines', 'description' => 'Check for leaks or damage to brake lines'],
                ];
                break;
                
            case 'fluids':
                $items = [
                    ['name' => 'Engine Oil', 'description' => 'Check level and condition'],
                    ['name' => 'Coolant', 'description' => 'Check level and condition'],
                    ['name' => 'Brake Fluid', 'description' => 'Check level'],
                    ['name' => 'Power Steering Fluid', 'description' => 'Check level if applicable'],
                    ['name' => 'Transmission Fluid', 'description' => 'Check level and condition if accessible'],
                    ['name' => 'Windshield Washer Fluid', 'description' => 'Check level and fill'],
                ];
                break;
                
            default:
                // For other categories, create a generic inspection item
                $items = [
                    ['name' => $category->name, 'description' => 'Inspect and document any issues']
                ];
        }
        
        // Create all the items
        foreach ($items as $item) {
            InspectionItem::create([
                'category_id' => $category->id,
                'name' => $item['name'],
                'description' => $item['description'],
                'status' => 'pending',
                'is_vendor_visible' => false,
                'is_completed' => false,
                'vehicle_id' => $vehicle->id,
                'recon_workflow_id' => $workflow->id,
            ]);
        }
    }
    
    /**
     * Upload a diagram for exterior damage.
     */
    public function uploadDiagram(Request $request, ReconWorkflow $workflow)
    {
        $validated = $request->validate([
            'diagram_type' => 'required|in:exterior,interior',
            'diagram_data' => 'required|json',
        ]);
        
        // Store diagram data in the workflow
        $diagrams = $workflow->diagrams ?? [];
        $diagrams[$validated['diagram_type']] = json_decode($validated['diagram_data'], true);
        
        $workflow->update(['diagrams' => $diagrams]);
        
        return response()->json(['success' => true]);
    }
}
