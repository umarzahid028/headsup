<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class VehiclePhoto extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'type',
        'description',
        'order',
        'is_primary',
        'uploaded_by'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
        'is_primary' => 'boolean',
    ];
    
    /**
     * Get the vehicle this photo belongs to.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the user who uploaded this photo.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    
    /**
     * Get the photo's URL.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
    
    /**
     * Set as primary photo for the vehicle.
     */
    public function setAsPrimary(): bool
    {
        // First reset all other photos
        self::where('vehicle_id', $this->vehicle_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
            
        // Then set this one as primary
        $this->is_primary = true;
        return $this->save();
    }
}
