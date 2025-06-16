<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use App\Models\Vendor;
use App\Models\Activity;
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
        return redirect()->route('sales.perosn');
    }
        // Get current month and last month dates
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');
        $lastMonth = $now->copy()->subMonth()->format('Y-m');
       
            
        // Top performing sales staff if relevant
       
        
        return view('dashboard', );
    }

    

//Sales perosn
public function salesdashboard()
{
    $user = Auth::user();
$customers = CustomerSale::with('user')->where('user_id', $user->id)->get();

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

    return view('sales-person-dashboard.dashboard', compact('token', 'onHoldToken', 'customers'));
}


}