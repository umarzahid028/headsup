<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QueuesController extends Controller
{

    public function dashboardstore(Request $request)
    {
        $user = Auth::user();

        // Latest queue record nikal lo for this user
        $latestQueue = \App\Models\Queue::where('user_id', $user->id)->latest('created_at')->first();

        if ($latestQueue && $latestQueue->is_checked_in) {
            // Agar user already checked in hai to ab check out karo
            $latestQueue->update([
                'is_checked_in' => false,
                'checked_out_at' => now(),
            ]);
            session()->flash('message', 'You are now checked out.');

            // TokenController se pending token create karvao on checkout
            app(\App\Http\Controllers\TokenController::class)->createPendingTokenOnCheckout($user->id);

        } else {
            // Agar user check out hai to naya record bana ke check in karo
            \App\Models\Queue::create([
                'user_id' => $user->id,
                'is_checked_in' => true,
                'checked_in_at' => now(),
            ]);
            session()->flash('message', 'You are now checked in.');

            // TokenController se pending token assign karvao on checkin
            app(\App\Http\Controllers\TokenController::class)->assignPendingTokenToUser($user->id);
        }

        return redirect()->back();
    }
}



