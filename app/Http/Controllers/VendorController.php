<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\LoginCredentials;

class VendorController extends Controller
{
    protected $specialties = [
        'mechanical' => 'Mechanical',
        'body_shop' => 'Body Shop',
        'detail' => 'Detailing',
        'tire' => 'Tire Services',
        'upholstery' => 'Upholstery',
        'glass' => 'Glass Repair',
        'other' => 'Other Services',
    ];

    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Vendor::query()->with('type');
        
        if ($type) {
            $query->where('specialty_tags', 'like', '%' . $type . '%');
        }
        
        $vendors = $query->orderBy('created_at', 'desc')->get();
        
        return view('vendors.index', [
            'vendors' => $vendors,
            'specialties' => $this->specialties,
            'type' => $type,
            'types' => $this->specialties, // Use specialties for the filter dropdown
        ]);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $types = VendorType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('vendors.create', [
            'specialties' => $this->specialties,
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'specialty_tags' => 'required|array',
            'specialty_tags.*' => 'required|in:mechanical,body_shop,detail,tire,upholstery,glass,other',
            'type_id' => 'nullable|exists:vendor_types,id',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        // Set is_active default if not provided
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Store the password before creating the vendor
        $password = $validated['password'];
        unset($validated['password']); // Remove password from vendor data

        // Create the vendor
        $vendor = Vendor::create($validated);

        // Create the user account manually since we have a password
        $user = User::create([
            'name' => $validated['contact_person'] ?? $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
        ]);

        // Assign vendor role
        $user->assignRole('Vendor');

        // Send welcome notification without password since user set it themselves
        $user->notify(new LoginCredentials('(your chosen password)', 'Vendor'));
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load('type');
        
        $inspectionItems = $vendor->inspectionItemResults()
            ->with(['inspectionItem', 'vehicleInspection.vehicle'])
            ->get()
            ->groupBy('vehicleInspection.vehicle_id');
            
        return view('vendors.show', [
            'vendor' => $vendor,
            'inspectionItems' => $inspectionItems,
            'types' => VendorType::orderBy('name')->get(),
            'specialties' => $this->specialties,
        ]);
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $types = VendorType::orderBy('name')->get();
        
        return view('vendors.edit', [
            'vendor' => $vendor,
            'specialties' => $this->specialties,
            'types' => $types,
        ]);
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
            'specialty_tags' => 'required|array',
            'specialty_tags.*' => 'required|in:mechanical,body_shop,detail,tire,upholstery,glass,other',
            'type_id' => 'nullable|exists:vendor_types,id',
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