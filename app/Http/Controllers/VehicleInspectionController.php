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
    public function __construct()
    {
        $this->middleware('auth');
       // $this->middleware('role_or_permission:Admin|Sales Manager|Recon Manager|view vehicles');
    }

    /**
     * Display a listing of the vehicle inspections.
     */
    public function index(Request $request)
    {
        $query = VehicleInspection::with([
            'vehicle.vehicleInspections.inspectionStage',
            'inspectionStage',
            'itemResults.inspectionItem',
            'itemResults.repairImages',
            'itemResults.assignedVendor'
        ])
        ->select([
            'vehicle_inspections.*',
            'vehicles.stock_number',
            'vehicles.year',
            'vehicles.make',
            'vehicles.model',
            'vehicles.vin'
        ])
        ->join('vehicles', 'vehicle_inspections.vehicle_id', '=', 'vehicles.id')
        ->whereIn('vehicle_inspections.id', function($query) {
            $query->selectRaw('MAX(vi2.id)')
                ->from('vehicle_inspections as vi2')
                ->groupBy('vi2.vehicle_id');
        });

        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vehicles.stock_number', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.vin', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.make', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.model', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('vehicle_inspections.status', $request->status);
        }

        // Get paginated results
        $inspections = $query->latest('vehicle_inspections.created_at')->paginate(10)->withQueryString();
        
        return view('inspection.inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new vehicle inspection.
     */
    public function create(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');
        
        // Get vehicles that haven't been inspected yet
        $vehicles = Vehicle::whereDoesntHave('vehicleInspections')
            ->whereHas('transports', function($query) {
                $query->where('status', 'delivered');
            })
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
        $inspection->load([
            'vehicle', 
            'inspectionStage.inspectionItems', 
            'user', 
            'vendor', 
            'itemResults.inspectionItem', 
            'itemResults.repairImages',
            'itemResults.assignedVendor'
        ]);
        
        // Create missing item results for all inspection items in this stage
        $existingItemIds = $inspection->itemResults->pluck('inspection_item_id')->toArray();
        $stageItems = $inspection->inspectionStage->inspectionItems;
        
        foreach ($stageItems as $item) {
            if (!in_array($item->id, $existingItemIds)) {
                $inspection->itemResults()->create([
                    'inspection_item_id' => $item->id,
                    'status' => 'pending',
                    'cost' => 0,
                    'actual_cost' => 0
                ]);
            }
        }
        
        // Reload the inspection with the new item results
        $inspection->load('itemResults.inspectionItem');
        
        // Calculate total costs
        $totalEstimatedCost = $inspection->itemResults->sum('cost');
        $totalActualCost = $inspection->itemResults->sum('actual_cost');
        
        // Update the inspection's total cost
        $inspection->update([
            'total_cost' => $totalEstimatedCost
        ]);
        
        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        
        return view('inspection.inspections.show', compact(
            'inspection', 
            'vendors',
            'totalEstimatedCost',
            'totalActualCost'
        ));
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
            'items.*.status' => 'required|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.requires_repair' => 'nullable|boolean',
            'items.*.cost' => 'nullable|numeric|min:0',
            'items.*.actual_cost' => 'nullable|numeric|min:0',
            'items.*.assigned_to_vendor_id' => 'nullable|exists:vendors,id',
            'items.*.repair_completed' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $totalEstimatedCost = 0;
            $totalActualCost = 0;

            foreach ($validated['items'] as $itemData) {
                $itemResult = InspectionItemResult::findOrFail($itemData['id']);
                
                // Ensure this item belongs to the current inspection
                if ($itemResult->vehicle_inspection_id !== $inspection->id) {
                    throw new \Exception("Item result doesn't belong to this inspection");
                }
                
                // Set requires_repair based on status
                $itemData['requires_repair'] = in_array($itemData['status'], ['warning', 'fail']);
                
                // Update the item result
                $itemResult->update($itemData);

                // Add to totals
                $totalEstimatedCost += $itemData['cost'] ?? 0;
                $totalActualCost += $itemData['actual_cost'] ?? 0;
            }
            
            // Update the inspection status if all items have been checked
            $this->updateInspectionStatus($inspection);
            
            // Update total costs
            $inspection->update([
                'total_cost' => $totalEstimatedCost
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
     * Show the comprehensive inspection form.
     */
    public function comprehensive(Vehicle $vehicle)
    {
        // Check if vehicle already has an inspection
        $existingInspection = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->latest()
            ->first();

        if ($existingInspection) {
            // Load existing inspection data
            $stages = InspectionStage::with(['inspectionItems' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])->where('is_active', true)->orderBy('order')->get();
            
            $vendors = Vendor::where('is_active', true)->orderBy('name')->get();

            // Pass the existing inspection data to the view
            return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors', 'existingInspection'));
        }
        
        // If no existing inspection, proceed with new inspection
        $stages = InspectionStage::with(['inspectionItems' => function ($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->where('is_active', true)->orderBy('order')->get();
        
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        
        return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors'));
    }

    /**
     * Store or update a comprehensive inspection.
     */
    public function comprehensiveStore(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'array',
            'items.*.status' => 'required|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.cost' => 'nullable|numeric|min:0',
            'items.*.images.*' => 'nullable|image|mimes:jpeg,png|max:5120',
        ]);

        // Check if vehicle already has an inspection
        $existingInspection = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->latest()
            ->first();

        DB::beginTransaction();
        
        try {
            if ($existingInspection) {
                // Update existing inspection
                foreach ($request->items as $itemId => $itemData) {
                    $item = InspectionItem::findOrFail($itemId);
                    $result = $existingInspection->itemResults()
                        ->where('inspection_item_id', $itemId)
                        ->first();

                    if (!$result) {
                        // Create new result if it doesn't exist
                        $result = $existingInspection->itemResults()->create([
                            'inspection_item_id' => $itemId,
                            'status' => $itemData['status'],
                            'notes' => $itemData['notes'] ?? null,
                            'cost' => $itemData['cost'] ?? 0,
                            'vendor_id' => $itemData['vendor_id'] ?? $request->vendor_id ?? null,
                            'requires_repair' => in_array($itemData['status'], ['warning', 'fail']),
                            'repair_completed' => false,
                        ]);
                    } else {
                        // Update existing result
                        $result->update([
                            'status' => $itemData['status'],
                            'notes' => $itemData['notes'] ?? null,
                            'cost' => $itemData['cost'] ?? 0,
                            'vendor_id' => $itemData['vendor_id'] ?? $request->vendor_id ?? null,
                            'requires_repair' => in_array($itemData['status'], ['warning', 'fail']),
                        ]);
                    }

                    // Handle new image uploads
                    if (isset($itemData['images']) && is_array($itemData['images'])) {
                        foreach ($itemData['images'] as $image) {
                            if ($image && $image->isValid()) {
                                $path = $image->store('inspection-images/' . $vehicle->id, 'public');
                                $result->repairImages()->create([
                                    'image_path' => $path,
                                    'image_type' => 'before',
                                    'caption' => 'Inspection image'
                                ]);
                            }
                        }
                    }
                }

                // Update inspection status and total cost
                $totalCost = $existingInspection->itemResults->sum('cost');
                $existingInspection->update([
                    'total_cost' => $totalCost,
                    'status' => 'completed',
                    'completed_date' => now()
                ]);

                $needsRepair = $existingInspection->itemResults()
                    ->whereIn('status', ['warning', 'fail'])
                    ->exists();

            } else {
                // Create new inspection (existing logic)
                // ... [Keep the existing creation logic here]
            }
            
            // Update vehicle status
            if ($needsRepair && $request->vendor_id) {
                $vehicle->update(['status' => 'repair_assigned']);
            } else if ($needsRepair) {
                $vehicle->update(['status' => 'needs_repair']);
            } else {
                $vehicle->update(['status' => 'ready']);
            }
            
            DB::commit();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Inspection ' . ($existingInspection ? 'updated' : 'completed') . ' successfully. ' . 
                ($needsRepair ? 'Repair items have been ' . ($request->vendor_id ? 'assigned to vendor.' : 'identified.') : 'Vehicle is ready.'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error ' . ($existingInspection ? 'updating' : 'saving') . ' inspection: ' . $e->getMessage())
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