<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReconWorkflow extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'status',
        'started_by',
        'completed_by',
        'total_cost',
        'total_items',
        'completed_items',
        'started_at',
        'completed_at',
        'notes',
        'has_arbitration_issues',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_cost' => 'decimal:2',
        'total_items' => 'integer',
        'completed_items' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'has_arbitration_issues' => 'boolean',
    ];

    /**
     * Get the vehicle that this workflow is for.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who started this workflow.
     */
    public function startedBy()
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    /**
     * Get the user who completed this workflow.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the inspection items for this workflow.
     */
    public function inspectionItems()
    {
        return $this->hasMany(InspectionItem::class, 'recon_workflow_id');
    }

    /**
     * Calculate the progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->total_items > 0) {
            return round(($this->completed_items / $this->total_items) * 100);
        }
        
        return 0;
    }

    /**
     * Calculate the pending items count.
     */
    public function getPendingItemsCountAttribute()
    {
        return $this->total_items - $this->completed_items;
    }

    /**
     * Scope a query to only include in progress workflows.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed workflows.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include on hold workflows.
     */
    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    /**
     * Scope a query to only include workflows with arbitration issues.
     */
    public function scopeWithArbitrationIssues($query)
    {
        return $query->where('has_arbitration_issues', true);
    }

    /**
     * Mark the workflow as completed and the vehicle as frontline ready.
     */
    public function markAsCompleted(int $userId)
    {
        // Update workflow status
        $this->status = 'completed';
        $this->completed_by = $userId;
        $this->completed_at = now();
        $this->completed_items = $this->total_items;
        
        // Mark vehicle as frontline ready
        if ($this->vehicle) {
            $this->vehicle->update([
                'is_frontline_ready' => true,
                'current_stage' => 'frontline',
                'stage_updated_at' => now()
            ]);
        }
        
        return $this->save();
    }
}
