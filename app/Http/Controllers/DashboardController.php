<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use App\Models\Vendor;
use App\Models\Activity;
use App\Models\Estimate;
use App\Models\Transport;
use Illuminate\View\View;
use App\Models\Inspection;
use App\Models\SalesIssue;
use Illuminate\Http\Request;
use App\Models\VendorEstimate;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionItemResult;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
   

    $user = auth()->user();

    if ($user->hasRole('Sales person')) {
        return redirect()->route('sales.perosn');
    }
        // Get current month and last month dates
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');
        $lastMonth = $now->copy()->subMonth()->format('Y-m');
        
        
        
        // Open issues metrics
        $openIssues = SalesIssue::where('status', 'open')->count();
        $resolvedIssues = SalesIssue::where('status', 'resolved')->count();
        
        // Monthly revenue
        $monthlyRevenue = Sale::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');
            
        $lastMonthRevenue = Sale::whereYear('created_at', $now->copy()->subMonth()->year)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->sum('amount');
        
        // Revenue chart - last 6 months
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $revenue = Sale::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            
            $revenueChart[$month->format('M Y')] = $revenue;
        }
        
        // Recent activities
        $recentActivities = Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Calculate repair metrics
        $repairsNeeded = InspectionItemResult::where('requires_repair', true)->count();
        $repairsCompleted = InspectionItemResult::where('requires_repair', true)
            ->where('repair_completed', true)
            ->count();
        
        $repairCompletionRate = $repairsNeeded > 0 
            ? round(($repairsCompleted / $repairsNeeded) * 100) 
            : 0;
            
        // Top performing sales staff if relevant
        $topSalesStaff = null;
        if (class_exists('App\Models\SalesTeam')) {
            $topSalesStaff = DB::table('sales')
                ->join('users', 'sales.user_id', '=', 'users.id')
                ->select('users.name', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(amount) as total_sales'))
                ->whereYear('sales.created_at', $now->year)
                ->whereMonth('sales.created_at', $now->month)
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_sales', 'desc')
                ->limit(3)
                ->get();
        }
        
        return view('dashboard', compact(
            'totalVehicles',
            'vehicleGrowth',
            'activeInspections',
            'completedInspections',
            'openIssues',
            'resolvedIssues',
            'monthlyRevenue',
            'lastMonthRevenue',
            'vehicleStatusChart',
            'revenueChart',
            'recentActivities',
            'repairCompletionRate',
            'topSalesStaff'
        ));
    }

    

//Sales perosn
public function salesdashboard()
{
    $user = Auth::user();

    $isCheckedIn = Queue::where('user_id', $user->id)
                        ->where('is_checked_in', true)
                        ->exists();

    $token = null;

 if ($isCheckedIn) {
    $token = Token::with('salesperson')
        ->where('user_id', $user->id)
        ->where('status', 'assigned')
        ->latest('created_at')
        ->first();
} else {
    $token = null;
}
    return view('sales-person-dashboard.dashboard', compact('token'));
}

}