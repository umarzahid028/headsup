<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
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
        'user_id'        => $validated['user_id'] ?? auth()->id(),
        'customer_id'    => $validated['customer_id'] ?? null,
        'name'           => $validated['name'],
        'email'          => $validated['email'] ?? null,
        'phone'          => $validated['phone'] ?? null,
        'interest'       => $validated['interest'] ?? null,
        'notes'          => $validated['notes'] ?? null,
        'process'        => $validated['process'] ?? [],
        'disposition'    => $validated['disposition'] ?? null,
        'appointment_id' => $validated['appointment_id'] ?? null,
    ];

    $sale = null;

    // ✅ 1. If ID is given
    if (!empty($validated['id'])) {
        $sale = CustomerSale::find($validated['id']);
    }

    // ✅ 2. If appointment already used
    if (!$sale && !empty($data['appointment_id'])) {
        $sale = CustomerSale::where('appointment_id', $data['appointment_id'])->first();
    }

    // ✅ 3. Same-day sale for same customer
    if (!$sale && !empty($data['customer_id'])) {
        $sale = CustomerSale::where('user_id', $data['user_id'])
            ->where('customer_id', $data['customer_id'])
            ->whereDate('created_at', now()->toDateString())
            ->latest()
            ->first();
    }

    // ✅ Create or Update
    if ($sale) {
        $sale->update($data);
    } else {
        $sale = CustomerSale::create($data);
    }

    // ✅ End sale time
    if (!empty($validated['disposition'])) {
        $sale->ended_at = now();
        $sale->save();
    }

    // ✅ Mark appointment as completed
    if (!empty($sale->appointment_id)) {
        $appointment = \App\Models\Appointment::find($sale->appointment_id);
        if ($appointment && $appointment->status !== 'completed') {
            $appointment->status = 'completed';
            $appointment->save();
        }
    }

    // ✅ Duration calculation from queue
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

    // ✅ Clean redirect URL
    if (!empty($sale->appointment_id)) {
        $redirectUrl = route('sales.perosn'); // Redirect to clean /sales route
    } else {
        $redirectUrl = url()->previous(); // Stay on current page
    }

    return response()->json([
        'status'   => 'success',
        'message'  => 'Customer sale data saved successfully!',
        'duration' => $duration,
        'id'       => $sale->id,
        'redirect' => $redirectUrl,
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
        $currentUser = auth()->user();

        if (!$currentUser->hasRole('Sales Manager') && $customer->user_id !== $currentUser->id) {
            return response()->json([
                'message' => 'Unauthorized to transfer this customer.'
            ], 403);
        }

        $isCheckedIn = User::whereHas('queues', function ($query) {
            $query->where('is_checked_in', true)
                ->whereNull('checked_out_at') // or where('is_checked_out', false)
                ->whereDate('created_at', today());
        })
            ->get();


        if (!$isCheckedIn) {
            return response()->json([
                'message' => 'The selected sales person is not checked-in.',
            ], 422);
        }

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
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        // ✅ Get all customers (or apply filter if needed)
        $customers = CustomerSale::get();

        // ✅ Get only users with "Sales person" role who are currently checked-in
        $checkedInUserIds = Queue::where('is_checked_in', true)
            ->latest('id')
            ->pluck('user_id')
            ->unique();

        $salespeople = User::role('Sales person')
            ->whereIn('id', function ($subquery) {
                $subquery->select('user_id')
                    ->from('queues as q1')
                    ->whereRaw('q1.id = (
                SELECT q2.id FROM queues as q2
                WHERE q2.user_id = q1.user_id
                ORDER BY q2.created_at DESC
                LIMIT 1
            )')
                    ->whereNull('q1.checked_out_at'); // only un-checked queues
            })
            ->get();


        return view('t/o-customers.customer', compact('customers', 'salespeople'));
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

        // ✅ Always update forwarding time
        $customer->forwarded_to_manager = true;
        $customer->forwarded_at = now();
        $customer->save();

        return response()->json([
            'status'   => 'forwarded',
            'message'  => 'T/O Customer Success !',
            'redirect' => route('sales.perosn', ['id' => $customer->id]),
        ]);
    }


    // CustomerController.php
    // public function forward(Request $request)
    // {
    //     $request->validate([
    //         'customer_id' => 'required|integer|exists:customer_sales,id',
    //     ]);

    //     $customer = CustomerSale::find($request->customer_id);
    //     $customer->forwarded_at = now(); // includes both date and time
    //     $customer->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Customer has been forwarded to the Sales Manager.'
    //     ]);
    // }



    public function fetch()
    {
        $customers = CustomerSale::with('user')->get();
        return view('partials.customer-list', compact('customers'));
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
