<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use App\Models\Vendor;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\CustomerSale;
use App\Models\Estimate;
use App\Models\Transport;
use Illuminate\View\View;
use App\Models\Inspection;
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

   
public function index(): mixed
{
    $user = auth()->user();

    if ($user && $user->hasRole('Sales person')) {
        return redirect()->route('sales.perosn');
    }

    $queues = User::role('Sales person')->count();
    $lastMonthCount = User::role('Sales person')
        ->whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->count();
    $queueGrowth = $lastMonthCount > 0
        ? round((($queues - $lastMonthCount) / $lastMonthCount) * 100)
        : 0;

    $customer = CustomerSale::count();
    $appointment = Appointment::count();
    $customerdetail = CustomerSale::where('disposition', 'Sold!')->count();

    $unsold = $customer - $customerdetail;
    $soldChart = [
        'Sold' => $customerdetail,
        'Unsold' => $unsold < 0 ? 0 : $unsold,
    ];

    $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('M'))->all();

    $customerChart = collect(range(5, 0))->map(fn($i) =>
        CustomerSale::whereMonth('created_at', now()->subMonths($i)->month)->count()
    )->values()->all();

    $appointmentChart = collect(range(5, 0))->map(fn($i) =>
        Appointment::whereMonth('created_at', now()->subMonths($i)->month)->count()
    )->values()->all();

    return view('dashboard', compact(
        'queues', 'queueGrowth', 'customer', 'appointment', 'customerdetail',
        'customerChart', 'appointmentChart', 'soldChart', 'months'
    ));
}


    //Sales perosn
   public function salesdashboard($id = null)
{
    $user = Auth::user();

    $customers = CustomerSale::where('user_id', auth()->id())
        ->where('forwarded_to_manager', false)
        ->get();

    $salespeople = \App\Models\User::role('Sales person')
        ->where('id', '!=', $user->id)
        ->get();

    $isCheckedIn = Queue::where('user_id', $user->id)
        ->where('is_checked_in', true)
        ->exists();

    $token = null;
    $onHoldToken = null;

    if ($isCheckedIn) {
        $token = Token::with('salesperson')
            ->where('user_id', $user->id)
            ->where('status', 'assigned')
            ->latest('created_at')
            ->first();

        $onHoldToken = Token::with('salesperson')
            ->where('user_id', $user->id)
            ->where('status', 'on_hold')
            ->latest('created_at')
            ->first();
    }

    $appointment = null;
    if ($id !== null) {
        $appointment = Appointment::find($id);
        if (!$appointment) {
            abort(404, 'Appointment not found');
        }
    }

    return view('sales-person-dashboard.dashboard', compact('token', 'onHoldToken', 'customers', 'salespeople', 'appointment'));
}

}
