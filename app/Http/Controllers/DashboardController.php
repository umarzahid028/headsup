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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Sales person')) {
    return redirect()->route('sales.perosn', ['id' => $user->id]);
}
          $queues = User::role('Sales person')->count();

    $lastMonthCount = User::role('Sales person')
        ->whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])
        ->count();

    $queueGrowth = $lastMonthCount > 0 
        ? round((($queues - $lastMonthCount) / $lastMonthCount) * 100)
        : 0;
$customer = CustomerSale::get()->count();
$appointment = Appointment::get()->count();
$customerdetail = CustomerSale::where('disposition', 'Sold!')->count();





    return view('dashboard', compact('queues', 'queueGrowth', 'customer', 'appointment', 'customerdetail'));
    }



    //Sales perosn
    public function salesdashboard($id)
    {
        
        $user = Auth::user();
$customers = CustomerSale::where('user_id', auth()->id())
    ->where('forwarded_to_manager', false)
    ->get();

        $salespeople = \App\Models\User::role('Sales person')
            ->where('id', '!=', $user->id) // optional: exclude current user
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

            $appointment = Appointment::findOrFail($id);
       
        return view('sales-person-dashboard.dashboard', compact('token', 'onHoldToken', 'customers', 'salespeople', 'appointment'));
    }
}
