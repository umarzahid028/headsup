<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionPhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inspection_item_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'thumbnail_path',
        'caption',
        'uploaded_by',
        'is_vendor_visible',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'is_vendor_visible' => 'boolean',
    ];

    /**
     * Get the inspection item that this photo belongs to.
     */
    public function inspectionItem()
    {
        return $this->belongsTo(InspectionItem::class);
    }

    /**
     * Get the user who uploaded this photo.
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the URL for this photo.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get the thumbnail URL for this photo.
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_path) {
            return asset('storage/' . $this->thumbnail_path);
        }
        
        return $this->url;
    }

    /**
     * Scope a query to only include vendor visible photos.
     */
    public function scopeVendorVisible($query)
    {
        return $query->where('is_vendor_visible', true);
    }
}
