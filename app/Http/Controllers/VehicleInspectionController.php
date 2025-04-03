<?php

namespace App\Http\Controllers;

use App\Models\InspectionStage;
use App\Models\Vehicle;
use App\Models\VehicleInspection;
use App\Models\Vendor;
use App\Models\InspectionItem;
use App\Models\InspectionItemResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleInspectionController extends Controller
{
    /**
     * Display a listing of the vehicle inspections.
     */
    public function index(Request $request)
    {
        $stageId = $request->query('stage_id');
        $status = $request->query('status');
        $vehicleId = $request->query('vehicle_id');

        $query = VehicleInspection::with(['vehicle', 'inspectionStage', 'user']);
        
        if ($stageId) {
            $query->where('inspection_stage_id', $stageId);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }
        
        $inspections = $query->latest()->paginate(10);
        $stages = InspectionStage::orderBy('order')->pluck('name', 'id');
        $statusOptions = [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];
        
        return view('inspection.inspections.index', compact(
            'inspections', 
            'stages', 
            'statusOptions', 
            'stageId', 
            'status', 
            'vehicleId'
        ));
    }

    /**
     * Show the form for creating a new vehicle inspection.
     */
    public function create(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');
        
        // Get vehicles with better display format
        $vehicles = Vehicle::where('transport_status', 'delivered')
            ->orderBy('stock_number')
            ->get(['id', 'stock_number', 'year', 'make', 'model'])
            ->mapWithKeys(function($vehicle) {
                return [
                    $vehicle->id => $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' (Stock #' . $vehicle->stock_number . ')'
                ];
            });
        
        // Add a highlighted prompt about comprehensive inspection
        session()->flash('info', 'Select a vehicle to begin the comprehensive inspection process.');
        
        return view('inspection.inspections.create', compact('vehicles', 'vehicleId'));
    }

    /**
     * Redirect legacy inspection creation to comprehensive view.
     * This method is preserved for backwards compatibility with any existing references.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        
        // Redirect to comprehensive inspection
        return redirect()->route('comprehensive.show', $vehicle)
            ->with('info', 'Using comprehensive inspection system for better workflow.');
    }

    /**
     * Display the specified vehicle inspection.
     */
    public function show(VehicleInspection $inspection)
    {
        $inspection->load(['vehicle', 'inspectionStage', 'user', 'vendor', 'itemResults.inspectionItem', 'itemResults.repairImages']);
        
        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        
        return view('inspection.inspections.show', compact('inspection', 'vendors'));
    }

    /**
     * Show the form for editing the specified vehicle inspection.
     */
    public function edit(VehicleInspection $inspection)
    {
        $inspection->load(['vehicle', 'inspectionStage']);
        
        $stages = InspectionStage::where('is_active', true)
            ->orderBy('order')
            ->pluck('name', 'id');
        
        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        
        return view('inspection.inspections.edit', compact('inspection', 'stages', 'vendors'));
    }

    /**
     * Update the specified vehicle inspection in storage.
     */
    public function update(Request $request, VehicleInspection $inspection)
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed',
        ]);

        // If status is changing to completed, set the completed date
        if ($validated['status'] === 'completed' && $inspection->status !== 'completed') {
            $validated['completed_date'] = now();
        } else if ($validated['status'] !== 'completed') {
            $validated['completed_date'] = null;
        }

        $inspection->update($validated);
        
        // If all inspections for this vehicle are completed, mark the vehicle as ready
        if ($validated['status'] === 'completed') {
            $vehicle = $inspection->vehicle;
            $incompleteInspections = VehicleInspection::where('vehicle_id', $vehicle->id)
                ->where('status', '!=', 'completed')
                ->count();
            
            if ($incompleteInspections === 0 && $vehicle->status !== 'ready') {
                $vehicle->update(['status' => 'ready']);
            }
        }

        return redirect()->route('inspection.inspections.show', $inspection)
            ->with('success', 'Vehicle inspection updated successfully.');
    }

    /**
     * Remove the specified vehicle inspection from storage.
     */
    public function destroy(VehicleInspection $inspection)
    {
        // Only allow deletion if the inspection is still pending and has no item results
        if ($inspection->status !== 'pending' || $inspection->itemResults()->count() > 0) {
            return redirect()->route('inspection.inspections.index')
                ->with('error', 'Cannot delete an inspection that is in progress or has results.');
        }

        $inspection->delete();

        return redirect()->route('inspection.inspections.index')
            ->with('success', 'Vehicle inspection deleted successfully.');
    }

    /**
     * Update inspection item results.
     */
    public function updateItems(Request $request, VehicleInspection $inspection)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_item_results,id',
            'items.*.status' => 'required|in:pass,fail,warning,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.requires_repair' => 'nullable|boolean',
            'items.*.repair_cost' => 'nullable|numeric|min:0',
            'items.*.assigned_to_vendor_id' => 'nullable|exists:vendors,id',
            'items.*.repair_completed' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $itemData) {
                $itemResult = InspectionItemResult::findOrFail($itemData['id']);
                
                // Ensure this item belongs to the current inspection
                if ($itemResult->vehicle_inspection_id !== $inspection->id) {
                    throw new \Exception("Item result doesn't belong to this inspection");
                }
                
                // Set defaults for checkbox fields
                $itemData['requires_repair'] = $itemData['requires_repair'] ?? false;
                $itemData['repair_completed'] = $itemData['repair_completed'] ?? false;
                
                $itemResult->update($itemData);
            }
            
            // Update the inspection status if all items have been checked
            $this->updateInspectionStatus($inspection);
            
            // Update total cost
            $inspection->update([
                'total_cost' => $inspection->calculateTotalCost()
            ]);
            
            DB::commit();
            
            return redirect()->route('inspection.inspections.show', $inspection)
                ->with('success', 'Inspection items updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('inspection.inspections.show', $inspection)
                ->with('error', 'Error updating inspection items: ' . $e->getMessage());
        }
    }

    /**
     * Update the inspection status based on item results.
     */
    private function updateInspectionStatus(VehicleInspection $inspection)
    {
        $inspection->refresh();
        
        $totalItems = $inspection->itemResults->count();
        $checkedItems = $inspection->itemResults->filter(function ($result) {
            return $result->status !== 'not_applicable';
        })->count();
        
        // If all items have been checked
        if ($totalItems > 0 && $checkedItems === $totalItems) {
            if ($inspection->status === 'pending') {
                $inspection->update(['status' => 'in_progress']);
            }
            
            // If all repairs are completed, mark as completed
            $requiresRepair = $inspection->countItemsRequiringRepair();
            $completedRepairs = $inspection->countCompletedRepairs();
            
            if ($requiresRepair > 0 && $requiresRepair === $completedRepairs) {
                $inspection->update([
                    'status' => 'completed',
                    'completed_date' => now()
                ]);
            }
        }
    }

    /**
     * Upload repair images.
     */
    public function uploadImages(Request $request, InspectionItemResult $result)
    {
        $validated = $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'image_type' => 'required|in:before,after,documentation',
            'caption' => 'nullable|string|max:255',
        ]);

        foreach ($request->file('images') as $image) {
            $path = $image->store('repair-images', 'public');
            
            $result->repairImages()->create([
                'image_path' => $path,
                'image_type' => $validated['image_type'],
                'caption' => $validated['caption'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }

    /**
     * Delete a repair image.
     */
    public function deleteImage(Request $request, $imageId)
    {
        $image = RepairImage::findOrFail($imageId);
        $image->delete();
        
        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Display the comprehensive inspection form for all stages at once.
     */
    public function comprehensive(Vehicle $vehicle)
    {
        $stages = InspectionStage::with(['inspectionItems' => function($query) {
            $query->where('is_active', true)->orderBy('id');
        }])->where('is_active', true)->orderBy('order')->get();
        
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors', 'users'));
    }

    /**
     * Store the comprehensive inspection for all stages at once.
     */
    public function comprehensiveStore(Request $request, Vehicle $vehicle)
    {
        // Validate the request
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'items' => 'required|array',
            'items.*' => 'array',
            'items.*.status' => 'required|in:pass,warning,fail',
            'items.*.notes' => 'nullable|string',
            'items.*.cost' => 'nullable|numeric|min:0',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
        ]);

        // Group items by their stage
        $stageItems = [];
        $needsRepair = false;
       
        foreach ($request->items as $itemId => $itemData) {
            $item = InspectionItem::findOrFail($itemId);
            $stageId = $item->inspection_stage_id;
            
            if (!isset($stageItems[$stageId])) {
                $stageItems[$stageId] = [];
            }
            
            // Track if any items need repair (warning or fail status)
            if ($itemData['status'] === 'warning' || $itemData['status'] === 'fail') {
                $needsRepair = true;
            }
            
            $stageItems[$stageId][$itemId] = $itemData;
        }
        
        DB::beginTransaction();
        
        try {
            // Create an inspection for each stage with items
            foreach ($stageItems as $stageId => $items) {
                // Create the vehicle inspection
                $inspection = VehicleInspection::create([
                    'vehicle_id' => $vehicle->id,
                    'inspection_stage_id' => $stageId,
                    'user_id' => $request->user_id,
                    'vendor_id' => $request->vendor_id, // Global vendor assignment if provided
                    'status' => 'in_progress',
                    'inspection_date' => now(),
                ]);
                
                // Create results for each item
                $totalCost = 0;
                foreach ($items as $itemId => $itemData) {
                    // Handle vendor assignment - if global vendor is set and no specific vendor for this item
                    $vendorId = $itemData['vendor_id'] ?? $request->vendor_id ?? null;
                    
                    // Create the result
                    $result = InspectionItemResult::create([
                        'vehicle_inspection_id' => $inspection->id,
                        'inspection_item_id' => $itemId,
                        'status' => $itemData['status'],
                        'notes' => $itemData['notes'] ?? null,
                        'cost' => $itemData['cost'] ?? 0,
                        'vendor_id' => $vendorId,
                        'requires_repair' => in_array($itemData['status'], ['warning', 'fail']),
                        'repair_completed' => false, // Initially not completed
                    ]);
                    
                    $totalCost += $result->cost;
                }
                
                // Update the inspection total cost
                $inspection->update([
                    'total_cost' => $totalCost,
                    'status' => 'completed',
                    'completed_date' => now()
                ]);
            }
            
            // Update vehicle status
            if ($needsRepair && $request->vendor_id) {
                // If repairs needed and vendor assigned, mark as 'repair_assigned'
                $vehicle->update(['status' => 'repair_assigned']);
            } else if ($needsRepair) {
                // If repairs needed but no vendor, mark as 'needs_repair'
                $vehicle->update(['status' => 'needs_repair']);
            } else {
                // No repairs needed, vehicle is ready
                $vehicle->update(['status' => 'ready']);
            }
            
            DB::commit();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Inspection completed successfully. ' . 
                ($needsRepair ? 'Repair items have been ' . ($request->vendor_id ? 'assigned to vendor.' : 'identified.') : 'Vehicle is ready.'));
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error saving inspection: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Start an inspection (redirects to comprehensive inspection).
     */
    public function startInspection(Vehicle $vehicle)
    {
        // Redirect to comprehensive inspection instead of step-by-step
        return redirect()->route('comprehensive.show', $vehicle)
            ->with('info', 'Using the comprehensive inspection workflow for better efficiency.');
    }

    /**
     * Mark an inspection as completed.
     */
    public function markComplete(Request $request, VehicleInspection $inspection)
    {
        // Check if all inspection items have been assessed
        $totalItems = $inspection->inspectionStage->inspectionItems->count();
        $assessedItems = $inspection->itemResults->whereIn('status', ['pass', 'warning', 'fail'])->count();
        
        if ($assessedItems < $totalItems) {
            return redirect()->back()->with('error', 'All inspection items must be assessed before completing the inspection.');
        }
        
        $inspection->update([
            'status' => 'completed',
            'completed_date' => now()
        ]);
        
        // Check if all inspections for this vehicle are completed
        $vehicle = $inspection->vehicle;
        $pendingInspections = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        if ($pendingInspections === 0) {
            $vehicle->update(['status' => 'ready']);
        }
        
        return redirect()->route('inspection.inspections.index')
            ->with('success', 'Inspection marked as completed successfully.');
    }
} 