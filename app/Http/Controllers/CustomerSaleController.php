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
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'interest' => 'nullable|string',
            'notes' => 'nullable|string',
            'process' => 'nullable|array',
            'disposition' => 'nullable|array',
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
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'interest' => $validated['interest'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'process' => $validated['process'] ?? [],
            'disposition' => $validated['disposition'] ?? [],
        ]
    );

    return response()->json([
        'status' => 'success',
        'message' => 'Customer sale data saved successfully!',
        'id' => $sale->id,
    ]);
}

}
