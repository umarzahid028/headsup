<?php

namespace App\Http\Controllers;

use App\Models\InspectionItem;
use App\Models\InspectionPhoto;
use App\Models\InspectionAssignment;
use App\Models\ReconWorkflow;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InspectionItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = InspectionItem::with(['category', 'vehicle', 'assignedVendor']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $items = $query->latest('updated_at')->paginate(20);
        
        return view('recon.items.index', compact('items', 'status'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $workflowId = $request->get('workflow_id');
        $workflow = ReconWorkflow::findOrFail($workflowId);
        $categories = \App\Models\InspectionCategory::where('is_active', true)->get();
        
        return view('recon.items.create', compact('workflow', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'workflow_id' => 'required|exists:recon_workflows,id',
            'category_id' => 'required|exists:inspection_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,pass,repair,replace',
            'cost' => 'nullable|numeric|min:0',
            'is_vendor_visible' => 'boolean',
            'assigned_to' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
        ]);
        
        $workflow = ReconWorkflow::findOrFail($validated['workflow_id']);
        
        $item = InspectionItem::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'cost' => $validated['cost'],
            'is_vendor_visible' => $validated['is_vendor_visible'] ?? false,
            'is_completed' => $validated['status'] === 'pass',
            'vehicle_id' => $workflow->vehicle_id,
            'recon_workflow_id' => $workflow->id,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'assigned_at' => $validated['assigned_to'] ? now() : null,
            'notes' => $validated['notes'],
        ]);
        
        // Update workflow total items count
        $workflow->increment('total_items');
        
        if ($validated['status'] === 'pass') {
            $workflow->increment('completed_items');
        }
        
        return redirect()->route('recon.workflows.show', $workflow)
            ->with('success', 'Inspection item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InspectionItem $item)
    {
        $item->load(['category', 'vehicle', 'reconWorkflow', 'assignedVendor', 'photos', 'assignments.vendor']);
        
        return view('recon.items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InspectionItem $item)
    {
        $categories = \App\Models\InspectionCategory::where('is_active', true)->get();
        $vendors = Vendor::where('is_active', true)
            ->where(function($query) {
                $query->where('type', 'service')
                    ->orWhere('type', 'parts');
            })
            ->get();
            
        return view('recon.items.edit', compact('item', 'categories', 'vendors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InspectionItem $item)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:inspection_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,pass,repair,replace',
            'cost' => 'nullable|numeric|min:0',
            'is_vendor_visible' => 'boolean',
            'assigned_to' => 'nullable|exists:vendors,id',
            'notes' => 'nullable|string',
        ]);
        
        $workflow = $item->reconWorkflow;
        $wasCompleted = $item->is_completed;
        
        // Update completion status based on the status
        $validated['is_completed'] = $validated['status'] === 'pass';
        
        // Set completed time if item is now completed
        if ($validated['is_completed'] && !$wasCompleted) {
            $validated['completed_at'] = now();
            $validated['completed_by'] = Auth::id();
            $workflow->increment('completed_items');
        } 
        // If item was completed but now isn't
        elseif (!$validated['is_completed'] && $wasCompleted) {
            $validated['completed_at'] = null;
            $validated['completed_by'] = null;
            $workflow->decrement('completed_items');
        }
        
        // Set assigned time if item is being assigned
        if ($validated['assigned_to'] && $item->assigned_to !== $validated['assigned_to']) {
            $validated['assigned_at'] = now();
        }
        
        $item->update($validated);
        
        // Update workflow total cost
        $totalCost = $workflow->inspectionItems()->sum('cost');
        $workflow->update(['total_cost' => $totalCost]);
        
        return redirect()->route('recon.workflows.show', $workflow)
            ->with('success', 'Inspection item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InspectionItem $item)
    {
        $workflow = $item->reconWorkflow;
        
        // If item was completed, decrement the workflow completed items count
        if ($item->is_completed) {
            $workflow->decrement('completed_items');
        }
        
        // Decrement total items count
        $workflow->decrement('total_items');
        
        // Delete the item
        $item->delete();
        
        return redirect()->route('recon.workflows.show', $workflow)
            ->with('success', 'Inspection item deleted successfully.');
    }
    
    /**
     * Mark an inspection item as complete.
     */
    public function complete(Request $request, InspectionItem $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:pass,repair,replace',
            'notes' => 'nullable|string',
        ]);
        
        $workflow = $item->reconWorkflow;
        $wasCompleted = $item->is_completed;
        
        // Only pass status should mark as completed
        $isCompleted = $validated['status'] === 'pass';
        
        $item->update([
            'status' => $validated['status'],
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
            'completed_by' => $isCompleted ? Auth::id() : null,
            'notes' => $validated['notes'] ?? $item->notes,
        ]);
        
        // Update workflow completed items count
        if ($isCompleted && !$wasCompleted) {
            $workflow->increment('completed_items');
        } 
        elseif (!$isCompleted && $wasCompleted) {
            $workflow->decrement('completed_items');
        }
        
        return redirect()->route('recon.workflows.show', $workflow)
            ->with('success', 'Inspection item updated successfully.');
    }
    
    /**
     * Assign a vendor to an inspection item.
     */
    public function assignVendor(Request $request, InspectionItem $item)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'is_internal' => 'boolean',
            'quoted_cost' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'internal_notes' => 'nullable|string',
        ]);
        
        // Create an assignment
        InspectionAssignment::create([
            'inspection_item_id' => $item->id,
            'vendor_id' => $validated['vendor_id'],
            'status' => 'pending',
            'quoted_cost' => $validated['quoted_cost'],
            'due_date' => $validated['due_date'],
            'assigned_by' => Auth::id(),
            'assigned_at' => now(),
            'internal_notes' => $validated['internal_notes'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);
        
        // Update the inspection item
        $item->update([
            'assigned_to' => $validated['vendor_id'],
            'assigned_at' => now(),
            'cost' => $validated['quoted_cost'] ?? $item->cost,
        ]);
        
        return redirect()->route('recon.workflows.show', $item->reconWorkflow)
            ->with('success', 'Vendor assigned successfully.');
    }
    
    /**
     * Upload photos for an inspection item.
     */
    public function uploadPhotos(Request $request, InspectionItem $item)
    {
        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|max:10240', // 10MB max
            'caption' => 'nullable|string|max:255',
            'is_vendor_visible' => 'boolean',
        ]);
        
        $photos = $request->file('photos');
        $caption = $request->input('caption');
        $isVendorVisible = $request->input('is_vendor_visible', false);
        
        foreach ($photos as $photo) {
            $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('inspection-photos/' . $item->id, $filename, 'public');
            
            // Create thumbnail
            // For simplicity, we're skipping thumbnail creation here but would implement in production
            
            InspectionPhoto::create([
                'inspection_item_id' => $item->id,
                'file_path' => $path,
                'file_name' => $filename,
                'mime_type' => $photo->getMimeType(),
                'file_size' => $photo->getSize(),
                'caption' => $caption,
                'uploaded_by' => Auth::id(),
                'is_vendor_visible' => $isVendorVisible,
            ]);
        }
        
        return redirect()->route('recon.items.show', $item)
            ->with('success', 'Photos uploaded successfully.');
    }
    
    /**
     * Delete a photo.
     */
    public function deletePhoto(InspectionPhoto $photo)
    {
        $item = $photo->inspectionItem;
        
        // Delete the file
        Storage::disk('public')->delete($photo->file_path);
        
        // Delete the thumbnail if it exists
        if ($photo->thumbnail_path) {
            Storage::disk('public')->delete($photo->thumbnail_path);
        }
        
        // Delete the record
        $photo->delete();
        
        return redirect()->route('recon.items.show', $item)
            ->with('success', 'Photo deleted successfully.');
    }
}
