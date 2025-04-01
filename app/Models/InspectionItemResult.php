<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionItemResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_inspection_id',
        'inspection_item_id',
        'status',
        'notes',
        'cost',
        'vendor_id',
        'requires_repair',
        'repair_completed',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'requires_repair' => 'boolean',
        'repair_completed' => 'boolean',
    ];

    /**
     * Get the vehicle inspection that owns this result.
     */
    public function vehicleInspection(): BelongsTo
    {
        return $this->belongsTo(VehicleInspection::class);
    }

    /**
     * Get the inspection item that this result is for.
     */
    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class);
    }

    /**
     * Get the vendor assigned to repair this item.
     */
    public function assignedVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the repair images for this result.
     */
    public function repairImages(): HasMany
    {
        return $this->hasMany(RepairImage::class);
    }

    /**
     * Get before repair images.
     */
    public function beforeImages()
    {
        return $this->repairImages()->where('image_type', 'before');
    }

    /**
     * Get after repair images.
     */
    public function afterImages()
    {
        return $this->repairImages()->where('image_type', 'after');
    }

    /**
     * Get documentation images.
     */
    public function documentationImages()
    {
        return $this->repairImages()->where('image_type', 'documentation');
    }

    /**
     * Determine if this item has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'fail';
    }

    /**
     * Determine if this item has passed.
     */
    public function hasPassed(): bool
    {
        return $this->status === 'pass';
    }
} 