<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueuesController extends Controller
{
public function dashboardstore(Request $request)
{
    try {
        $user = Auth::user();

        $latestQueue = Queue::where('user_id', $user->id)->latest('created_at')->first();

        if ($latestQueue && $latestQueue->is_checked_in) {
            // Check-out
            $latestQueue->update([
                'is_checked_in' => false,
                'checked_out_at' => now(),
            ]);


            $checkedIn = false;
            $message = 'You are now checked out.';
            $checkedInAt = null;
        } else {
            // Check-in
            $queue = Queue::create([
                'user_id' => $user->id,
                'is_checked_in' => true,
                'checked_in_at' => now(),
            ]);


            $checkedIn = true;
            $message = 'You are now checked in.';
            $checkedInAt = $queue->checked_in_at->toIso8601String();
        }

        return response()->json([
            'checked_in' => $checkedIn,
            'checked_in_at' => $checkedInAt,
            'message' => $message,
        ]);
    } catch (\Exception $e) {
        \Log::error('Dashboard store error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => true,
            'message' => 'Server error: ' . $e->getMessage(),
        ], 500);
    }
}
public function takeTurn(Request $request)
{
    $user = Auth::user();

    // Find current active queue
    $queue = Queue::where('user_id', $user->id)
        ->where('is_checked_in', true)
        ->whereNull('took_turn_at')
        ->orderBy('id')
        ->first();

    if (!$queue) {
        return response()->json(['message' => 'No active turn found.'], 404);
    }

    // 1. Mark current turn complete
    $queue->took_turn_at = now();
    $queue->save();

    // 2. Reinsert into queue (simulate continuous presence)
    Queue::create([
        'user_id' => $user->id,
        'is_checked_in' => true,
        'took_turn_at' => null,
        'checked_in_at' => now(),
    ]);

    return response()->json(['message' => 'Turn completed.']);
}

 public function checkInStatus(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['is_checked_in' => false], 401);
            }

            $latestQueue = Queue::where('user_id', $user->id)
                ->latest('created_at')
                ->first();

            $isCheckedIn = $latestQueue && $latestQueue->is_checked_in;

            return response()->json([
                'is_checked_in' => $isCheckedIn
            ]);
        } catch (\Exception $e) {
            \Log::error('Check-in status error: ' . $e->getMessage());

            return response()->json([
                'error' => true,
                'message' => 'Server error'
            ], 500);
        }
    }
// app/Http/Controllers/QueueController.php

public function nextTurnStatus()
{
    $user = Auth::user();

    // Next in queue (turn not taken yet)
    $nextInQueue = Queue::with('user')
        ->where('is_checked_in', true)
        ->whereNull('took_turn_at')
        ->orderBy('id')
        ->first();

    // Boolean: is it your turn?
    $isYourTurn = $nextInQueue && $nextInQueue->user_id === $user->id;

    // Count others in queue excluding you
    $othersPending = Queue::where('is_checked_in', true)
        ->whereNull('took_turn_at')
        ->where('user_id', '!=', $user->id)
        ->count();

    // Boolean: is anyone else checked in (excluding you)
    $anyoneElse = $othersPending > 0;

    // ✅ Get current user's check-in status from the Queue table
    $userQueue = Queue::where('user_id', $user->id)
        ->where('is_checked_in', true)
        ->whereNull('took_turn_at')
        ->first();

    return response()->json([
        'is_your_turn' => $isYourTurn,
        'others_pending' => $othersPending,
        'user_name' => $nextInQueue && $nextInQueue->user ? $nextInQueue->user->name : null,
        'current_turn_user_id' => $nextInQueue ? $nextInQueue->user_id : null,
        'any_one_else' => $anyoneElse,
        'is_checked_in' => $userQueue ? true : false, // ✅ Return true if current user is in queue and checked-in
    ]);
}


public function completeForm(Request $request)
{
    $user = Auth::user();

    $queue = Queue::where('user_id', $user->id)
        ->whereNotNull('took_turn_at')
        ->whereNull('form_completed_at')
        ->latest()
        ->first();

    if (!$queue) {
        return response()->json(['message' => 'No active form found to complete.'], 404);
    }

    $queue->form_completed_at = now();
    $queue->save();

    $duration = Carbon::parse($queue->took_turn_at)
        ->diff(Carbon::parse($queue->form_completed_at))
        ->format('%Hh %Im %Ss');

    return response()->json([
        'message' => 'Form completed successfully.',
        'duration' => $duration,
    ]);
}
}
