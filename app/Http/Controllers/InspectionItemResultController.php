<?php

namespace App\Http\Controllers;

use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionItemResultController extends Controller
{
    /**
     * Store a newly created inspection item result.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_inspection_id' => 'required|exists:vehicle_inspections,id',
            'inspection_item_id' => 'required|exists:inspection_items,id',
            'status' => 'required|in:pass,warning,fail,not_applicable',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Create the result
        $result = InspectionItemResult::create($validated);
        
        // Update the inspection status
        $inspection = $result->vehicleInspection;
        if ($inspection->status === 'pending') {
            $inspection->update(['status' => 'in_progress']);
        }
        
        // Update total cost
        $totalCost = $inspection->itemResults->sum('cost');
        $inspection->update(['total_cost' => $totalCost]);
        
        // Handle images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('repair-images', 'public');
                
                RepairImage::create([
                    'inspection_item_result_id' => $result->id,
                    'image_path' => $path,
                    'image_type' => $request->image_type ?? 'documentation',
                    'caption' => $request->caption ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Assessment saved successfully.');
    }

    /**
     * Update the specified inspection item result.
     */
    public function update(Request $request, InspectionItemResult $result)
    {
        $validated = $request->validate([
            'status' => 'required|in:pass,warning,fail,not_applicable',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Update the result
        $result->update($validated);
        
        // Update total cost for the inspection
        $inspection = $result->vehicleInspection;
        $totalCost = $inspection->itemResults->sum('cost');
        $inspection->update(['total_cost' => $totalCost]);
        
        // Handle images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('repair-images', 'public');
                
                RepairImage::create([
                    'inspection_item_result_id' => $result->id,
                    'image_path' => $path,
                    'image_type' => $request->image_type ?? 'documentation',
                    'caption' => $request->caption ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified inspection item result.
     */
    public function destroy(InspectionItemResult $result)
    {
        // Only allow deletion if no attachments and if inspection is not completed
        if ($result->vehicleInspection->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot delete a result from a completed inspection.');
        }
        
        // Delete associated images
        foreach ($result->repairImages as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
        
        $result->delete();
        
        return redirect()->back()->with('success', 'Assessment deleted successfully.');
    }
} 