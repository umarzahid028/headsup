<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index()
    {
        $vendors = Vendor::latest()->paginate(15);
        return view('vendors.index', compact('vendors'));
    }

    /**
     * Display a listing of transporters.
     */
    public function transporters()
    {
        $transporters = Vendor::transporters()->latest()->paginate(15);
        return view('vendors.transporters', compact('transporters'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $types = [
            'dealer' => 'Dealer',
            'auction' => 'Auction',
            'private' => 'Private Party',
            'transportation' => 'Transportation Company',
        ];
        
        return view('vendors.create', compact('types'));
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:dealer,auction,private,transportation',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);
        
        Vendor::create($validated);
        
        if ($validated['type'] === 'transportation') {
            return redirect()->route('transporters.index')
                ->with('success', 'Transporter created successfully.');
        }
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $types = [
            'dealer' => 'Dealer',
            'auction' => 'Auction',
            'private' => 'Private Party',
            'transportation' => 'Transportation Company',
        ];
        
        return view('vendors.edit', compact('vendor', 'types'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:dealer,auction,private,transportation',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'is_active' => 'boolean',
        ]);
        
        $vendor->update($validated);
        
        if ($validated['type'] === 'transportation') {
            return redirect()->route('transporters.index')
                ->with('success', 'Transporter updated successfully.');
        }
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if the vendor is being used in any vehicles or tasks
        $isInUse = DB::table('vehicles')->where('purchased_from', $vendor->id)->exists()
            || DB::table('vehicles')->where('transporter_id', $vendor->id)->exists()
            || DB::table('tasks')->where('vendor_id', $vendor->id)->exists();
            
        if ($isInUse) {
            if ($vendor->type === 'transportation') {
                return redirect()->route('transporters.index')
                    ->with('error', 'This transporter cannot be deleted as it is being used by vehicles or tasks.');
            }
            
            return redirect()->route('vendors.index')
                ->with('error', 'This vendor cannot be deleted as it is being used by vehicles or tasks.');
        }
        
        $vendor->delete();
        
        if ($vendor->type === 'transportation') {
            return redirect()->route('transporters.index')
                ->with('success', 'Transporter deleted successfully.');
        }
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
