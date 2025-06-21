<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Queue;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSaleController extends Controller
{
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'id'             => 'nullable|exists:customer_sales,id',
            'user_id'        => 'required|exists:users,id',
            'customer_id'    => 'nullable|exists:customers,id',
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'required|string|max:20',
            'interest'       => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'process'        => 'nullable|array',
            'disposition'    => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'errors' => $e->errors(),
        ], 422);
    }

    $data = [
        'user_id'     => $validated['user_id'],
        'customer_id' => $validated['customer_id'] ?? null,
        'name'        => $validated['name'],
        'email'       => $validated['email'],
        'phone'       => $validated['phone'],
        'interest'    => $validated['interest'] ?? null,
        'notes'       => $validated['notes'] ?? null,
        'process'     => $validated['process'] ?? [],
        'disposition' => $validated['disposition'] ?? null,
    ];

    // Check if sale exists
    if (!empty($validated['id'])) {
        $sale = CustomerSale::find($validated['id']);
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    // Save ended_at time if disposition is present
    if (!empty($validated['disposition'])) {
        $sale->ended_at = now();
        $sale->save();
    }

    // Get turn start time from Queue
    $queue = \App\Models\Queue::where('user_id', $data['user_id'])
                ->where('customer_id', $data['customer_id'])
                ->whereNotNull('took_turn_at')
                ->latest('took_turn_at')
                ->first();

    $duration = null;

    if ($queue && $sale->ended_at) {
        $start = \Carbon\Carbon::parse($queue->took_turn_at);
        $end = \Carbon\Carbon::parse($sale->ended_at);
        $duration = $start->diff($end)->format('%Hh %Im %Ss');
    }

    return response()->json([
        'status'   => 'success',
        'message'  => 'Customer sale data saved successfully!',
        'duration' => $duration,
        'redirect' => route('sales.perosn', ['id' => auth()->id()]),
    ]);
}


public function transferToManager(Request $request)
{
    $customer = CustomerSale::findOrFail($request->id);

    $customer->disposition = 'T/O'; 
    $customer->save();

    return response()->json(['status' => 'success']);
}


  public function index(Request $request)
{
    $customers = CustomerSale::with('user')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

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



    public function addcustomer()
    {
        return view('tokens-history/addcustomer');
    }

public function customer()
{
    $customers = CustomerSale::where('disposition', 'T/O')->latest()->get();
    return view('t/o-customers.customer', compact('customers'));
}

public function completeForm(Request $request, $id)
{
    $user = Auth::user();

    // 1. Find or create sale record
    $sale = CustomerSale::firstOrNew([
        'user_id' => $user->id,
        'customer_id' => $id
    ]);

    // 2. Get latest took_turn_at based on user_id + customer_id
    $queue = Queue::where('user_id', $user->id)
                  ->where('customer_id', $id)
                  ->whereNotNull('took_turn_at')
                  ->latest('took_turn_at')
                  ->first();

    if (!$queue) {
        return response()->json(['message' => 'No queue turn found.'], 404);
    }

    // 3. Save form fields
    $sale->notes = $request->input('notes');
    $sale->disposition = $request->input('disposition');
    $sale->ended_at = now();
    $sale->save();

    // 4. Calculate duration
    $start = \Carbon\Carbon::parse($queue->took_turn_at);
    $end = \Carbon\Carbon::parse($sale->ended_at);
    $duration = $start->diff($end)->format('%Hh %Im %Ss');

    return response()->json([
        'message' => 'Form saved successfully.',
        'duration' => $duration
    ]);
}

}

    
