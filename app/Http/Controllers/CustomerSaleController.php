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
            'id'             => 'nullable|exists:customer_sales,id',
            'user_id'        => 'nullable|exists:users,id',
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'required|string|max:20',
            'interest'       => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'process'        => 'nullable|array',
            'disposition'    => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id', // 👈 add this
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status' => 'error',
            'errors' => $e->errors(),
        ], 422);
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

    // ✅ Try to find existing sale
    if (!empty($validated['id'])) {
        $sale = CustomerSale::find($validated['id']);
    } else {
        $sale = CustomerSale::where('user_id', $data['user_id'])
            ->where(function ($q) use ($data) {
                $q->where('email', $data['email'])
                  ->orWhere('phone', $data['phone']);
            })
            ->latest()
            ->first();
    }

    // ✅ Create or Update
    if ($sale) {
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    // ✅ Appointment status update
    if (!empty($validated['appointment_id'])) {
        \App\Models\Appointment::where('id', $validated['appointment_id'])
            ->update(['status' => 'completed']);  // make sure this matches your DB ENUM
    }

    return response()->json([
        'status'   => 'success',
        'message'  => 'Customer sale data saved successfully!',
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

public function completeForm($id)
{
    $user = Auth::user();

    // 1. Get latest took_turn_at from Queue
    $queue = Queue::where('user_id', $user->id)
                  ->whereNotNull('took_turn_at')
                  ->latest('took_turn_at')
                  ->first();

    if (!$queue) {
        return response()->json(['message' => 'No turn found.'], 404);
    }

    // 2. Get the latest customer_sales record of this user (based on customer ID)
    $sale = CustomerSale::where('user_id', $user->id)
                        ->where('customer_id', $id)
                        ->latest()
                        ->first();

    if (!$sale) {
        return response()->json(['message' => 'No customer sale found.'], 404);
    }

    // 3. Save ended_at = now()
    $sale->ended_at = now();
    $sale->save();

    return response()->json(['message' => 'Form completed & time saved.']);
}

    }
