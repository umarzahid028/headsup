<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\WeOweItem;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Get summary data for dashboard
        $data = [
            'totalVehicles' => Vehicle::count(),
            'activeVehicles' => Vehicle::where('is_archived', false)->where('is_sold', false)->count(),
            'pendingWeOwe' => WeOweItem::pending()->count(),
            'completedWeOwe' => WeOweItem::completed()->count(),
        ];
        
        return view('reports.index', compact('data'));
    }
}
