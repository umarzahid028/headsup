<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VehicleInspection;
use App\Models\InspectionItemResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InspectionController extends Controller
{
    /**
     * Display a listing of the assigned inspections.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $assignedInspections = VehicleInspection::with(['vehicle', 'inspectionItems'])
            ->whereHas('inspectionItems', function ($query) use ($user) {
                $query->where('vendor_id', $user->id)
                    ->whereIn('status', ['pending', 'diagnostic', 'in_progress']);
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
        
        // Ensure the vendor has access to this inspection
        if (!$inspection->inspectionItems()->where('vendor_id', $user->id)->exists()) {
            abort(403);
        }

        $inspection->load(['vehicle', 'inspectionItems' => function ($query) use ($user) {
            $query->where('vendor_id', $user->id);
        }]);

        return view('vendor.inspections.show', compact('inspection'));
    }

    /**
     * Update the inspection item status.
     */
    public function updateItemStatus(Request $request, InspectionItemResult $item)
    {
        $user = auth()->user();
        
        // Ensure the vendor owns this item
        if ($item->vendor_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:repair,replace',
            'notes' => 'nullable|string',
        ]);

        $item->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'completed_at' => now(),
        ]);

        // Check if all items are completed
        $inspection = $item->vehicleInspection;
        $allCompleted = $inspection->inspectionItems()
            ->where('vendor_id', $user->id)
            ->whereNull('completed_at')
            ->doesntExist();

        if ($allCompleted) {
            $inspection->update([
                'status' => 'completed',
                'completed_date' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Inspection item updated successfully.');
    }
} 