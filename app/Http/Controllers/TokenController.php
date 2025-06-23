<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Queue;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
   

// app/Http/Controllers/YourController.php

public function queuelist(Request $request)
{
    $latestQueues = Queue::with('user')
        ->where('is_checked_in', true)
        ->latest()
        ->get()
        ->unique('user_id');

    $activeData = $latestQueues->map(function ($queue) {
        $salesPersonName = $queue->user->name ?? 'Unassigned';

       $customerSales = CustomerSale::where('user_id', $queue->user_id)
    ->latest()
    ->get();


        $customers = $customerSales->map(function ($sale) {
            $customerName = $sale->name ?? 'Unknown Customer';

            $lastProcess = [];
            if (!empty($sale->process)) {
                $processArray = is_array($sale->process)
                    ? $sale->process
                    : json_decode($sale->process, true);

                if (is_array($processArray)) {
                    $last = end($processArray);
                    if (!empty($last)) {
                        $lastProcess = [$last];
                    }
                }
            }

            return [
                'customer_name' => $customerName,
                'process'       => $lastProcess,
                'forwarded'     => (bool) $sale->forwarded_to_manager,
                'forwarded_at'  => $sale->forwarded_at,
            ];
        });

        return [
            'sales_person' => $salesPersonName,
            'customers'    => $customers,
        ];
    })->values();

    if ($request->wantsJson()) {
        return response()->json(['active' => $activeData]);
    }

    return view('screen.active-tokens', ['tokens' => $activeData]);
}


//Check in
// Controller Method (already discussed)
public function checkinSalespersons(Request $request)
{
    // Get only those users who are currently checked-in
    $latestCheckinPerUser = \App\Models\Queue::select(DB::raw('MIN(id) as id'))
        ->where('is_checked_in', true)
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('queues as q2')
                ->whereColumn('q2.user_id', 'queues.user_id')
                ->where('q2.id', '>', DB::raw('queues.id')) 
                ->where('q2.is_checked_in', false); 
        })
        ->groupBy('user_id');

    $checkins = \App\Models\Queue::whereIn('id', $latestCheckinPerUser)
        ->with('user:id,name')
        ->get()
        ->map(function ($queue) {
            return [
                'name' => $queue->user->name ?? 'Unnamed',
                'time' => optional($queue->created_at)->toIso8601String(),
            ];
        });

    if ($request->wantsJson()) {
        return response()->json($checkins);
    }

    return view('screen.checkins', ['checkins' => $checkins]);
}

    

  

public function addusers()
{
    $user = Auth::user();

    $customerSales = CustomerSale::where('user_id', $user->id)
        
        ->latest()
        ->get();

    return view('tokens-history.tokens-history', compact('customerSales'));
}




}
