<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;

class StatusController extends Controller
{

public function showStatus()
{
    $users = User::role('Sales person')->get()->map(function ($user) {
        $latestQueue = Queue::where('user_id', $user->id)
            ->latest('created_at')
            ->first();

        return [
            'name' => $user->name,
            'status' => $latestQueue?->is_checked_in ? 'Available' : 'Unavailable',
        ];
    });

    return view('screen.status', compact('users'));
}

}