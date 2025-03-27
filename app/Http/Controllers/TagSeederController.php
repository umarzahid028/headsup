<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagSeederController extends Controller
{
    /**
     * Create default system tags.
     */
    public function seedTags()
    {
        $this->authorize('create tags');
        
        DB::beginTransaction();
        
        try {
            // System tags for stages
            $stageTags = [
                ['name' => 'New Arrival', 'color' => '#4299e1', 'description' => 'Recently arrived vehicle', 'order' => 10],
                ['name' => 'In Arbitration', 'color' => '#ed8936', 'description' => 'Vehicle with pending arbitration issues', 'order' => 20],
                ['name' => 'In Recon', 'color' => '#9f7aea', 'description' => 'Vehicle undergoing reconditioning', 'order' => 30],
                ['name' => 'Ready for Sale', 'color' => '#48bb78', 'description' => 'Vehicle ready to be sold', 'order' => 40],
                ['name' => 'On Hold', 'color' => '#e53e3e', 'description' => 'Vehicle temporarily unavailable for sale', 'order' => 50],
            ];
            
            // System tags for conditions
            $conditionTags = [
                ['name' => 'Excellent', 'color' => '#38a169', 'description' => 'Vehicle in excellent condition', 'order' => 110],
                ['name' => 'Good', 'color' => '#68d391', 'description' => 'Vehicle in good condition', 'order' => 120],
                ['name' => 'Fair', 'color' => '#f6e05e', 'description' => 'Vehicle in fair condition', 'order' => 130],
                ['name' => 'Poor', 'color' => '#f6ad55', 'description' => 'Vehicle in poor condition', 'order' => 140],
                ['name' => 'Salvage', 'color' => '#fc8181', 'description' => 'Vehicle with salvage title', 'order' => 150],
            ];
            
            // System tags for special features
            $featureTags = [
                ['name' => 'Leather', 'color' => '#805ad5', 'description' => 'Vehicle with leather seats', 'order' => 210],
                ['name' => 'Sunroof', 'color' => '#667eea', 'description' => 'Vehicle with sunroof', 'order' => 220],
                ['name' => 'Navigation', 'color' => '#4fd1c5', 'description' => 'Vehicle with navigation system', 'order' => 230],
                ['name' => 'Backup Camera', 'color' => '#4fd1c5', 'description' => 'Vehicle with backup camera', 'order' => 240],
                ['name' => 'Third Row', 'color' => '#4fd1c5', 'description' => 'Vehicle with third row seating', 'order' => 250],
                ['name' => 'AWD/4WD', 'color' => '#b794f4', 'description' => 'Vehicle with all-wheel drive or four-wheel drive', 'order' => 260],
                ['name' => 'Bluetooth', 'color' => '#63b3ed', 'description' => 'Vehicle with Bluetooth connectivity', 'order' => 270],
                ['name' => 'Low Miles', 'color' => '#48bb78', 'description' => 'Vehicle with low mileage', 'order' => 280],
            ];
            
            // System tags for internal flags
            $flagTags = [
                ['name' => 'Need Photos', 'color' => '#e53e3e', 'description' => 'Vehicle needs photos', 'order' => 310],
                ['name' => 'Need Detailing', 'color' => '#e53e3e', 'description' => 'Vehicle needs detailing', 'order' => 320],
                ['name' => 'Need Service', 'color' => '#e53e3e', 'description' => 'Vehicle needs service', 'order' => 330],
                ['name' => 'Need Pricing', 'color' => '#e53e3e', 'description' => 'Vehicle needs pricing', 'order' => 340],
                ['name' => 'Front Line Ready', 'color' => '#38a169', 'description' => 'Vehicle ready for front line', 'order' => 350],
                ['name' => 'Featured', 'color' => '#d69e2e', 'description' => 'Featured vehicle', 'order' => 360],
                ['name' => 'Price Drop', 'color' => '#dd6b20', 'description' => 'Vehicle with recent price drop', 'order' => 370],
                ['name' => 'Hot Deal', 'color' => '#e53e3e', 'description' => 'Hot deal vehicle', 'order' => 380],
            ];
            
            $allTags = array_merge($stageTags, $conditionTags, $featureTags, $flagTags);
            
            $count = 0;
            
            foreach ($allTags as $tagData) {
                $slug = Str::slug($tagData['name']);
                
                // Check if tag already exists
                if (!Tag::where('slug', $slug)->exists()) {
                    Tag::create([
                        'name' => $tagData['name'],
                        'slug' => $slug,
                        'color' => $tagData['color'],
                        'description' => $tagData['description'],
                        'order' => $tagData['order'],
                        'is_system' => true,
                    ]);
                    
                    $count++;
                }
            }
            
            DB::commit();
            
            return redirect()->route('tags.index')
                ->with('success', $count . ' system tags created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('tags.index')
                ->with('error', 'An error occurred while creating system tags: ' . $e->getMessage());
        }
    }
}
