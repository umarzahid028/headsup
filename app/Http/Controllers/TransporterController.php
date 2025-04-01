<?php

namespace App\Http\Controllers;

use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TransporterController extends Controller
{
    /**
     * Display a listing of the transporters.
     */
    public function index(Request $request): View
    {
        $query = Transporter::query()->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        // Active status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $transporters = $query->paginate(10);
        return view('transporters.index', compact('transporters'));
    }

    /**
     * Show the form for creating a new transporter.
     */
    public function create(): View
    {
        return view('transporters.create');
    }

    /**
     * Store a newly created transporter in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Set is_active to true if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        Transporter::create($validated);
        return redirect()->route('transporters.index')
                         ->with('success', 'Transporter created successfully.');
    }

    /**
     * Display the specified transporter.
     */
    public function show(Transporter $transporter): View
    {
        $transporter->load('transports.vehicle');
        return view('transporters.show', compact('transporter'));
    }

    /**
     * Show the form for editing the specified transporter.
     */
    public function edit(Transporter $transporter): View
    {
        return view('transporters.edit', compact('transporter'));
    }

    /**
     * Update the specified transporter in storage.
     */
    public function update(Request $request, Transporter $transporter): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        // Handle the checkbox for is_active
        $validated['is_active'] = $request->has('is_active');

        $transporter->update($validated);
        return redirect()->route('transporters.index')
                         ->with('success', 'Transporter updated successfully.');
    }

    /**
     * Remove the specified transporter from storage.
     */
    public function destroy(Transporter $transporter): RedirectResponse
    {
        // Check if the transporter has any transports
        if ($transporter->transports()->exists()) {
            return redirect()->route('transporters.index')
                             ->with('error', 'Cannot delete transporter with associated transports.');
        }

        $transporter->delete();
        return redirect()->route('transporters.index')
                         ->with('success', 'Transporter removed successfully.');
    }
} 