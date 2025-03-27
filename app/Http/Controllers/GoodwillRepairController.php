<?php

namespace App\Http\Controllers;

use App\Models\GoodwillRepair;
use App\Models\Vehicle;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PDF;

class GoodwillRepairController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $repairs = GoodwillRepair::with(['vehicle', 'assignedTo', 'vendor'])
            ->orderByDesc('created_at')
            ->paginate(15);
            
        return view('goodwill-repairs.index', compact('repairs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $vehicle = null;
        if ($request->has('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
        }
        
        $vendors = Vendor::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('goodwill-repairs.create', compact('vehicle', 'vendors', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
        ]);
        
        // Add created_by field
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        $repair = GoodwillRepair::create($validated);
        
        return redirect()->route('goodwill-repairs.show', $repair)
            ->with('success', 'Goodwill repair created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodwillRepair $goodwillRepair)
    {
        $goodwillRepair->load(['vehicle', 'assignedTo', 'vendor', 'createdBy']);
        
        return view('goodwill-repairs.show', compact('goodwillRepair'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodwillRepair $goodwillRepair)
    {
        $goodwillRepair->load(['vehicle', 'assignedTo', 'vendor']);
        
        $vendors = Vendor::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('goodwill-repairs.edit', compact('goodwillRepair', 'vendors', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GoodwillRepair $goodwillRepair)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
        ]);
        
        // Set completed_at if status changed to completed
        if ($validated['status'] === 'completed' && $goodwillRepair->status !== 'completed') {
            $validated['completed_at'] = now();
        }
        
        $goodwillRepair->update($validated);
        
        return redirect()->route('goodwill-repairs.show', $goodwillRepair)
            ->with('success', 'Goodwill repair updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodwillRepair $goodwillRepair)
    {
        $goodwillRepair->delete();
        
        return redirect()->route('goodwill-repairs.index')
            ->with('success', 'Goodwill repair deleted successfully.');
    }
    
    /**
     * Show the waiver signature form.
     */
    public function showWaiverForm(GoodwillRepair $goodwillRepair)
    {
        return view('goodwill-repairs.waiver', compact('goodwillRepair'));
    }
    
    /**
     * Process waiver signature.
     */
    public function processWaiver(Request $request, GoodwillRepair $goodwillRepair)
    {
        $validated = $request->validate([
            'signature_data' => 'required|string',
            'customer_name' => 'required|string|max:255',
        ]);
        
        // Store signature data
        $goodwillRepair->signature_data = $validated['signature_data'];
        $goodwillRepair->waiver_signed = true;
        $goodwillRepair->waiver_signed_at = now();
        $goodwillRepair->signature_ip = $request->ip();
        
        // Update customer name if different
        if ($goodwillRepair->customer_name !== $validated['customer_name']) {
            $goodwillRepair->customer_name = $validated['customer_name'];
        }
        
        // Generate PDF
        $pdf = $this->generateWaiverPdf($goodwillRepair);
        
        // Save PDF to storage
        $pdfPath = 'waivers/' . $goodwillRepair->id . '_' . time() . '.pdf';
        Storage::put($pdfPath, $pdf->output());
        $goodwillRepair->waiver_pdf_path = $pdfPath;
        
        $goodwillRepair->save();
        
        // Send SMS if phone number exists
        if ($goodwillRepair->customer_phone) {
            $this->sendWaiverSms($goodwillRepair);
        }
        
        return redirect()->route('goodwill-repairs.waiver-complete', $goodwillRepair);
    }
    
    /**
     * Show waiver completion page.
     */
    public function waiverComplete(GoodwillRepair $goodwillRepair)
    {
        return view('goodwill-repairs.waiver-complete', compact('goodwillRepair'));
    }
    
    /**
     * Generate waiver PDF document.
     */
    private function generateWaiverPdf(GoodwillRepair $goodwillRepair)
    {
        $pdf = PDF::loadView('goodwill-repairs.pdf.waiver', compact('goodwillRepair'));
        return $pdf;
    }
    
    /**
     * Send waiver PDF via SMS.
     */
    private function sendWaiverSms(GoodwillRepair $goodwillRepair)
    {
        // This would be implemented with a real SMS service like Twilio
        // For now, just mark as sent
        $goodwillRepair->sms_sent = true;
        $goodwillRepair->sms_sent_at = now();
        $goodwillRepair->sms_status = 'success';
        $goodwillRepair->save();
        
        return true;
    }
    
    /**
     * Download waiver PDF.
     */
    public function downloadWaiver(GoodwillRepair $goodwillRepair)
    {
        if (!$goodwillRepair->waiver_pdf_path) {
            return redirect()->back()->with('error', 'No waiver PDF found for this repair');
        }
        
        return Storage::download(
            $goodwillRepair->waiver_pdf_path, 
            'Goodwill_Waiver_' . $goodwillRepair->id . '.pdf'
        );
    }
}
