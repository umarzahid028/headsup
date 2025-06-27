<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Token;
use Carbon\Carbon;

class SalesDashboardController extends Controller
{

public function activityReport(Request $request)
{
    $loggedInUser = Auth::user();
    $isManager = $loggedInUser->hasRole('Sales Manager|Sales person', );

    // If Sales Manager and a user_id is passed, allow viewing that user's report
    $targetUserId = $isManager && $request->has('user_id')
        ? $request->input('user_id')
        : $loggedInUser->id;

    // Prevent access to other users’ data if not a manager
    if (!$isManager && $targetUserId != $loggedInUser->id) {
        abort(403, 'Unauthorized access.');
    }

    // ✅ Filter queue records by current month & year
    $queueRecords = Queue::where('user_id', $targetUserId)
        ->whereNotNull('checked_in_at')
        ->whereNotNull('checked_out_at')
        ->whereMonth('checked_in_at', now()->month)
        ->whereYear('checked_in_at', now()->year)
        ->orderByDesc('created_at')
        ->get();

    $checkInCount = 0;
    $checkOutCount = 0;
    $totalDurationMinutes = 0;

    $report = $queueRecords->map(function ($record) use (&$checkInCount, &$checkOutCount, &$totalDurationMinutes) {
        $checkIn = $record->checked_in_at ? Carbon::parse($record->checked_in_at) : null;
        $checkOut = $record->checked_out_at ? Carbon::parse($record->checked_out_at) : null;
        $duration = null;

        if ($checkIn) $checkInCount++;
        if ($checkOut) $checkOutCount++;

        if ($checkIn && $checkOut) {
            $durationInMinutes = $checkIn->diffInMinutes($checkOut);
            $totalDurationMinutes += $durationInMinutes;
            $duration = $checkIn->diffForHumans($checkOut, true);
        }

        return [
            'checked_in_at' => $checkIn?->format('Y-m-d H:i:s'),
            'checked_out_at' => $checkOut?->format('Y-m-d H:i:s'),
            'duration' => $duration,
        ];
    });

    return view('activity-records.activity-records', compact(
        'report',
        'checkInCount',
        'checkOutCount',
        'totalDurationMinutes'
    ));
}

}


