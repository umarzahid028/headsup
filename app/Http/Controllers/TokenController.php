<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function showTokensPage()
    {
        return view('tokens.tokens');
    }

public function generateToken(Request $request)
{
    // Step 1: Validate the input
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
    ]);

    // Step 2: Get all checked-in Sales persons
    $checkedInUserIds = Queue::where('is_checked_in', true)->pluck('user_id')->toArray();

    $availableSalespersons = User::role('Sales person')
        ->whereIn('id', $checkedInUserIds)
        ->get();

    if ($availableSalespersons->isEmpty()) {
        return response()->json(['message' => 'No available salespersons found'], 422);
    }

    // Step 3: Filter free salespersons
    $busyUserIds = Token::where('status', 'assigned')->pluck('user_id')->toArray();

    $freeSalespersons = $availableSalespersons->filter(function ($user) use ($busyUserIds) {
        return !in_array($user->id, $busyUserIds);
    });

    // Step 4: Get next serial number (optional if you want to keep it)
    $lastToken = Token::orderBy('serial_number', 'desc')->first();
    $nextSerial = $lastToken ? $lastToken->serial_number + 1 : 1;

    // Step 5: Create Token
    if ($freeSalespersons->isNotEmpty()) {
        $freeSalesperson = $freeSalespersons->first();

        $token = Token::create([
            'user_id' => $freeSalesperson->id,
            'serial_number' => $nextSerial, // optional
            'status' => 'assigned',
            'assigned_at' => Carbon::now(),
            'customer_name' => $validated['customer_name'],
        ]);
    } else {
        $token = Token::create([
            'user_id' => null,
            'serial_number' => $nextSerial, // optional
            'status' => 'pending',
            'assigned_at' => null,
            'customer_name' => $validated['customer_name'],
        ]);
    }

    return response()->json(['token' => $token]);
}


public function activeTokens(Request $request)
{
    // 🔹 Step 1: Get latest check-in per salesperson
    $latestQueues = Queue::with('user')
        ->where('is_checked_in', true)
        ->latest()
        ->get()
        ->unique('user_id'); // only latest check-in per salesperson

    // 🔹 Step 2: Prepare output
    $activeData = $latestQueues->map(function ($queue) {
        $salesPersonName = $queue->user->name ?? 'Unassigned';

        // Get latest CustomerSale for that salesperson
        $customerSale = CustomerSale::where('user_id', $queue->user_id)
            ->latest()
            ->first();

        // Get customer name (no model/relationship needed)
        $customerName = $customerSale->name ?? 'Unknown Customer';

        // Get last process from process field (assumed JSON or array)
        $lastProcess = [];

        if ($customerSale && !empty($customerSale->process)) {
            $processArray = is_array($customerSale->process)
                ? $customerSale->process
                : json_decode($customerSale->process, true);

            $last = end($processArray);
            if (!empty($last)) {
                $lastProcess = [$last];
            }
        }

        return [
            'sales_person'  => $salesPersonName,
            'customer_name' => $customerName,
            'process'       => $lastProcess,
        ];
    })->values(); // reset array indexes

    // 🔹 Step 3: Return JSON or view
    if ($request->wantsJson()) {
        return response()->json(['active' => $activeData]);
    }

    return view('screen.active-tokens', ['tokens' => $activeData]);
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

    if ($user->hasRole(['Admin', 'Sales Manager', 'Sales person'])) {
        $cutoff = Carbon::now()->subHours(24);

        $query = Token::whereIn('status', ['completed', 'skipped'])
            ->where('updated_at', '>=', $cutoff)
            ->orderBy('serial_number');

        if ($user->hasRole('Sales person')) {
            $query->where('user_id', $user->id);
        }

        $tokens = $query->get();
        return view('tokens-history.tokens-history', compact('tokens'));
    }

    abort(403);
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
