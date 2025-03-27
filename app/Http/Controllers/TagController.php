<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
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
        $this->authorize('view tags');
        
        $tags = Tag::query();
        
        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $tags->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }
        
        $tags = $tags->orderBy('order', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();
        
        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create tags');
        
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create tags');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_system' => 'nullable|boolean',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Set default color if not provided
        if (empty($validated['color'])) {
            $validated['color'] = '#3490dc';
        }
        
        $tag = Tag::create($validated);
        
        return redirect()->route('tags.index')
            ->with('success', 'Tag created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        $this->authorize('view tags');
        
        $vehicles = $tag->vehicles()->paginate(10);
        
        return view('tags.show', compact('tag', 'vehicles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        $this->authorize('edit tags');
        
        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $this->authorize('edit tags');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_system' => 'nullable|boolean',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $tag->update($validated);
        
        return redirect()->route('tags.index')
            ->with('success', 'Tag updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag)
    {
        $this->authorize('delete tags');
        
        // Prevent deletion of system tags
        if ($tag->is_system) {
            return redirect()->route('tags.index')
                ->with('error', 'System tags cannot be deleted.');
        }
        
        $tag->delete();
        
        return redirect()->route('tags.index')
            ->with('success', 'Tag deleted successfully.');
    }
    
    /**
     * Attach a tag to a vehicle.
     */
    public function attachToVehicle(Request $request)
    {
        $this->authorize('assign tags');
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'tag_id' => 'required|exists:tags,id',
        ]);
        
        $vehicle = \App\Models\Vehicle::find($validated['vehicle_id']);
        $tag = Tag::find($validated['tag_id']);
        
        // Attach tag if not already attached
        if (!$vehicle->tags()->where('tag_id', $tag->id)->exists()) {
            $vehicle->tags()->attach($tag->id, [
                'created_by' => Auth::id()
            ]);
            
            // Record in timeline
            $vehicle->recordTimelineEvent('tag_added', null, $tag->name);
            
            return response()->json([
                'success' => true,
                'message' => "Tag '{$tag->name}' attached successfully."
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => "Vehicle already has tag '{$tag->name}'."
        ]);
    }
    
    /**
     * Detach a tag from a vehicle.
     */
    public function detachFromVehicle(Request $request)
    {
        $this->authorize('assign tags');
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'tag_id' => 'required|exists:tags,id',
        ]);
        
        $vehicle = \App\Models\Vehicle::find($validated['vehicle_id']);
        $tag = Tag::find($validated['tag_id']);
        
        if ($vehicle->tags()->where('tag_id', $tag->id)->exists()) {
            $vehicle->tags()->detach($tag->id);
            
            // Record in timeline
            $vehicle->recordTimelineEvent('tag_removed', $tag->name, null);
            
            return response()->json([
                'success' => true,
                'message' => "Tag '{$tag->name}' removed successfully."
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => "Vehicle does not have tag '{$tag->name}'."
        ]);
    }
}
