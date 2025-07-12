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
    $isManager = $loggedInUser->hasRole('Sales Manager|Sales person|Admin');

    $targetUserId = $isManager && $request->has('user_id')
        ? $request->input('user_id')
        : $loggedInUser->id;

    if (!$isManager && $targetUserId != $loggedInUser->id) {
        abort(403, 'Unauthorized access.');
    }

    $from = $request->input('from');
    $to = $request->input('to');

    // ✅ Default: show all records (no date filter)
    $query = Queue::where('user_id', $targetUserId)
        ->whereNotNull('checked_in_at')
        ->whereNotNull('checked_out_at');

    if ($from && $to) {
        $fromCarbon = Carbon::parse($from);
        $toCarbon = Carbon::parse($to);

        if ($fromCarbon->diffInDays($toCarbon) > 366) {
            return back()->with('error', 'You can only select up to 1 year range.');
        }

        $query->whereBetween('checked_in_at', [$from, $to]);
    }

    $queueRecords = $query->orderByDesc('created_at')->get();

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
    $durationInSeconds = $checkIn->diffInSeconds($checkOut);
    $totalDurationMinutes += floor($durationInSeconds / 60);

    $hours = floor($durationInSeconds / 3600);
    $minutes = floor(($durationInSeconds % 3600) / 60);

    $duration = sprintf('%02dh %02dm', $hours, $minutes);
}


        return [
            'checked_in_at' => $checkIn?->format('Y-m-d H:i:s'),
            'checked_out_at' => $checkOut?->format('Y-m-d H:i:s'),
            'duration' => $duration,
        ];
    });
    // ✅ If filtered and nothing found
    if ($from && $to && $report->isEmpty()) {
        return view('activity-records.activity-records', [
            'report' => [],
            'checkInCount' => 0,
            'checkOutCount' => 0,
            'totalDurationMinutes' => 0,
            'from' => '',
            'to' => '',
            'infoMessage' => 'No records found in selected date range.',
        ]);
    }

    return view('activity-records.activity-records', [
        'report' => $report,
        'checkInCount' => $checkInCount,
        'checkOutCount' => $checkOutCount,
        'totalDurationMinutes' => $totalDurationMinutes,
        'from' => $from,
        'to' => $to,
        'infoMessage' => null,
    ]);
}





}


