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
            'name'           => 'nullable|string|max:255',
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
        'user_id'       => $validated['user_id'] ?? auth()->id(),
        'customer_id'   => $validated['customer_id'] ?? null,
        'name'          => $validated['name'],
        'email'         => $validated['email'] ?? null,
        'phone'         => $validated['phone'] ?? null,
        'interest'      => $validated['interest'] ?? null,
        'notes'         => $validated['notes'] ?? null,
        'process'       => $validated['process'] ?? [],
        'disposition'   => $validated['disposition'] ?? null,
        'appointment_id'=> $validated['appointment_id'] ?? null, 
    ];

    $sale = null;

    if (!empty($validated['id'])) {
        $sale = CustomerSale::find($validated['id']);
    }

    if (!$sale && !empty($validated['customer_id'])) {
        $existing = CustomerSale::where('user_id', $data['user_id'])
            ->where('customer_id', $validated['customer_id'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();

        if ($existing) {
            $sale = $existing;
        }
    }

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

    // Step 1: Create or get existing sale
    $sale = \App\Models\CustomerSale::firstOrNew([
        'user_id' => $user->id,
        'customer_id' => $id
    ]);

    // Step 2: Attempt to auto-link appointment (if not already linked)
    if (!$sale->appointment_id) {
        $appointment = \App\Models\Appointment::where('customer_id', $id)
            ->whereNotNull('arrival_time')
            ->latest('arrival_time')
            ->first();

        if ($appointment) {
            $sale->appointment_id = $appointment->id;
        }
    }

    // Step 3: Save sale info
    $sale->notes = $request->input('notes');
    $sale->disposition = $request->input('disposition');
    $sale->ended_at = now();
    $sale->save();

    // Step 4: Determine Start Time (Queue > Appointment > CreatedAt)
    $startAt = null;
    $startSource = null;

    $queue = \App\Models\Queue::where('user_id', $user->id)
        ->where('customer_id', $id)
        ->whereNotNull('took_turn_at')
        ->latest('took_turn_at')
        ->first();

    if ($queue) {
        $startAt = $queue->took_turn_at;
        $startSource = 'Queue';
    } elseif ($sale->appointment_id) {
        $appointment = \App\Models\Appointment::find($sale->appointment_id);
        if ($appointment && $appointment->arrival_time) {
            $startAt = $appointment->arrival_time;
            $startSource = 'Appointment Arrival Time';
        }
    }

    if (!$startAt) {
        $startAt = $sale->created_at;
        $startSource = 'CreatedAt (Fallback)';
    }

    // Step 5: Calculate Duration
    $endAt = $sale->ended_at;
    $duration = 'N/A';

    if ($startAt && $endAt) {
        $start = \Carbon\Carbon::parse($startAt);
        $end = \Carbon\Carbon::parse($endAt);

        if ($start->lte($end)) {
            $diffSeconds = $start->diffInSeconds($end);
            $hours = floor($diffSeconds / 3600);
            $minutes = floor(($diffSeconds % 3600) / 60);
            $seconds = $diffSeconds % 60;

            $duration = sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
        } else {
            $duration = 'Start > End';
        }
    }

    // Step 6: Logging (optional)
    \Log::info('Customer Sale Completed:', [
        'sale_id' => $sale->id,
        'user_id' => $user->id,
        'customer_id' => $id,
        'appointment_id' => $sale->appointment_id,
        'startAt' => $startAt,
        'startSource' => $startSource,
        'endAt' => $endAt,
        'duration' => $duration
    ]);

    // Step 7: Response
    return response()->json([
        'message' => 'Form saved successfully.',
        'duration' => $duration,
        'started_from' => $startSource
    ]);
}


// public function forwardToManager(Request $request)
// {
//     $customer = null;

//     if ($request->filled('id')) {
//         $customer = CustomerSale::find($request->id);
//     }

//     // If no direct customer but appointment is provided
//     if (!$customer && $request->filled('appointment_id')) {
//         $appointment = Appointment::find($request->appointment_id);

//         if (!$appointment) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'Appointment not found.',
//             ], 404);
//         }

//         // Create a new customer from appointment
//         $customer = new CustomerSale([
//             'name'           => $appointment->customer_name,
//             'phone'          => $appointment->customer_phone,
//             'user_id'        => auth()->id(),
//             'appointment_id' => $appointment->id,
//             'forwarded_to_manager' => true,
//             'forwarded_at'         => now(),
//         ]);
//         $customer->save();

//         // Mark appointment as completed
//         $appointment->status = 'completed';
//         $appointment->save();
//     }

//     // If customer still not found
//     if (!$customer) {
//         return response()->json([
//             'status'  => 'error',
//             'message' => 'No valid customer or appointment found.',
//         ], 404);
//     }

//     // If existing customer, update forward flags if needed
//     if (!$customer->forwarded_to_manager) {
//         $customer->forwarded_to_manager = true;
//         $customer->forwarded_at = now();
//         $customer->save();
//     }

//     return response()->json([
//         'status'   => 'forwarded',
//         'message'  => 'Customer forwarded to Sales Manager!',
//         'redirect' => route('sales.perosn', ['id' => $customer->id]),
//     ]);
// }

// CustomerController.php
public function forwardToManager(Request $request)
{
    $request->validate([
        'customer_id' => 'required|integer|exists:customer_sales,id',
    ]);

    $customer = CustomerSale::find($request->customer_id);
    $customer->forwarded_at = now(); // includes both date and time
    $customer->save();

    return response()->json([
        'status' => 'success',
        'message' => 'Customer has been forwarded to the Sales Manager.'
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

public function checkout(Request $request, $id)
{
    $person = Queue::find($id);
    if (!$person->is_checked_in) {
    //     return response()->json([
    //         'message' => 'This salesperson is already checked out.'
    //     ], 400);

        return redirect()->back()->with('error', 'This salesperson is already checked out.');
    }

    $person->is_checked_in = false;
    $person->checked_out_at = now();
    $person->save();

    // return response()->json([
    //     'message' => 'Checked out successfully!',
    // ]);

    return redirect()->back()->with('success', 'Checked out successfully!');
}

public function saveArrivalTime(Request $request)
{
    $request->validate([
        'appointment_id' => 'required|exists:appointments,id',
    ]);

    $appointment = Appointment::find($request->appointment_id);
    $appointment->arrival_time = now(); // Server time
    $appointment->save();

    return redirect()->route('sales.perosn', ['id' => $appointment->id]);
}

}

  
