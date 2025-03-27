<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Store a new document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'documentable_id' => 'required|integer',
            'documentable_type' => 'required|string',
        ]);
        
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('documents', 'public');
            
            $document = Document::create([
                'name' => $request->name,
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'type' => $request->type,
                'documentable_id' => $request->documentable_id,
                'documentable_type' => $request->documentable_type,
            ]);
            
            return response()->json([
                'success' => true,
                'document' => $document,
                'message' => 'Document uploaded successfully.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to upload document.'
        ], 400);
    }
    
    /**
     * Download a document.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->path)) {
            abort(404, 'Document not found');
        }
        
        return Storage::disk('public')->download(
            $document->path, 
            $document->name . '.' . pathinfo($document->path, PATHINFO_EXTENSION)
        );
    }
    
    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        if (Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }
        
        $document->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully.'
        ]);
    }
}
