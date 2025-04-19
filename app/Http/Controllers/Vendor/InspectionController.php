<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VehicleInspection;
use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\InspectionItem;

class InspectionController extends Controller
{
    /**
     * Display a listing of the assigned inspections.
     */
    public function index(): View
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }
        
        $assignedInspections = VehicleInspection::with(['vehicle', 'itemResults'])
            ->whereHas('itemResults', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id)
                    ->where(function($q) {
                        $q->whereIn('status', ['pending', 'fail', 'warning'])
                            ->orWhereNotNull('diagnostic_status')
                            ->orWhere('requires_repair', true);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.inspections.index', compact('assignedInspections'));
    }

    /**
     * Display the specified inspection.
     */
    public function show(VehicleInspection $inspection): View
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Ensure the vendor has access to this inspection
        if (!$inspection->itemResults()->where('vendor_id', $vendor->id)->exists()) {
            abort(403);
        }

        $inspection->load([
            'vehicle',
            'itemResults' => function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            },
            'itemResults.inspectionItem'
        ]);

        return view('vendor.inspections.show', compact('inspection'));
    }

    /**
     * Update the inspection item status.
     * 
     * Sales Manager statuses: pass, warning (repair), fail (replace)
     * Vendor statuses: in_progress, completed, cancelled
     */
    public function updateItemStatus(Request $request, InspectionItemResult $item)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Ensure the vendor owns this item
        if ($item->vendor_id !== $vendor->id) {
            abort(403);
        }

        // Ensure the item is in a status that can be acted upon by vendors
        // Only items marked for repair (warning) or replacement (fail) can be updated
        if (!in_array($item->status, ['warning', 'fail', 'in_progress'])) {
            return redirect()->back()->with('error', 'This item cannot be updated. It must be marked for repair or replacement first.');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'actual_cost' => 'required_if:status,completed|nullable|numeric|min:0',
            'completion_notes' => 'nullable|string',
        ]);

        // Different handling based on status
        if ($validated['status'] === 'in_progress') {
            $updateData = [
                'status' => 'in_progress',
                'started_at' => now(),
            ];
        } else {
            // For completed status, we need to determine if this was originally 
            // a "repair" (warning) or "replace" (fail) item
            if ($validated['status'] === 'completed') {
                // If status was originally 'warning' (repair) or 'fail' (replace)
                // Keep track that repair was completed
                $originalStatus = $item->status === 'warning' ? 'repair' : 'replace';
                
                $updateData = [
                    'status' => 'completed',
                    'actual_cost' => $validated['actual_cost'],
                    'completion_notes' => $validated['completion_notes'] ?? null,
                    'completed_at' => now(),
                    'repair_completed' => true,
                ];
            } else {
                // For cancelled status
                $updateData = [
                    'status' => 'cancelled',
                    'completion_notes' => $validated['completion_notes'] ?? null,
                    'completed_at' => now(),
                    'repair_completed' => false,
                ];
            }
        }

        $item->update($updateData);

        // Only check for all completed if marking items as completed/cancelled
        if ($validated['status'] !== 'in_progress') {
            // Check if all items are completed
            $inspection = $item->vehicleInspection;
            $allCompleted = $inspection->itemResults()
                ->where('vendor_id', $vendor->id)
                ->whereNull('completed_at')
                ->doesntExist();

            if ($allCompleted) {
                $inspection->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                ]);
            }
        }

        $statusMessages = [
            'in_progress' => 'Work started on this item.',
            'completed' => 'Inspection item marked as completed.',
            'cancelled' => 'Inspection item cancelled.'
        ];

        return redirect()->back()->with('success', $statusMessages[$validated['status']]);
    }

    /**
     * Upload images for an inspection item
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\InspectionItem $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadImages(Request $request, InspectionItem $item)
    {
        $request->validate([
            'image_type' => 'required|in:before,after,documentation',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        if (!$request->hasFile('images')) {
            return redirect()->back()->with('error', 'No images were uploaded.');
        }

        $vendor = auth()->user()->vendor;

        // Find the inspection item result for this vendor
        $itemResult = InspectionItemResult::where('inspection_item_id', $item->id)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        // Ensure this vendor has access to this inspection
        if ($itemResult->vehicleInspection->vendor_id !== $vendor->id) {
            abort(403, 'You do not have permission to modify this inspection.');
        }

        // Ensure item is in progress
        if ($itemResult->status !== 'in_progress') {
            return redirect()->back()->with('error', 'You can only upload images for items that are in progress.');
        }

        foreach ($request->file('images') as $image) {
            $path = $image->store('inspection_images', 'public');
            
            RepairImage::create([
                'inspection_item_result_id' => $itemResult->id,
                'image_path' => $path,
                'image_type' => $request->image_type,
                'caption' => $request->caption,
            ]);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }
} 