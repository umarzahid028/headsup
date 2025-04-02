<?php

namespace App\Http\Controllers;

use App\Models\SalesIssue;
use App\Models\Vehicle;
use App\Models\User;
use App\Notifications\NewSalesIssueReported;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class SalesIssueController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:view sales issues')->only(['index', 'show']);
        $this->middleware('permission:create sales issues')->only(['create', 'store']);
        $this->middleware('permission:edit sales issues')->only(['edit', 'update']);
        $this->middleware('permission:review sales issues')->only(['updateStatus']);
    }

    /**
     * Display a listing of sales issues.
     */
    public function index(Request $request)
    {
        $query = SalesIssue::with(['vehicle', 'reportedBy', 'reviewedBy']);

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by priority
        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }

        // Filter by vehicle
        if ($vehicleId = $request->query('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $issues = $query->latest()->paginate(10);

        return view('sales.issues.index', compact('issues'));
    }

    /**
     * Show the form for creating a new sales issue.
     */
    public function create(Request $request)
    {
        $vehicle = null;
        if ($vehicleId = $request->query('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($vehicleId);
        }

        // Get all active vehicles for the dropdown
        $vehicles = Vehicle::orderBy('stock_number')
            ->select('id', 'stock_number', 'year', 'make', 'model')
            ->get();

        return view('sales.issues.create', [
            'vehicles' => $vehicles,
            'selectedVehicle' => $vehicle,
        ]);
    }

    /**
     * Store a newly created sales issue in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'issue_type' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $validated['reported_by_user_id'] = Auth::id();
        $validated['status'] = 'pending';

        $issue = SalesIssue::create($validated);

        // Notify managers
        $managers = User::role('manager')->get();
        Notification::send($managers, new NewSalesIssueReported($issue));

        return redirect()->route('sales.issues.show', $issue)
            ->with('success', 'Sales issue reported successfully.');
    }

    /**
     * Display the specified sales issue.
     */
    public function show(SalesIssue $issue)
    {
        $issue->load(['vehicle', 'reportedBy', 'reviewedBy', 'goodwillClaim']);
        return view('sales.issues.show', compact('issue'));
    }

    /**
     * Update the status of the specified sales issue.
     */
    public function updateStatus(Request $request, SalesIssue $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,reviewed',
            'review_notes' => 'required_if:status,reviewed|nullable|string',
        ]);

        $issue->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'],
            'reviewed_by_user_id' => Auth::id(),
            'reviewed_at' => $validated['status'] === 'reviewed' ? now() : null,
        ]);

        return redirect()->route('sales.issues.show', $issue)
            ->with('success', 'Issue status updated successfully.');
    }

    /**
     * Update the priority of the specified sales issue.
     */
    public function updatePriority(Request $request, SalesIssue $issue)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $issue->update($validated);

        return redirect()->route('sales.issues.show', $issue)
            ->with('success', 'Issue priority updated successfully.');
    }
} 