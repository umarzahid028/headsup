<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Vendor::query();
        
        if ($type) {
            $query->where('type', $type);
        }
        
        $vendors = $query->orderBy('name')->get();
        
        $types = [
            'mechanical' => 'Mechanical',
            'body_shop' => 'Body Shop',
            'detail' => 'Detailing',
            'tire' => 'Tire Services',
            'upholstery' => 'Upholstery',
            'glass' => 'Glass Repair',
            'other' => 'Other Services',
        ];
        
        return view('vendors.index', compact('vendors', 'types', 'type'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $types = [
            'mechanical' => 'Mechanical',
            'body_shop' => 'Body Shop',
            'detail' => 'Detailing',
            'tire' => 'Tire Services',
            'upholstery' => 'Upholstery',
            'glass' => 'Glass Repair',
            'other' => 'Other Services',
        ];
        
        return view('vendors.create', compact('types'));
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:mechanical,body_shop,detail,tire,upholstery,glass,other',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Set is_active default if not provided
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        Vendor::create($validated);
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $inspectionItems = $vendor->inspectionItemResults()
            ->with(['inspectionItem', 'vehicleInspection.vehicle'])
            ->get()
            ->groupBy('vehicleInspection.vehicle_id');
            
        return view('vendors.show', compact('vendor', 'inspectionItems'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $types = [
            'mechanical' => 'Mechanical',
            'body_shop' => 'Body Shop',
            'detail' => 'Detailing',
            'tire' => 'Tire Services',
            'upholstery' => 'Upholstery',
            'glass' => 'Glass Repair',
            'other' => 'Other Services',
        ];
        
        return view('vendors.edit', compact('vendor', 'types'));
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vendors')->ignore($vendor->id),
            ],
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'required|in:mechanical,body_shop,detail,tire,upholstery,glass,other',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Set is_active default if not provided
        $validated['is_active'] = $validated['is_active'] ?? false;
        
        $vendor->update($validated);
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if the vendor has any associated inspections or items
        if ($vendor->vehicleInspections()->count() > 0 || $vendor->inspectionItemResults()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Cannot delete a vendor with associated inspections or repair items.');
        }
        
        $vendor->delete();
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Toggle active status of the vendor.
     */
    public function toggleActive(Vendor $vendor)
    {
        $vendor->update(['is_active' => !$vendor->is_active]);
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor status updated successfully.');
    }
} 