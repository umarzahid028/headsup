<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CustomerSale;
use Illuminate\Http\Request;

class CustomerSaleController extends Controller
{
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'id'           => 'nullable|exists:customer_sales,id',
            'user_id'      => 'nullable|exists:users,id',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:20',
            'interest'     => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'process'      => 'nullable|array',
            'disposition'  => 'nullable|string',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'errors' => $e->errors(),
        ], 422);
    }

    $sale = null;

    if (!empty($validated['id'])) {
        $sale = CustomerSale::find($validated['id']);
    } else {
        $sale = CustomerSale::where('phone', $validated['phone'])->first();
    }

    $data = [
        'user_id'     => $validated['user_id'] ?? null,
        'name'        => $validated['name'],
        'email'       => $validated['email'],
        'phone'       => $validated['phone'],
        'interest'    => $validated['interest'] ?? null,
        'notes'       => $validated['notes'] ?? null,
        'process'     => $validated['process'] ?? [],
        'disposition' => $validated['disposition'] ?? null,
    ];

    if ($sale) {
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    return response()->json([
        'status'   => 'success',
        'message'  => 'Customer sale data saved successfully!',
        'customer' => $sale,
    ]);
}


    public function index(Request $request)
    {
        $customers = CustomerSale::with('user')->latest()->get();

        if ($request->ajax() || $request->boolean('partial')) {
            return view('partials.customers', compact('customers'))->render();
        }

        return view('sales-person-dashboard.dashboard', compact('customers'));
    }

public function transfer(Request $request, $id)
{
    $request->validate([
        'new_user_id' => 'required|exists:users,id'
    ]);

    $customer = CustomerSale::findOrFail($id);
    $customer->user_id = $request->new_user_id;
    $customer->save();

    return response()->json([
        'message' => 'Customer transferred successfully.'
    ]);
}

public function stopTimer(Request $request, $id)
    {
        $start = $request->input('start_time');

        if (!$start) {
            return response()->json(['error' => 'Start time missing'], 400);
        }

        $startTime = Carbon::createFromTimestampMs($start);
        $now = Carbon::now();
        $duration = $now->diffInSeconds($startTime);

        $formatted = gmdate('H:i:s', $duration);

        $customer = CustomerSale::findOrFail($id);
        $customer->served_at = $now;
        $customer->served_duration = $formatted;
        $customer->save();

        return response()->json(['status' => 'success', 'duration' => $formatted]);
    }

}
