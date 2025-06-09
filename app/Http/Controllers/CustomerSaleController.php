<?php

namespace App\Http\Controllers;

use App\Models\CustomerSale;
use Illuminate\Http\Request;

class CustomerSaleController extends Controller
{
    public function store(Request $request)
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

    return response()->json([
        'status' => 'success',
        'message' => 'Customer sale data saved successfully!',
    ]);
}

}
