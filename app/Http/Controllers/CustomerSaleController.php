<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Queue;
use App\Models\Appointment;
use App\Models\CustomerSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerSaleController extends Controller
{
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'id'             => 'nullable|integer|exists:customer_sales,id',
            'user_id'        => 'nullable|exists:users,id',
            'customer_id'    => 'nullable|exists:customers,id',
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'phone'          => 'nullable|string|max:20',
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
        'user_id'     => $validated['user_id'] ?? auth()->id(),
        'customer_id' => $validated['customer_id'] ?? null,
        'name'        => $validated['name'],
        'email'       => $validated['email'] ?? null,
        'phone'       => $validated['phone'] ?? null,
        'interest'    => $validated['interest'] ?? null,
        'notes'       => $validated['notes'] ?? null,
        'process'     => $validated['process'] ?? [],
        'disposition' => $validated['disposition'] ?? null,
    ];

    $sale = !empty($validated['id']) ? CustomerSale::find($validated['id']) : null;

    if ($sale) {
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    if (!empty($validated['disposition'])) {
        $sale->ended_at = now();
        $sale->save();
    }

    if (!empty($validated['appointment_id'])) {
        \App\Models\Appointment::where('id', $validated['appointment_id'])
            ->update(['status' => 'completed']);
    }

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
        'id'       => $sale->id,
        'redirect' => route('sales.perosn'),
    ]);
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
    $customers = CustomerSale::where('forwarded_to_manager', true)->latest()->get();
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

public function forwardToManager(Request $request)
{
    $customer = null;

    if ($request->filled('id')) {
        $customer = CustomerSale::find($request->id);
    }

    // If no direct customer but appointment is provided
    if (!$customer && $request->filled('appointment_id')) {
        $appointment = Appointment::find($request->appointment_id);

        if (!$appointment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Appointment not found.',
            ], 404);
        }

        // Create a new customer from appointment
        $customer = new CustomerSale([
            'name'           => $appointment->customer_name,
            'phone'          => $appointment->customer_phone,
            'user_id'        => auth()->id(),
            'appointment_id' => $appointment->id,
            'forwarded_to_manager' => true,
            'forwarded_at'         => now(),
        ]);
        $customer->save();

        // Mark appointment as completed
        $appointment->status = 'completed';
        $appointment->save();
    }

    // If customer still not found
    if (!$customer) {
        return response()->json([
            'status'  => 'error',
            'message' => 'No valid customer or appointment found.',
        ], 404);
    }

    // If existing customer, update forward flags if needed
    if (!$customer->forwarded_to_manager) {
        $customer->forwarded_to_manager = true;
        $customer->forwarded_at = now();
        $customer->save();
    }

    return response()->json([
        'status'   => 'forwarded',
        'message'  => 'Customer forwarded to Sales Manager!',
        'redirect' => route('sales.perosn', ['id' => $customer->id]),
    ]);
}

 public function fetch()
{
    $customers = CustomerSale::with('user')->get();
    return view('partials.customer-list', compact('customers'));
}

public function customerform(Request $request)
{
    try {
        $validated = $request->validate([
            'id'             => 'nullable|integer|exists:customer_sales,id',
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
        'email'       => $validated['email'] ?? null,
        'phone'       => $validated['phone'],
        'interest'    => $validated['interest'] ?? null,
        'notes'       => $validated['notes'] ?? null,
        'process'     => $validated['process'] ?? [],
        'disposition' => $validated['disposition'] ?? null,
    ];

    // ✅ Find or create
    $sale = !empty($validated['id']) ? CustomerSale::find($validated['id']) : null;

    if ($sale) {
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    // ✅ Set ended_at if disposition is given
    if (!empty($validated['disposition'])) {
        $sale->ended_at = now();
        $sale->save();
    }

    // ✅ Update appointment status if needed
    if (!empty($validated['appointment_id'])) {
        \App\Models\Appointment::where('id', $validated['appointment_id'])
            ->update(['status' => 'completed']);
    }

    // ✅ Calculate duration from queue
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
        'id'       => $sale->id, // send back id to frontend
        'redirect' => route('to.customers', ['id' => auth()->id()]),
    ]);
}
// app/Http/Controllers/SalesPersonController.php

}

  
