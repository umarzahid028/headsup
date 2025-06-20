<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\CustomerSale;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

       if ($user->hasRole('Sales person')) {
            $appointments = Appointment::where('salesperson_id', $user->id)->latest()->get();
        } elseif ($user->hasAnyRole(['Admin', 'Sales Manager'])) {
            $appointments = Appointment::latest()->get();
        } else {
            $appointments = collect();
        }

        return view('appointments.index', compact('appointments'));
    }
    public function create()
    {
        $salespersons = User::role('Sales person')->get();
        return view('appointments.create', compact('salespersons'));
    }

    public function store(Request $request)
{
    $user = auth()->user();

    // Validate basic fields
    $rules = [
        'customer_name' => 'required',
        'customer_phone' => 'required',
        'date' => 'required|date',
        'time' => 'required',
    ];

    // Only validate salesperson_id if user is Admin or Sales Manager
    if (in_array($user->role, ['Admin', 'Sales Manager'])) {
        $rules['salesperson_id'] = 'required|exists:users,id';
    }

    $request->validate($rules);

    // Determine salesperson_id
    $salespersonId = in_array($user->role, ['Admin', 'Sales Manager']) 
        ? $request->salesperson_id 
        : $user->id;

    // Create appointment
    Appointment::create([
        'created_by' => $user->id,
        'salesperson_id' => $salespersonId,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'date' => $request->date,
        'time' => $request->time,
        'notes' => $request->notes,
        'status' => 'pending',
    ]);

    return redirect('/appointments')->with('success', 'Appointment booked successfully.');
}


public function edit(Appointment $appointment)
{
    $user = auth()->user();
    $salespersons = User::role('Sales person')->get();
     if ($user->hasRole('Sales person')) {
            $appointments = Appointment::where('salesperson_id', $user->id)->latest()->get();
        } elseif ($user->hasAnyRole(['Admin', 'Sales Manager'])) {
            $appointments = Appointment::latest()->get();
        } else {
            $appointments = collect();
        }

    return view('appointments.edit', compact('appointment', 'salespersons', 'appointments'));
}

public function update(Request $request, Appointment $appointment)
{
    $user = auth()->user();

    // Validation rules
    $rules = [
        'customer_name' => 'required',
        'customer_phone' => 'required',
        'date' => 'required|date',
        'time' => 'required',
    ];

    // Admin or Sales Manager must provide salesperson_id
    if (in_array($user->role, ['Admin', 'Sales Manager'])) {
        $rules['salesperson_id'] = 'required|exists:users,id';
    }

    $request->validate($rules);

    // Determine salesperson_id
    $salespersonId = in_array($user->role, ['Admin', 'Sales Manager']) 
        ? $request->salesperson_id 
        : $user->id;

    // Update appointment
    $appointment->update([
        'salesperson_id' => $salespersonId,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'date' => $request->date,
        'time' => $request->time,
        'notes' => $request->notes,
    ]);

    return redirect()->route('appointment.records')->with('success', 'Appointment updated successfully.');
}



    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        
        if ($appointment->salesperson_id !== auth()->id() && !auth()->user()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:processing,completed,no_show',
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated.');
    }

}
