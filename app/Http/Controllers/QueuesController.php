<?php

namespace App\Http\Controllers;

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

            app(\App\Http\Controllers\TokenController::class)->returnAssignedTokenToPending($user->id);

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

            app(\App\Http\Controllers\TokenController::class)->assignPendingTokenToUser($user->id);

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



}
