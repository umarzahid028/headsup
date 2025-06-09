<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Queue;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    public function showTokensPage()
    {
        return view('tokens.tokens');
    }

    public function generateToken()
    {
        $checkedInUserIds = Queue::where('is_checked_in', true)->pluck('user_id')->toArray();

        $availableSalespersons = User::role('Sales person')
            ->whereIn('id', $checkedInUserIds)
            ->get();

        $busyUserIds = Token::where('status', 'assigned')->pluck('user_id')->toArray();

        $freeSalespersons = $availableSalespersons->filter(function ($user) use ($busyUserIds) {
            return !in_array($user->id, $busyUserIds);
        });

        $lastToken = Token::orderBy('serial_number', 'desc')->first();
        $nextSerial = $lastToken ? $lastToken->serial_number + 1 : 1;

        if ($freeSalespersons->isNotEmpty()) {
            $freeSalesperson = $freeSalespersons->first();

            $token = Token::create([
                'user_id' => $freeSalesperson->id,
                'serial_number' => $nextSerial,
                'status' => 'assigned',
                'assigned_at' => Carbon::now(),
            ]);
        } else {
            $token = Token::create([
                'user_id' => null,
                'serial_number' => $nextSerial,
                'status' => 'pending',
                'assigned_at' => null,
            ]);
        }

        return response()->json(['token' => $token]);
    }

public function activeTokens(Request $request)
{
    $checkedInUserIds = Queue::where('is_checked_in', true)->pluck('user_id')->toArray();

    $tokens = Token::where('status', 'assigned')
        ->whereIn('user_id', $checkedInUserIds)
        ->orderBy('serial_number')
        ->get();

    $pendingtokens = Token::where('status', 'pending')
        ->orderBy('serial_number')
        ->get();

    if ($request->wantsJson()) {
        return response()->json([
            'active' => $tokens->map(function ($token) {
                return [
                    'serial_number' => $token->serial_number,
                    'salesperson' => $token->salesperson->name ?? 'Unassigned',
                    'status' => $token->status,
                    'counter_number' => $token->salesperson->counter_number ?? 'N/A',
                    'assigned_at' => optional($token->assigned_at)->toDateTimeString(),
                ];
            }),
            'pending' => $pendingtokens->map(function ($token) {
                return [
                    'serial_number' => $token->serial_number,
                ];
            }),
        ]);
    }

    return view('public.active-tokens', [
        'tokens' => $tokens,
        'pendingtokens' => $pendingtokens,
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

    if ($user->hasRole('Sales person')) {
        $now = Carbon::now();
        $cutoff = $now->subHours(24); // 24 ghantay ka filter

        $tokens = Token::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'skipped'])
            ->where('updated_at', '>=', $cutoff) // 24-hour filter
            ->orderBy('serial_number')
            ->get();

        return view('tokens-history.tokens-history', compact('tokens'));
    }

    abort(403);
}
}
