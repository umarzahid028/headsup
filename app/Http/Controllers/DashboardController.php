<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\VendorEstimate;
use App\Models\Sale;
use Carbon\Carbon;
use App\Models\Estimate;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index(): View
    {
        
        if (auth()->user()->hasAnyRole(['Sales Manager', 'Recon Manager'])) {
            return $this->managerDashboard();
        } elseif (auth()->user()->hasRole('Transporter')) {
            return $this->transporterDashboard();
        } elseif (auth()->user()->hasRole('Vendor')) {
            return $this->vendorDashboard();
        } elseif (auth()->user()->hasRole('Sales Team')) {
            return $this->salesTeamDashboard();
        }

        return view('dashboard');
    }

    /**
     * Dashboard for Sales Manager and Recon Manager.
     */
    protected function managerDashboard(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get sales data
        $salesData = Sale::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->get();

        // Get vehicle data
        $vehicleStats = [
            'total' => Vehicle::count(),
            'in_stock' => Vehicle::where('status', 'in_stock')->count(),
            'in_recon' => Vehicle::where('status', 'in_recon')->count(),
            'sold' => Vehicle::where('status', 'sold')->count(),
        ];

        // Get transport data
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count(),
        ];

        // Get vendor estimates data
        $estimatesData = VendorEstimate::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(estimated_cost) as total')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->get();

        return view('dashboards.manager', compact(
            'salesData',
            'vehicleStats',
            'transportStats',
            'estimatesData'
        ));
    }

    /**
     * Dashboard for Transporters.
     */
    protected function transporterDashboard(): View
    {
        $transporter_id = auth()->user()->transporter_id;
        $currentYear = now()->year;

        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'pending')
                ->count(),
            'in_transit' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'in_transit')
                ->count(),
            'delivered' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'delivered')
                ->count(),
        ];

        // Check if any data exists for the year and this transporter
        $hasData = Transport::where('transporter_id', $transporter_id)
            ->whereYear('created_at', $currentYear)
            ->exists();
            
        if ($hasData) {
            // Get monthly transport data directly 
            $monthlyData = Transport::where('transporter_id', $transporter_id)
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            // Initialize array with zeros for all months (0-11 for Jan-Dec)
            $monthlyTransports = array_fill(0, 12, 0);
            
            // Fill in the actual counts
            foreach ($monthlyData as $data) {
                $monthlyTransports[$data->month - 1] = (int)$data->count;
            }
        } else {
            // Provide sample data for demonstration if no real data exists
            $monthlyTransports = [2, 4, 1, 5, 3, 2, 6, 4, 3, 2, 1, 2];
        }
        
        // Get recent transport activities
        $recentActivities = Transport::where('transporter_id', $transporter_id)
            ->with(['vehicle', 'transporter'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.transporter', compact('transportStats', 'monthlyTransports', 'recentActivities'));
    }

    /**
     * Dashboard for Vendors.
     */
    protected function vendorDashboard(): View
    {
        $vendor_id = auth()->user()->vendor_id;
        $thisMonth = Carbon::now()->startOfMonth();

        // Get estimates statistics
        $estimateStats = VendorEstimate::where('vendor_id', $vendor_id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Get monthly estimates data
        $estimatesData = VendorEstimate::where('vendor_id', $vendor_id)
            ->where('created_at', '>=', $thisMonth)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(estimated_cost) as total')
            ->groupBy('date')
            ->get();

        return view('dashboards.vendor', compact('estimateStats', 'estimatesData'));
    }

    /**
     * Dashboard for Sales Team.
     */
    protected function salesTeamDashboard(): View
    {
        $user_id = auth()->id();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get sales statistics
        $salesStats = Sale::where('user_id', $user_id)
            ->where('created_at', '>=', $thisMonth)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        // Get performance metrics
        $performance = [
            'total_sales' => Sale::where('user_id', $user_id)->count(),
            'monthly_sales' => Sale::where('user_id', $user_id)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'total_amount' => Sale::where('user_id', $user_id)
                ->sum('amount'),
            'monthly_amount' => Sale::where('user_id', $user_id)
                ->where('created_at', '>=', $thisMonth)
                ->sum('amount'),
        ];

        return view('dashboards.sales', compact('salesStats', 'performance'));
    }

    public function vendor()
    {
        // Get estimates statistics
        $estimateStats = [
            'pending' => Estimate::where('status', 'pending')->count(),
            'approved' => Estimate::where('status', 'approved')->count(),
            'rejected' => Estimate::where('status', 'rejected')->count(),
        ];

        // Get monthly estimates data for the last 12 months
        $estimatesData = Estimate::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(estimated_cost) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('dashboards.vendor', compact('estimateStats', 'estimatesData'));
    }

    public function transporter()
    {
        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count()
        ];

        // Get recent transport activities
        $recentActivities = Transport::with('vehicle')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transport) {
                return (object)[
                    'id' => $transport->id,
                    'status' => $transport->status,
                    'vehicle' => (object)[
                        'make' => $transport->vehicle->make,
                        'model' => $transport->vehicle->model,
                        'stock_number' => $transport->vehicle->stock_number
                    ],
                    'origin' => $transport->origin,
                    'destination' => $transport->destination,
                    'updated_at' => $transport->updated_at,
                    'pickup_date' => $transport->pickup_date,
                    'delivery_date' => $transport->delivery_date
                ];
            });

        return view('dashboards.transporter', compact(
            'transportStats',
            'recentActivities'
        ));
    }

    public function manager()
    {
        // Get vehicle statistics
        $vehicleStats = [
            'total' => Vehicle::count(),
            'in_stock' => Vehicle::where('status', 'in_stock')->count(),
            'in_recon' => Vehicle::where('status', 'in_recon')->count(),
            'sold' => Vehicle::where('status', 'sold')->count()
        ];

        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count()
        ];

        // Get monthly sales data for the last 6 months
        $salesData = Sale::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        // Get monthly estimates data for the last 6 months
        $estimatesData = Estimate::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        return view('dashboards.manager', compact(
            'vehicleStats',
            'transportStats',
            'salesData',
            'estimatesData'
        ));
    }
} 