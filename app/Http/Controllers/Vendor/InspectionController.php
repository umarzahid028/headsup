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

        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
            'actual_cost' => 'required_if:status,completed|nullable|numeric|min:0',
            'completion_notes' => 'nullable|string',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'actual_cost' => $validated['status'] === 'completed' ? $validated['actual_cost'] : null,
            'completion_notes' => $validated['completion_notes'] ?? null,
            'completed_at' => now(),
            'repair_completed' => $validated['status'] === 'completed',
        ];

        $item->update($updateData);

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

        return redirect()->back()->with('success', 'Inspection item updated successfully.');
    }
} 