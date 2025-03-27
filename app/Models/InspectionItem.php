<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'status',
        'cost',
        'is_vendor_visible',
        'is_completed',
        'vehicle_id',
        'recon_workflow_id',
        'assigned_to',
        'completed_by',
        'assigned_at',
        'completed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'is_vendor_visible' => 'boolean',
        'is_completed' => 'boolean',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the category that this inspection item belongs to.
     */
    public function category()
    {
        return $this->belongsTo(InspectionCategory::class, 'category_id');
    }

    /**
     * Get the vehicle that this inspection item is for.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the recon workflow that this inspection item belongs to.
     */
    public function reconWorkflow()
    {
        return $this->belongsTo(ReconWorkflow::class);
    }

    /**
     * Get the vendor that this inspection item is assigned to.
     */
    public function assignedVendor()
    {
        return $this->belongsTo(Vendor::class, 'assigned_to');
    }

    /**
     * Get the user who completed this inspection item.
     */
    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the photos for this inspection item.
     */
    public function photos()
    {
        return $this->hasMany(InspectionPhoto::class);
    }

    /**
     * Get the assignments for this inspection item.
     */
    public function assignments()
    {
        return $this->hasMany(InspectionAssignment::class);
    }

    /**
     * Scope a query to only include pending items.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include passed items.
     */
    public function scopePassed($query)
    {
        return $query->where('status', 'pass');
    }

    /**
     * Scope a query to only include items that need repair.
     */
    public function scopeNeedsRepair($query)
    {
        return $query->where('status', 'repair');
    }

    /**
     * Scope a query to only include items that need replacement.
     */
    public function scopeNeedsReplacement($query)
    {
        return $query->where('status', 'replace');
    }

    /**
     * Scope a query to only include completed items.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope a query to only include uncompleted items.
     */
    public function scopeUncompleted($query)
    {
        return $query->where('is_completed', false);
    }
}
