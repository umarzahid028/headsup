<?php

namespace App\Http\Controllers;

use App\Models\VehicleInspection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        // Get all assigned inspections with related data
        $assignedInspections = VehicleInspection::with(['vehicle', 'inspectionItems'])
            ->whereHas('inspectionItems', function ($query) use ($user) {
                $query->where('vendor_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Pending inspections (diagnostic or pending)
        $pendingInspections = $assignedInspections->filter(function ($inspection) {
            return $inspection->inspectionItems->contains(function ($item) {
                return in_array($item->status, ['pending', 'diagnostic', 'in_progress']);
            });
        });

        // Completed inspections
        $completedInspections = $assignedInspections->filter(function ($inspection) {
            return $inspection->inspectionItems->every(function ($item) {
                return in_array($item->status, ['pass', 'completed', 'cancelled']);
            });
        });

        // Calculate dashboard statistics
        $stats = [
            'total_assigned' => $assignedInspections->count(),
            'pending_count' => $pendingInspections->count(),
            'completed_count' => $completedInspections->count(),
            'urgent_count' => $pendingInspections->filter(function ($inspection) {
                return $inspection->created_at->diffInDays() > 2;
            })->count(),
            'this_week_completed' => $completedInspections->filter(function ($inspection) {
                return $inspection->completed_date?->isCurrentWeek();
            })->count(),
            'total_items' => $assignedInspections->sum(function ($inspection) use ($user) {
                return $inspection->inspectionItems->where('vendor_id', $user->id)->count();
            }),
            'pending_approval' => $assignedInspections->filter(function ($inspection) {
                return $inspection->inspectionItems->contains('status', 'pending_approval');
            })->count(),
        ];

        // Get recent activity
        $recentActivity = $assignedInspections
            ->flatMap(function ($inspection) use ($user) {
                return $inspection->inspectionItems
                    ->where('vendor_id', $user->id)
                    ->map(function ($item) use ($inspection) {
                        return [
                            'type' => 'inspection_update',
                            'inspection' => $inspection,
                            'item' => $item,
                            'date' => $item->updated_at,
                            'status' => $item->status,
                        ];
                    });
            })
            ->sortByDesc('date')
            ->take(10);

        return view('vendor.dashboard', compact(
            'pendingInspections',
            'completedInspections',
            'stats',
            'recentActivity'
        ));
    }

    public function inspectionHistory(): View
    {
        $user = auth()->user();
        
        $completedInspections = VehicleInspection::with(['vehicle', 'inspectionItems'])
            ->whereHas('inspectionItems', function ($query) use ($user) {
                $query->where('vendor_id', $user->id)
                    ->whereIn('status', ['pass', 'completed', 'cancelled']);
            })
            ->orderBy('completed_date', 'desc')
            ->get();

        return view('vendor.inspection-history', compact('completedInspections'));
    }

    public function showInspection(VehicleInspection $inspection): View
    {
        $user = auth()->user();
        
        // Ensure the vendor has access to this inspection
        if (!$inspection->inspectionItems()->where('vendor_id', $user->id)->exists()) {
            abort(403);
        }

        $inspection->load(['vehicle', 'inspectionItems' => function ($query) use ($user) {
            $query->where('vendor_id', $user->id);
        }]);

        return view('vendor.inspection-details', compact('inspection'));
    }

    public function submitEstimate(Request $request, VehicleInspection $inspection)
    {
        $user = auth()->user();
        
        // Validate the request
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_items,id',
            'items.*.estimated_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        // Ensure vendor has access to these items
        foreach ($validated['items'] as $item) {
            $inspectionItem = $inspection->inspectionItems()->find($item['id']);
            
            if (!$inspectionItem || $inspectionItem->vendor_id !== $user->id) {
                abort(403);
            }

            $inspectionItem->update([
                'estimated_cost' => $item['estimated_cost'],
                'notes' => $item['notes'],
                'status' => 'pending_approval',
                'estimate_submitted_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Estimate submitted successfully.');
    }

    public function updateServiceStatus(Request $request, VehicleInspection $inspection)
    {
        $user = auth()->user();
        
        // Validate the request
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_items,id',
            'items.*.status' => 'required|in:completed,cancelled',
            'items.*.actual_cost' => 'required_if:items.*.status,completed|nullable|numeric|min:0',
            'items.*.completion_notes' => 'nullable|string',
            'items.*.photos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Process each item
        foreach ($validated['items'] as $item) {
            $inspectionItem = $inspection->inspectionItems()->find($item['id']);
            
            if (!$inspectionItem || $inspectionItem->vendor_id !== $user->id) {
                abort(403);
            }

            // Handle photo uploads if any
            $photos = [];
            if ($request->hasFile("items.{$item['id']}.photos")) {
                foreach ($request->file("items.{$item['id']}.photos") as $photo) {
                    $path = $photo->store('inspection-photos', 'public');
                    $photos[] = $path;
                }
            }

            $inspectionItem->update([
                'status' => $item['status'],
                'actual_cost' => $item['actual_cost'],
                'completion_notes' => $item['completion_notes'],
                'photos' => $photos,
                'completed_at' => $item['status'] === 'completed' ? now() : null,
            ]);
        }

        // Check if all items are completed or cancelled
        $allCompleted = $inspection->inspectionItems()
            ->where('vendor_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->doesntExist();

        if ($allCompleted) {
            $inspection->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Service status updated successfully.');
    }
} 