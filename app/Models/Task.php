<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'vehicle_id', 'workflow_stage_id', 'title', 'description', 'status',
        'assigned_to', 'vendor_id', 'is_internal', 'is_vendor_visible',
        'assigned_at', 'started_at', 'completed_at', 'due_date',
        'cost', 'cost_type', 'cost_notes', 'has_photos', 'photo_count',
        'requires_approval', 'approved_at', 'approved_by'
    ];
    
    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'due_date' => 'datetime',
        'approved_at' => 'datetime',
        'is_internal' => 'boolean',
        'is_vendor_visible' => 'boolean',
        'has_photos' => 'boolean',
        'requires_approval' => 'boolean',
        'cost' => 'decimal:2',
    ];
    
    /**
     * Get the vehicle that this task belongs to.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the workflow stage that this task belongs to.
     */
    public function workflowStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class);
    }
    
    /**
     * Get the user that this task is assigned to.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the vendor that this task is assigned to.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    
    /**
     * Get the user that approved this task.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get all documents attached to this task.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
    
    /**
     * Get all photos attached to this task.
     */
    public function photos()
    {
        return $this->documents()->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }
    
    /**
     * Calculate days since assignment.
     */
    public function daysInProgress()
    {
        if (!$this->assigned_at) {
            return 0;
        }
        
        $endDate = $this->completed_at ?? now();
        return $this->assigned_at->diffInDays($endDate);
    }
    
    /**
     * Determine if the task is overdue.
     */
    public function isOverdue()
    {
        if (!$this->due_date || in_array($this->status, ['completed', 'pass'])) {
            return false;
        }
        
        return now()->greaterThan($this->due_date);
    }
    
    /**
     * Start this task.
     */
    public function start()
    {
        if (!$this->started_at) {
            $this->started_at = now();
            $this->status = 'pending';
            $this->save();
        }
    }
    
    /**
     * Complete this task.
     */
    public function complete()
    {
        $this->completed_at = now();
        $this->status = 'completed';
        $this->save();
    }
}
