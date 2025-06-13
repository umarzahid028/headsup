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

        $latestQueue = \App\Models\Queue::where('user_id', $user->id)->latest('created_at')->first();

        if ($latestQueue && $latestQueue->is_checked_in) {
            $latestQueue->update([
                'is_checked_in' => false,
                'checked_out_at' => now(),
            ]);

            // Token creation on checkout
            app(\App\Http\Controllers\TokenController::class)->createPendingTokenOnCheckout($user->id);

            $checkedIn = false;
            $message = 'You are now checked out.';
        } else {
            \App\Models\Queue::create([
                'user_id' => $user->id,
                'is_checked_in' => true,
                'checked_in_at' => now(),
            ]);

            // Token assign on check-in
            app(\App\Http\Controllers\TokenController::class)->assignPendingTokenToUser($user->id);

            $checkedIn = true;
            $message = 'You are now checked in.';
        }

        return response()->json([
            'checked_in' => $checkedIn,
            'message' => $message,
        ]);

    } catch (\Exception $e) {
        // Log the full error
        \Log::error('Dashboard store error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'error' => true,
            'message' => 'Server error: ' . $e->getMessage(), // Send actual error for debugging
        ], 500);
    }
}


}



