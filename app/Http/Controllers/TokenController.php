<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokenController extends Controller
{
    public function showTokensPage()
    {
        return view('tokens.tokens');
    }

// app/Http/Controllers/YourController.php

public function activeTokens(Request $request)
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
                ->where('q2.id', '>', DB::raw('queues.id')) // newer record exists
                ->where('q2.is_checked_in', false); // which is a checkout
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

//Current Token
 public function currentAssignedToken(Request $request)
    {
        $userId = Auth::id();
 $customers = CustomerSale::with('user')
        ->where('user_id', $user->id)
        ->get();
        // Check if user is checked in
        $isCheckedIn = \App\Models\Queue::where('user_id', $userId)
                        ->where('is_checked_in', true)
                        ->exists();

        if (!$isCheckedIn) {
            return response()->json(['token' => null]);
        }

        // Get latest assigned token for this user
        $token = Token::with('salesperson')
            ->where('user_id', $userId)
            ->where('status', 'assigned')
            ->latest('created_at')
            ->first();

        if (!$token) {
            return response()->json(['token' => null]);
        }

      return response()->json([
    'token' => [
        'serial_number' => $token->serial_number,
        'customer_name' => $token->customer_name,
        'counter_number' => $token->salesperson->counter_number ?? 'N/A',
        'status' => $token->status,
        'customer' => $customers,
    ]
]);

    }
    
    public function checkIn(Request $request)
    {
        $userId = auth()->id();

        \Log::info("Check-in called for user: $userId");

        Queue::updateOrCreate(
            ['user_id' => $userId],
            ['is_checked_in' => true, 'checked_in_at' => now()]
        );

        \Log::info("Queue updated for user: $userId");

        $this->assignPendingTokenToUser($userId);

        return response()->json(['message' => 'Check-in successful']);
    }

    public function assignPendingTokenToUser($userId)
    {
        \Log::info("assignPendingTokenToUser called for user: $userId");

        $hasAssignedToken = Token::where('user_id', $userId)
            ->where('status', 'assigned')
            ->exists();

        if ($hasAssignedToken) {
            \Log::info("User $userId already has an assigned token.");
            return;
        }

        $pendingToken = Token::where('status', 'pending')
            ->orderBy('serial_number', 'asc')
            ->first();

        if (!$pendingToken) {
            \Log::info("No pending token found to assign for user $userId");
            return;
        }

        \Log::info("Assigning token {$pendingToken->serial_number} to user $userId");

        $pendingToken->update([
            'user_id' => $userId,
            'status' => 'assigned',
            'assigned_at' => Carbon::now(),
        ]);
    }

    //skip tokken
    // TokenController.php
public function skip(Request $request, $tokenId)
{
    $token = Token::findOrFail($tokenId);

    // Ensure only assigned tokens can be skipped
    if ($token->status !== 'assigned') {
        return response()->json(['message' => 'Only assigned tokens can be skipped'], 422);
    }

    // Mark token as skipped
    $token->update([
        'status' => 'skipped',
        'skipped_at' => \Carbon\Carbon::now(),
    ]);

    $salespersonId = $token->user_id;

    // Assign next pending token to the same salesperson
    $pendingToken = Token::where('status', 'pending')->orderBy('serial_number')->first();

    if ($pendingToken) {
        $pendingToken->update([
            'user_id' => $salespersonId,
            'status' => 'assigned',
            'assigned_at' => \Carbon\Carbon::now(),
        ]);

        return response()->json([
            'message' => "Token skipped. Token {$pendingToken->serial_number} assigned to salesperson.",
            'status' => 'success'
        ]);
    }

    return response()->json([
        'message' => 'Token skipped. No pending tokens available for assignment.',
        'status' => 'success'
    ]);
}

    public function createPendingTokenOnCheckout($userId)
    {
        \Log::info("createPendingTokenOnCheckout called for user: $userId");

        // Check if user already has a pending token
        $existingPending = Token::where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            \Log::info("User $userId already has a pending token.");
            return;
        }

        $lastToken = Token::orderBy('serial_number', 'desc')->first();
        $nextSerial = $lastToken ? $lastToken->serial_number + 1 : 1;

        Token::create([
            'user_id' => $userId,
            'serial_number' => $nextSerial,
            'status' => 'pending',
            'assigned_at' => null,
        ]);

        \Log::info("Pending token created for user $userId");
    }

    public function returnAssignedTokenToPending($userId): bool
{
    $assigned = \App\Models\Token::where('user_id', $userId)
                                 ->where('status', 'assigned')
                                 ->first();

    if (!$assigned) {
        \Log::info("No assigned token for user $userId");
        return false;
    }

    $assigned->update([
        'user_id'     => null,
        'status'      => 'pending',
        'assigned_at' => null,
    ]);

    \Log::info("Token {$assigned->serial_number} returned to pending from user $userId");
    return true;
}

    public function completeToken(Request $request, $tokenId)
    {
        $token = Token::findOrFail($tokenId);

        if ($token->status !== 'assigned') {
            return response()->json(['message' => 'Token is not active'], 422);
        }

        $token->update([
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        $salespersonId = $token->user_id;

        $pendingToken = Token::where('status', 'pending')->orderBy('serial_number')->first();

        if ($pendingToken) {
            $pendingToken->update([
                'user_id' => $salespersonId,
                'status' => 'assigned',
                'assigned_at' => Carbon::now(),
            ]);
        }

        return response()->json([
            'message' => 'Token completed and next assigned if available',
            'status' => 'success'
        ]);
    }

public function tokenhistory()
{
    $user = Auth::user();

    $customerSales = CustomerSale::where('user_id', $user->id)
        
        ->latest()
        ->get();

    return view('tokens-history.tokens-history', compact('customerSales'));
}

public function assignNextToken(Request $request, Token $token)
{
    $salespersonId = $token->user_id;

    if (!$salespersonId) {
        return response()->json(['status' => 'error', 'message' => 'Invalid token or user not assigned.']);
    }

    $pendingToken = Token::where('status', 'pending')->orderBy('serial_number')->first();

    if ($pendingToken) {
        $pendingToken->update([
            'user_id' => $salespersonId,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'token' => [
                'serial_number' => $pendingToken->serial_number,
                'customer_name' => $pendingToken->customer_name,
                'counter_number' => $token->salesperson->counter_number ?? 'N/A'
            ]
        ]);
    }

    return response()->json(['status' => 'error', 'message' => 'No pending tokens available.']);
}


// public function hold($id)
// {
//     $token = Token::findOrFail($id);

//     if ($token->status !== 'assigned') {
//         return redirect()->back()->with('error', 'Only assigned tokens can be held.');
//     }

//     $token->status = 'on_hold';
//     $token->save();

//     return redirect()->back()->with('success', 'Token has been put on hold for test drive.');
// }

// public function resume($id)
// {
//     $token = Token::findOrFail($id);

//     if ($token->status !== 'on_hold') {
//         return redirect()->back()->with('error', 'Only on-hold tokens can be resumed.');
//     }

//     $token->status = 'assigned';
//     $token->save();

//     return redirect()->back()->with('success', 'Token resumed successfully.');
// }

}
