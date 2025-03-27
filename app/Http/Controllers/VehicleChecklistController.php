<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\ReadyToPostChecklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleChecklistController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('view checklists');
        
        $checklists = ReadyToPostChecklist::query()
            ->with('vehicle', 'completedByUser');
        
        // Apply filters
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'complete') {
                $checklists->where('is_complete', true);
            } elseif ($status === 'incomplete') {
                $checklists->where('is_complete', false);
            }
        }
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $checklists->whereHas('vehicle', function ($query) use ($search) {
                $query->where('stock_number', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }
        
        $checklists = $checklists->latest()
            ->paginate(15)
            ->withQueryString();
        
        return view('checklists.index', compact('checklists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create checklists');
        
        $vehicleId = $request->input('vehicle_id');
        $vehicle = null;
        
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
        } else {
            // Get vehicles without a checklist
            $vehicles = Vehicle::whereDoesntHave('readyToPostChecklist')
                ->where('is_frontline_ready', false)
                ->where('is_archived', false)
                ->where('is_sold', false)
                ->get();
                
            return view('checklists.select-vehicle', compact('vehicles'));
        }
        
        // Default checklist items
        $defaultItems = [
            ['name' => 'Vehicle Information Verification', 'description' => 'Verify VIN, make, model, year, and trim information', 'order' => 1, 'is_required' => true],
            ['name' => 'Photos Complete', 'description' => 'Ensure all required exterior and interior photos are uploaded', 'order' => 2, 'is_required' => true],
            ['name' => 'Price Verification', 'description' => 'Verify pricing is competitive and accurate', 'order' => 3, 'is_required' => true],
            ['name' => 'Vehicle Description', 'description' => 'Ensure description is appealing and highlights key features', 'order' => 4, 'is_required' => true],
            ['name' => 'Carfax Attached', 'description' => 'Make sure Carfax report is attached to the listing', 'order' => 5, 'is_required' => true],
            ['name' => 'Reconditioning Complete', 'description' => 'Verify all reconditioning work is complete', 'order' => 6, 'is_required' => true],
            ['name' => 'Detail Complete', 'description' => 'Ensure the vehicle has been detailed', 'order' => 7, 'is_required' => true],
            ['name' => 'Keys and Remotes', 'description' => 'Verify all keys and remotes are accounted for', 'order' => 8, 'is_required' => true],
            ['name' => 'Documentation Complete', 'description' => 'All paperwork, manuals, and registration documents are in order', 'order' => 9, 'is_required' => true],
            ['name' => 'Final Quality Check', 'description' => 'Perform final inspection to ensure vehicle is frontline ready', 'order' => 10, 'is_required' => true],
        ];
        
        return view('checklists.create', compact('vehicle', 'defaultItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create checklists');
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.order' => 'required|integer',
            'items.*.is_required' => 'required|boolean',
        ]);
        
        // Check if vehicle already has a checklist
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        
        if ($vehicle->readyToPostChecklist) {
            return redirect()->route('checklists.edit', $vehicle->readyToPostChecklist)
                ->with('warning', 'Vehicle already has a checklist. You can edit it here.');
        }
        
        DB::beginTransaction();
        
        try {
            // Create the checklist
            $checklist = ReadyToPostChecklist::create([
                'vehicle_id' => $validated['vehicle_id'],
                'notes' => $validated['notes'],
                'is_complete' => false,
            ]);
            
            // Create checklist items
            foreach ($validated['items'] as $item) {
                ChecklistItem::create([
                    'ready_to_post_checklist_id' => $checklist->id,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'order' => $item['order'],
                    'is_required' => $item['is_required'],
                    'is_completed' => false,
                ]);
            }
            
            DB::commit();
            
            // Record in vehicle timeline
            $vehicle->recordTimelineEvent('checklist_created', null, null, 'Ready-to-post checklist created');
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the checklist: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ReadyToPostChecklist $checklist)
    {
        $this->authorize('view checklists');
        
        $checklist->load('vehicle', 'completedByUser', 'items.completedByUser');
        
        return view('checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReadyToPostChecklist $checklist)
    {
        $this->authorize('edit tags');
        
        $checklist->load('vehicle', 'items');
        
        return view('checklists.edit', compact('checklist'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReadyToPostChecklist $checklist)
    {
        $this->authorize('edit tags');
        
        $validated = $request->validate([
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:checklist_items,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.order' => 'required|integer',
            'items.*.is_required' => 'required|boolean',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Update the checklist
            $checklist->update([
                'notes' => $validated['notes'],
            ]);
            
            // Track existing and new item IDs
            $existingItemIds = [];
            
            // Update or create checklist items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = ChecklistItem::find($itemData['id']);
                    $item->update([
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'order' => $itemData['order'],
                        'is_required' => $itemData['is_required'],
                    ]);
                    
                    $existingItemIds[] = $item->id;
                } else {
                    // Create new item
                    $item = ChecklistItem::create([
                        'ready_to_post_checklist_id' => $checklist->id,
                        'name' => $itemData['name'],
                        'description' => $itemData['description'] ?? null,
                        'order' => $itemData['order'],
                        'is_required' => $itemData['is_required'],
                        'is_completed' => false,
                    ]);
                    
                    $existingItemIds[] = $item->id;
                }
            }
            
            // Delete items that were removed
            ChecklistItem::where('ready_to_post_checklist_id', $checklist->id)
                ->whereNotIn('id', $existingItemIds)
                ->delete();
            
            DB::commit();
            
            return redirect()->route('checklists.show', $checklist)
                ->with('success', 'Checklist updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the checklist: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReadyToPostChecklist $checklist)
    {
        $this->authorize('delete tags');
        
        // Prevent deletion of completed checklists
        if ($checklist->is_complete) {
            return redirect()->route('checklists.index')
                ->with('error', 'Completed checklists cannot be deleted.');
        }
        
        $vehicle = $checklist->vehicle;
        
        DB::beginTransaction();
        
        try {
            // Delete all items first
            $checklist->items()->delete();
            
            // Delete the checklist
            $checklist->delete();
            
            DB::commit();
            
            // Record in vehicle timeline
            $vehicle->recordTimelineEvent('checklist_deleted', null, null, 'Ready-to-post checklist deleted');
            
            return redirect()->route('checklists.index')
                ->with('success', 'Checklist deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the checklist: ' . $e->getMessage());
        }
    }
    
    /**
     * Update an item's completion status.
     */
    public function updateItemStatus(Request $request, ChecklistItem $item)
    {
        $this->authorize('complete checklists');
        
        $validated = $request->validate([
            'is_completed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);
        
        if ($validated['is_completed']) {
            $item->markAsComplete(Auth::id(), $validated['notes']);
        } else {
            $item->update([
                'is_completed' => false,
                'completed_at' => null,
                'completed_by' => null,
                'notes' => $validated['notes'],
            ]);
        }
        
        // Check if all required items are complete to see if the checklist can be marked complete
        $canBeCompleted = $item->checklist->canBeCompleted();
        
        return response()->json([
            'success' => true,
            'message' => $validated['is_completed'] ? 'Item marked as complete.' : 'Item marked as incomplete.',
            'item' => $item->refresh()->load('completedByUser'),
            'canBeCompleted' => $canBeCompleted,
        ]);
    }
    
    /**
     * Mark a checklist as complete.
     */
    public function markAsComplete(Request $request, ReadyToPostChecklist $checklist)
    {
        $this->authorize('complete checklists');
        
        if (!$checklist->canBeCompleted()) {
            return redirect()->route('checklists.show', $checklist)
                ->with('error', 'All required items must be completed first.');
        }
        
        $checklist->markAsComplete(Auth::id());
        
        // Update vehicle status
        $vehicle = $checklist->vehicle;
        $vehicle->update([
            'is_frontline_ready' => true,
        ]);
        
        // Record in vehicle timeline
        $vehicle->recordTimelineEvent('checklist_completed', null, null, 'Ready-to-post checklist completed');
        
        return redirect()->route('checklists.show', $checklist)
            ->with('success', 'Checklist marked as complete and vehicle is now frontline ready.');
    }
}
