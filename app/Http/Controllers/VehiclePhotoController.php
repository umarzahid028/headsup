<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VehiclePhotoController extends Controller
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
        $this->authorize('view photos');
        
        $vehicleId = $request->input('vehicle_id');
        
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            $photos = $vehicle->photos()->orderBy('order')->paginate(20);
            
            return view('photos.index', compact('vehicle', 'photos'));
        } else {
            return redirect()->route('vehicles.index')
                ->with('warning', 'Please select a vehicle to view photos.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('upload photos');
        
        $vehicleId = $request->input('vehicle_id');
        
        if ($vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            return view('photos.create', compact('vehicle'));
        } else {
            return redirect()->route('vehicles.index')
                ->with('warning', 'Please select a vehicle to upload photos.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('upload photos');
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'photos' => 'required|array',
            'photos.*' => 'required|image|max:10240', // 10MB max
            'type' => 'required|string|in:general,damage,repair,interior,exterior',
            'description' => 'nullable|string',
        ]);
        
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $photos = [];
        
        $maxOrder = $vehicle->photos()->max('order') ?? 0;
        
        foreach ($request->file('photos') as $index => $file) {
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('vehicle-photos/' . $vehicle->id, $fileName, 'public');
            
            // Create thumbnail using Intervention Image (requires installation)
            $thumbnailPath = 'vehicle-photos/' . $vehicle->id . '/thumbnails';
            $thumbnail = 'thumb_' . $fileName;
            
            if (!Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->makeDirectory($thumbnailPath);
            }
            
            // Open the image, resize it to fit within 300x300, and save it as a thumbnail
            $img = Image::make($file)->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            $img->save(storage_path('app/public/' . $thumbnailPath . '/' . $thumbnail));
            
            // Create the photo record
            $photo = VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'type' => $validated['type'],
                'description' => $validated['description'],
                'order' => $maxOrder + $index + 1,
                'uploaded_by' => Auth::id(),
            ]);
            
            // If this is the first photo, set it as primary
            if ($vehicle->photos()->count() === 1) {
                $photo->setAsPrimary();
            }
            
            $photos[] = $photo;
        }
        
        // Record in vehicle timeline
        $vehicle->recordTimelineEvent(
            'photos_uploaded',
            null,
            count($photos) . ' photos',
            count($photos) . ' photos uploaded of type ' . $validated['type']
        );
        
        return redirect()->route('photos.index', ['vehicle_id' => $vehicle->id])
            ->with('success', count($photos) . ' photos uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(VehiclePhoto $photo)
    {
        $this->authorize('view photos');
        
        $vehicle = $photo->vehicle;
        
        return view('photos.show', compact('photo', 'vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VehiclePhoto $photo)
    {
        $this->authorize('upload photos');
        
        $vehicle = $photo->vehicle;
        
        return view('photos.edit', compact('photo', 'vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VehiclePhoto $photo)
    {
        $this->authorize('upload photos');
        
        $validated = $request->validate([
            'type' => 'required|string|in:general,damage,repair,interior,exterior',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_primary' => 'nullable|boolean',
        ]);
        
        $photo->update($validated);
        
        // If marked as primary, update the vehicle's primary photo
        if ($request->has('is_primary') && $validated['is_primary']) {
            $photo->setAsPrimary();
        }
        
        return redirect()->route('photos.index', ['vehicle_id' => $photo->vehicle_id])
            ->with('success', 'Photo updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VehiclePhoto $photo)
    {
        $this->authorize('delete photos');
        
        $vehicleId = $photo->vehicle_id;
        $vehicle = $photo->vehicle;
        
        // Delete the file from storage
        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
            
            // Delete thumbnail if it exists
            $thumbnailPath = 'vehicle-photos/' . $vehicleId . '/thumbnails/thumb_' . $photo->file_name;
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
        }
        
        // If this was the primary photo, set another one as primary
        if ($photo->is_primary) {
            $nextPhoto = VehiclePhoto::where('vehicle_id', $vehicleId)
                ->where('id', '!=', $photo->id)
                ->orderBy('order')
                ->first();
                
            if ($nextPhoto) {
                $nextPhoto->setAsPrimary();
            }
        }
        
        // Record in vehicle timeline
        $vehicle->recordTimelineEvent(
            'photo_deleted',
            null,
            null,
            'Photo deleted: ' . ($photo->description ?? $photo->file_name)
        );
        
        // Delete the photo record
        $photo->delete();
        
        return redirect()->route('photos.index', ['vehicle_id' => $vehicleId])
            ->with('success', 'Photo deleted successfully.');
    }
    
    /**
     * Reorder photos.
     */
    public function reorder(Request $request)
    {
        $this->authorize('upload photos');
        
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'photos' => 'required|array',
            'photos.*' => 'required|exists:vehicle_photos,id',
        ]);
        
        foreach ($validated['photos'] as $index => $photoId) {
            VehiclePhoto::where('id', $photoId)->update(['order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Set a photo as primary.
     */
    public function setPrimary(Request $request, VehiclePhoto $photo)
    {
        $this->authorize('upload photos');
        
        $photo->setAsPrimary();
        
        return redirect()->route('photos.index', ['vehicle_id' => $photo->vehicle_id])
            ->with('success', 'Primary photo updated successfully.');
    }
}
