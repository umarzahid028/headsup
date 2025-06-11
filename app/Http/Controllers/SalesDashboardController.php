<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Token;
use Carbon\Carbon;
class SalesDashboardController extends Controller
{

public function activityReport()
{
    $user = Auth::user();

    $queueRecords = Queue::where('user_id', $user->id)
        ->orderByDesc('created_at')
        ->get();

    // Counters
    $checkInCount = 0;
    $checkOutCount = 0;
    $totalDurationMinutes = 0;

    // Process each record
    $report = $queueRecords->map(function ($record) use (&$checkInCount, &$checkOutCount, &$totalDurationMinutes) {
        $checkIn = $record->checked_in_at ? Carbon::parse($record->checked_in_at) : null;
        $checkOut = $record->checked_out_at ? Carbon::parse($record->checked_out_at) : null;
        $duration = null;

        if ($checkIn) $checkInCount++;
        if ($checkOut) $checkOutCount++;

        if ($checkIn && $checkOut) {
            $durationInMinutes = $checkIn->diffInMinutes($checkOut);
            $totalDurationMinutes += $durationInMinutes;

            // Format as "2 hours 15 minutes"
            $duration = $checkIn->diffForHumans($checkOut, true);
        }

        return [
            'checked_in_at' => $checkIn ? $checkIn->format('Y-m-d H:i:s') : null,
            'checked_out_at' => $checkOut ? $checkOut->format('Y-m-d H:i:s') : null,
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
