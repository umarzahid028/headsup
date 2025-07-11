<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Queue;
use App\Models\Appointment;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{

   public function queuelist(Request $request)
{
    $users = User::with(['latestQueue.appointment'])
        ->whereHas('latestQueue', function ($query) {
            $query->where('is_checked_in', true);
        })->get();

    $latestQueues = $users->map(function ($user) {
        return $user->latestQueue;
    })->filter()->values();

    $activeData = $latestQueues->map(function ($queue) {
        $salesPersonName = $queue->user->name ?? 'Unassigned';

        $customerSales = CustomerSale::where('user_id', $queue->user_id)
            ->whereNull('disposition')
            ->latest()
            ->get();

        $appointment = $queue->appointment;

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
                'id'            => $sale->id,
                'customer_name' => $customerName,
                'process'       => $lastProcess,
                'forwarded'     => (bool) $sale->forwarded_to_manager,
                'forwarded_at'  => $sale->forwarded_at,
            ];
        });

        // ✅ Appointment ko prepend karna sirf agar wo exist karta ho
        if ($appointment && $appointment->status !== 'completed') {
            $customers->prepend([
                'id'            => 'appointment_' . $appointment->id,
                'customer_name' => $appointment->customer_name ?? 'N/A',
                'process'       => ['Appointment'],
                'forwarded'     => false,
                'forwarded_at'  => null,
            ]);
        }

        return [
            'sales_person' => $salesPersonName,
            'customers'    => $customers,
        ];
    })->values();

    // ✅ Remove custom error — just return empty array
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





 public function addusers(Request $request)
{
    $search = $request->input('search');
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');

    $customerSales = CustomerSale::query()
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('disposition', 'like', "%{$search}%");
            });
        })
        ->when($fromDate, function ($query, $fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        })
        ->when($toDate, function ($query, $toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        })
        ->latest()
        ->paginate(10)
        ->appends([
            'search' => $search,
            'from_date' => $fromDate,
            'to_date' => $toDate
        ]);

    return view('tokens-history.tokens-history', compact('customerSales', 'search', 'fromDate', 'toDate'));
}

}
