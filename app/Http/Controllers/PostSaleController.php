<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\WeOweItem;
use App\Models\GoodwillRepair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostSaleController extends Controller
{
    /**
     * Display a list of archived vehicles.
     */
    public function index()
    {
        $archivedVehicles = Vehicle::where('is_archived', true)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
            
        return view('post-sale.index', compact('archivedVehicles'));
    }
    
    /**
     * Display a specific archived vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        // Load relationships
        $vehicle->load([
            'weOweItems' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'goodwillRepairs' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);
        
        $pendingWeOweItems = $vehicle->weOweItems->where('status', '!=', 'completed')->count();
        $pendingGoodwillRepairs = $vehicle->goodwillRepairs->where('status', '!=', 'completed')->count();
        
        return view('post-sale.show', compact(
            'vehicle', 
            'pendingWeOweItems', 
            'pendingGoodwillRepairs'
        ));
    }
    
    /**
     * Archive a vehicle.
     */
    public function archive(Vehicle $vehicle)
    {
        $vehicle->is_archived = true;
        $vehicle->save();
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle archived successfully.');
    }
    
    /**
     * Unarchive a vehicle.
     */
    public function unarchive(Vehicle $vehicle)
    {
        $vehicle->is_archived = false;
        $vehicle->save();
        
        return redirect()->route('post-sale.show', $vehicle)
            ->with('success', 'Vehicle unarchived successfully.');
    }
    
    /**
     * Display a list of all we-owe items.
     */
    public function weOweItems()
    {
        $pendingItems = WeOweItem::with('vehicle')
            ->pending()
            ->orderBy('due_date')
            ->get();
            
        $completedItems = WeOweItem::with('vehicle')
            ->completed()
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();
            
        return view('post-sale.we-owe-items', compact('pendingItems', 'completedItems'));
    }
    
    /**
     * Display a list of all goodwill repairs.
     */
    public function goodwillRepairs()
    {
        $pendingRepairs = GoodwillRepair::with('vehicle')
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('due_date')
            ->get();
            
        $completedRepairs = GoodwillRepair::with('vehicle')
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(10)
            ->get();
            
        return view('post-sale.goodwill-repairs', compact('pendingRepairs', 'completedRepairs'));
    }
    
    /**
     * Reopen a sold/archived vehicle for post-sale service.
     */
    public function reopenVehicle(Vehicle $vehicle)
    {
        // If the vehicle is sold but not archived, mark it for service
        if ($vehicle->is_sold && !$vehicle->is_archived) {
            $vehicle->current_stage = 'post_sale_service';
            $vehicle->stage_updated_at = now();
            $vehicle->save();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Vehicle reopened for post-sale service.');
        }
        
        // If the vehicle is archived, unarchive it and mark for service
        if ($vehicle->is_archived) {
            $vehicle->is_archived = false;
            $vehicle->current_stage = 'post_sale_service';
            $vehicle->stage_updated_at = now();
            $vehicle->save();
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Archived vehicle reopened for post-sale service.');
        }
        
        return redirect()->back()
            ->with('error', 'Cannot reopen vehicle. Vehicle must be sold or archived.');
    }
}
