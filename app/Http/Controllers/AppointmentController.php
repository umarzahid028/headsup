<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Appointment;
use App\Models\CustomerSale;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
     public function index() {
        $user = auth()->user();

       if ($user->hasRole('Sales person')) {
    $appointments = Appointment::where('salesperson_id', $user->id)->get();
} elseif ($user->hasRole('Sales Manager')) {
    $appointments = Appointment::all();
} else {
    $appointments = collect(); 
}
return view('appointments.index', compact('appointments'));
}
    public function create() {
        $salespersons = User::role('Sales person')->get();
        return view('appointments.create', compact('salespersons'));
    }

    public function store(Request $request) {
        $request->validate([
            'salesperson_id' => 'required|exists:users,id',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        Appointment::create([
            'created_by' => auth()->id(),
            'salesperson_id' => $request->salesperson_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'date' => $request->date,
            'time' => $request->time,
            'notes' => $request->notes,
            'status' => 'scheduled',
        ]);

        return redirect('/appointments')->with('success', 'Appointment booked successfully.');
    }

    public function updateStatus(Request $request, $id) {
        $appointment = Appointment::findOrFail($id);

        if ($appointment->salesperson_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:processing,completed,no_show',
        ]);

        $appointment->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status updated.');
    }

    public function appointmentform()
    {
        return view('appointments/form');
    }

      public function appointmentstore(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'interest' => 'nullable|string',
        'notes' => 'nullable|string',
        'process' => 'nullable|array',
        'disposition' => 'nullable|array',
    ]);

    CustomerSale::create($validated);

    return redirect()->route('appointment.records')->with('success','Customer sale data saved successfully!');
}
}
