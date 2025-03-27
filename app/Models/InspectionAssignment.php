<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionAssignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'inspection_item_id',
        'vendor_id',
        'status',
        'quoted_cost',
        'final_cost',
        'due_date',
        'assigned_by',
        'completed_by',
        'assigned_at',
        'accepted_at',
        'completed_at',
        'vendor_notes',
        'internal_notes',
        'is_internal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quoted_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
        'due_date' => 'datetime',
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_internal' => 'boolean',
    ];

    /**
     * Get the inspection item that this assignment belongs to.
     */
    public function inspectionItem()
    {
        return $this->belongsTo(InspectionItem::class);
    }

    /**
     * Get the vendor that this assignment is for.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who assigned this assignment.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who completed this assignment.
     */
    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope a query to only include pending assignments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in progress assignments.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed assignments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include rejected assignments.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include internal assignments.
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope a query to only include external assignments.
     */
    public function scopeExternal($query)
    {
        return $query->where('is_internal', false);
    }
}
