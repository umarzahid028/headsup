<?php

namespace App\Http\Controllers;

use App\Models\ReconWorkflow;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontlineReadyController extends Controller
{
    /**
     * Display a listing of vehicles ready to be moved to frontline status.
     */
    public function index()
    {
        $workflows = ReconWorkflow::with('vehicle')
            ->where('status', 'in_progress')
            ->whereHas('inspectionItems', function ($query) {
                $query->where('category_id', function ($subquery) {
                    $subquery->select('id')
                        ->from('inspection_categories')
                        ->where('slug', 'final-checks');
                })
                ->where('is_completed', true);
            })
            ->get();
            
        return view('vehicles.frontline-ready', compact('workflows'));
    }
    
    /**
     * Mark a vehicle as frontline ready.
     */
    public function markAsFrontlineReady(Request $request, ReconWorkflow $workflow)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Check if all required inspections are complete
        $incompleteItems = $workflow->inspectionItems()
            ->where('is_completed', false)
            ->count();
            
        if ($incompleteItems > 0) {
            return back()->with('error', 'All inspection items must be completed before marking as frontline ready.');
        }
        
        // Mark the workflow as completed
        $workflow->markAsCompleted(Auth::id());
        
        // Add notes if provided
        if ($request->filled('notes')) {
            $workflow->update(['notes' => $request->notes]);
        }
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle has been marked as frontline ready.');
    }
    
    /**
     * Display a page to confirm moving a vehicle to frontline ready status.
     */
    public function confirm(ReconWorkflow $workflow)
    {
        $workflow->load('vehicle', 'inspectionItems.category');
        
        // Get counts of items by category
        $categorySummary = $workflow->inspectionItems()
            ->selectRaw('category_id, COUNT(*) as total, SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed')
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');
            
        return view('vehicles.confirm-frontline', compact('workflow', 'categorySummary'));
    }
}
