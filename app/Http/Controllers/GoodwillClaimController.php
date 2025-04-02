<?php

namespace App\Http\Controllers;

use App\Models\GoodwillClaim;
use App\Models\Vehicle;
use App\Models\SalesIssue;
use App\Models\User;
use App\Notifications\NewGoodwillClaimSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class GoodwillClaimController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
       // $this->middleware('permission:view goodwill claims')->only(['index', 'show']);
       // $this->middleware('permission:create goodwill claims')->only(['create', 'store']);
       // $this->middleware('permission:edit goodwill claims')->only(['edit', 'update']);
       // $this->middleware('permission:approve goodwill claims|reject goodwill claims')->only(['updateStatus']);
       // $this->middleware('permission:update goodwill claims')->only(['updateConsent']);
    }

    /**
     * Display a listing of goodwill claims.
     */
    public function index(Request $request)
    {
        $query = GoodwillClaim::with(['vehicle', 'salesIssue', 'createdBy', 'approvedBy']);

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by vehicle
        if ($vehicleId = $request->query('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $claims = $query->latest()->paginate(10);

        return view('sales.goodwill-claims.index', compact('claims'));
    }

    /**
     * Show the form for creating a new goodwill claim.
     */
    public function create(Request $request)
    {
        $vehicle = null;
        $salesIssue = null;

        if ($vehicleId = $request->query('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($vehicleId);
        }

        if ($issueId = $request->query('sales_issue_id')) {
            $salesIssue = SalesIssue::with('vehicle')->findOrFail($issueId);
            $vehicle = $salesIssue->vehicle;
        }

        // Get all active vehicles for the dropdown
        $vehicles = Vehicle::orderBy('stock_number')
            ->select('id', 'stock_number', 'year', 'make', 'model')
            ->get();

        return view('sales.goodwill-claims.create', [
            'vehicles' => $vehicles,
            'selectedVehicle' => $vehicle,
            'salesIssue' => $salesIssue,
        ]);
    }

    /**
     * Store a newly created goodwill claim in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'sales_issue_id' => 'nullable|exists:sales_issues,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'issue_description' => 'required|string',
            'requested_resolution' => 'required|string',
            'customer_consent' => 'required|boolean',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by_user_id'] = Auth::id();
        $validated['status'] = 'pending';
        
        if ($validated['customer_consent']) {
            $validated['customer_consent_date'] = now();
        }

        $claim = GoodwillClaim::create($validated);

        // Notify admins and managers
        $managers = User::role(['admin', 'manager'])->get();
        Notification::send($managers, new NewGoodwillClaimSubmitted($claim));

        return redirect()->route('sales.goodwill-claims.show', $claim)
            ->with('success', 'Goodwill claim submitted successfully.');
    }

    /**
     * Display the specified goodwill claim.
     */
    public function show(GoodwillClaim $claim)
    {
        $claim->load(['vehicle', 'salesIssue', 'createdBy', 'approvedBy']);
        return view('sales.goodwill-claims.show', compact('claim'));
    }

    /**
     * Update the specified goodwill claim status.
     */
    public function updateStatus(Request $request, GoodwillClaim $claim)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'approval_notes' => 'required_unless:status,pending|nullable|string',
            'estimated_cost' => 'required_if:status,approved|nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
        ]);

        $claim->update([
            'status' => $validated['status'],
            'approval_notes' => $validated['approval_notes'],
            'estimated_cost' => $validated['estimated_cost'],
            'actual_cost' => $validated['actual_cost'],
            'approved_by_user_id' => Auth::id(),
            'approved_at' => in_array($validated['status'], ['approved', 'rejected']) ? now() : null,
        ]);

        return redirect()->route('sales.goodwill-claims.show', $claim)
            ->with('success', 'Claim status updated successfully.');
    }

    /**
     * Update customer consent for the goodwill claim.
     */
    public function updateConsent(Request $request, GoodwillClaim $claim)
    {
        $validated = $request->validate([
            'customer_consent' => 'required|boolean',
        ]);

        $claim->update([
            'customer_consent' => $validated['customer_consent'],
            'customer_consent_date' => $validated['customer_consent'] ? now() : null,
        ]);

        return redirect()->route('sales.goodwill-claims.show', $claim)
            ->with('success', 'Customer consent updated successfully.');
    }
} 