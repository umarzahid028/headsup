<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStage;
use App\Models\Task;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View
    {
        // Date range handling
        $startDate = $request->input('start_date', Carbon::now()->subMonth()->startOfDay());
        $endDate = $request->input('end_date', Carbon::now()->endOfDay());
        
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        // Get statistics
        $stats = [
            'total_active_vehicles' => Vehicle::where('is_archived', false)
                                       ->where('is_sold', false)
                                       ->count(),
            'frontline_ready_vehicles' => Vehicle::where('is_frontline_ready', true)
                                         ->where('is_archived', false)
                                         ->where('is_sold', false)
                                         ->count(),
            'open_tasks' => Task::whereNotIn('status', ['completed'])
                           ->count(),
            'overdue_tasks' => Task::whereNotIn('status', ['completed'])
                             ->whereNotNull('due_date')
                             ->where('due_date', '<', Carbon::now()->startOfDay())
                             ->count(),
        ];
        
        // Get all stages for the pipeline view
        $stages = WorkflowStage::orderBy('order')->get();
        
        // Count vehicles in each stage
        $stageCounts = [];
        foreach($stages as $stage) {
            $stageCounts[$stage->slug] = Vehicle::where('current_stage', $stage->slug)
                                       ->where('is_archived', false)
                                       ->where('is_sold', false)
                                       ->count();
        }
        
        // Get recent tasks
        $recentTasks = Task::with(['vehicle', 'assignedUser'])
                      ->orderBy('created_at', 'desc')
                      ->limit(5)
                      ->get();
        
        // Get recent vehicles
        $recentVehicles = Vehicle::orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get();
        
        // Prepare chart data
        $chartData = $this->prepareChartData($startDate, $endDate);
        
        return view('dashboard', compact(
            'stats', 
            'stages', 
            'stageCounts',
            'recentTasks',
            'recentVehicles',
            'chartData',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Prepare the chart data for revenue and orders
     */
    private function prepareChartData(Carbon $startDate, Carbon $endDate): array
    {
        // Generate months for labels
        $labels = [];
        $revenueData = [];
        $ordersData = [];
        
        // For demo purposes, we'll generate some sample data
        // In a real application, this would come from your database
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $labels[] = $currentDate->format('M');
            
            // Sample data - in a real app, you'd query your database for this period
            $revenueData[] = rand(1000, 10000) / 100;
            $ordersData[] = rand(1, 10);
            
            $currentDate->addMonth();
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'backgroundColor' => 'rgba(0, 0, 0, 0.1)',
                    'borderColor' => 'rgb(0, 0, 0)',
                    'pointBackgroundColor' => 'white',
                    'pointBorderColor' => 'black',
                    'pointRadius' => 4,
                    'data' => $revenueData,
                    'tension' => 0.2,
                ],
                [
                    'label' => 'Orders',
                    'backgroundColor' => 'rgba(200, 200, 200, 0.2)', 
                    'borderColor' => 'rgb(200, 200, 200)',
                    'pointBackgroundColor' => 'white',
                    'pointBorderColor' => 'rgb(200, 200, 200)',
                    'pointRadius' => 4,
                    'data' => $ordersData,
                    'tension' => 0.2,
                ]
            ]
        ];
    }
} 