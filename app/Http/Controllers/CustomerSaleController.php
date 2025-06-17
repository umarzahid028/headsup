<?php

namespace App\Http\Controllers;

use App\Models\CustomerSale;
use Illuminate\Http\Request;

class CustomerSaleController extends Controller
{
   public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id'      => 'required|exists:users,id',
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|max:255',
                'phone'        => 'required|string|max:20',
                'interest'     => 'nullable|string|max:255',
                'notes'        => 'nullable|string',
                'process'      => 'nullable|array',
                'disposition'  => 'nullable|array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
            ], 422);
        }

        $sale = CustomerSale::updateOrCreate(
            ['email' => $validated['email'], 'user_id' => $validated['user_id']],
            [
                'name'        => $validated['name'],
                'phone'       => $validated['phone'],
                'interest'    => $validated['interest'] ?? null,
                'notes'       => $validated['notes'] ?? null,
                'process'     => $validated['process'] ?? [],
                'disposition' => $validated['disposition'] ?? [],
            ]
        );

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

}
