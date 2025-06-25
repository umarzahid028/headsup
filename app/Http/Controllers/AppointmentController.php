<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\CustomerSale;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
public function index(Request $request)
{
    $appointments = Appointment::latest()->paginate(10);
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

    $rules = [
        'customer_name' => 'required',
        'customer_phone' => 'required',
        'date' => 'required|date',
        'time' => 'required',
    ];

    if (in_array($user->getRoleNames()->first(), ['Admin', 'Sales Manager'])) {
        $rules['salesperson_id'] = 'required|exists:users,id';
    }

    $validated = $request->validate($rules);

    $salespersonId = in_array($user->getRoleNames()->first(), ['Admin', 'Sales Manager']) 
        ? $request->salesperson_id 
        : $user->id;

    Appointment::create([
        'created_by' => $user->id,
        'salesperson_id' => $salespersonId,
        'customer_name' => $request->customer_name,
        'customer_phone' => $request->customer_phone,
        'date' => $request->date,
        'time' => $request->time,
        'notes' => $request->notes,
        'status' => 'scheduled',
    ]);

    return response()->json([
        'message' => 'Appointment booked successfully.'
    ]);
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
    $validated = $request->validate([
        'customer_name'   => 'required|string',
        'customer_phone'  => 'required|string',
        'date'            => 'required|date',
        'time'            => 'required',
        'status'          => 'required|in:scheduled,completed,canceled',
        'salesperson_id'  => 'required|exists:users,id',  
        'notes'           => 'nullable|string',
    ]);

    $user = auth()->user();

    $salespersonId = in_array($user->getRoleNames()->first(), ['Admin', 'Sales Manager']) 
    ? $request->salesperson_id 
    : $user->id;
    

    $appointment->update([
        'customer_name'   => $validated['customer_name'],
        'customer_phone'  => $validated['customer_phone'],
        'date'            => $validated['date'],
        'time'            => $validated['time'],
        'status'          => $validated['status'],
        'salesperson_id'  =>   $salespersonId,
        'notes'           => $validated['notes'] ?? null,
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Appointment updated successfully.',
        'redirect' => route('appointment.records')
    ]);
}


}
