<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TimeBasedAlert extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'alert_type',
        'alertable_id',
        'alertable_type',
        'warning_threshold',
        'critical_threshold',
        'is_active',
        'triggered_at',
        'resolved_at',
        'created_by',
        'notes'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'warning_threshold' => 'integer',
        'critical_threshold' => 'integer',
        'is_active' => 'boolean',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
    
    /**
     * Get the alertable entity.
     */
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the user who created this alert.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Resolve this alert.
     */
    public function resolve(): bool
    {
        $this->is_active = false;
        $this->resolved_at = now();
        return $this->save();
    }
    
    /**
     * Get the current status of this alert.
     * Returns: 'green', 'yellow', 'red'
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active || $this->resolved_at) {
            return 'green';
        }
        
        $hours = 0;
        
        // Different calculation based on alert type
        if ($this->alert_type === 'vehicle_stage' && $this->alertable instanceof Vehicle) {
            $hours = $this->alertable->stage_updated_at ? $this->alertable->stage_updated_at->diffInHours(now()) : 0;
        } elseif ($this->alert_type === 'task_deadline' && $this->alertable instanceof Task) {
            $hours = $this->alertable->due_date ? $this->alertable->due_date->diffInHours(now(), false) : 0;
        } else {
            $hours = $this->triggered_at ? $this->triggered_at->diffInHours(now()) : 0;
        }
        
        if ($hours >= $this->critical_threshold) {
            return 'red';
        } elseif ($hours >= $this->warning_threshold) {
            return 'yellow';
        } else {
            return 'green';
        }
    }
    
    /**
     * Check alert status and trigger notification if needed.
     */
    public function checkStatus()
    {
        $currentStatus = $this->getStatusAttribute();
        
        if ($currentStatus === 'yellow' && !$this->triggered_at) {
            $this->triggered_at = now();
            $this->save();
            
            // Trigger warning notification here
        } elseif ($currentStatus === 'red' && $this->triggered_at && $this->triggered_at->diffInHours(now()) >= ($this->critical_threshold - $this->warning_threshold)) {
            // Trigger critical notification here
        }
    }
}
