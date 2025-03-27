<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vin', 'make', 'model', 'year', 'stock_number', 'color', 'trim', 'mileage',
        'purchased_from', 'purchase_location', 'purchase_price', 'purchase_date', 
        'is_arbitrable', 'current_stage', 'stage_updated_at', 'is_frontline_ready',
        'is_archived', 'is_sold', 'transporter_id', 'transport_assigned_at',
        'transport_expected_at', 'check_in_date'
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'stage_updated_at' => 'datetime',
        'transport_assigned_at' => 'datetime',
        'transport_expected_at' => 'datetime',
        'check_in_date' => 'datetime',
        'is_arbitrable' => 'boolean',
        'is_frontline_ready' => 'boolean',
        'is_archived' => 'boolean',
        'is_sold' => 'boolean',
        'purchase_price' => 'decimal:2',
    ];

    /**
     * Get the transporter assigned to this vehicle.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transporter_id');
    }

    /**
     * Get all tasks for this vehicle.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all we-owe items for this vehicle.
     */
    public function weOweItems(): HasMany
    {
        return $this->hasMany(WeOweItem::class);
    }

    /**
     * Get all goodwill repairs for this vehicle.
     */
    public function goodwillRepairs(): HasMany
    {
        return $this->hasMany(GoodwillRepair::class);
    }

    /**
     * Get all documents attached to this vehicle.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get active tasks for the current workflow stage.
     */
    public function currentStageTasks()
    {
        return $this->tasks()
            ->whereHas('workflowStage', function ($query) {
                $query->where('slug', $this->current_stage);
            })
            ->where('status', '!=', 'completed');
    }

    /**
     * Get tasks for a specific workflow stage.
     */
    public function stageTasksCompleted($stage)
    {
        return $this->tasks()
            ->whereHas('workflowStage', function ($query) use ($stage) {
                $query->where('slug', $stage);
            })
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Calculate days in current stage.
     */
    public function daysInCurrentStage()
    {
        if (!$this->stage_updated_at) {
            return 0;
        }
        
        return $this->stage_updated_at->diffInDays(now());
    }

    /**
     * Get all release forms for this vehicle.
     */
    public function releaseForms()
    {
        return $this->documents()->where('type', 'release_form');
    }

    /**
     * Get all gate passes for this vehicle.
     */
    public function gatePasses()
    {
        return $this->documents()->where('type', 'gate_pass');
    }

    /**
     * Get all tags for this vehicle.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'vehicle_tags')->withTimestamps();
    }
    
    /**
     * Get timeline entries for this vehicle.
     */
    public function timeline()
    {
        return $this->hasMany(VehicleTimeline::class)->orderByDesc('created_at');
    }
    
    /**
     * Get photos for this vehicle.
     */
    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('order');
    }
    
    /**
     * Get the ready-to-post checklist for this vehicle.
     */
    public function readyToPostChecklist()
    {
        return $this->hasOne(ReadyToPostChecklist::class);
    }
    
    /**
     * Get time-based alerts for this vehicle.
     */
    public function timeBasedAlerts()
    {
        return $this->morphMany(TimeBasedAlert::class, 'alertable');
    }
    
    /**
     * Get the active alert status for this vehicle.
     * Returns: 'green', 'yellow', 'red' based on time thresholds
     */
    public function getAlertStatusAttribute()
    {
        $alert = $this->timeBasedAlerts()
            ->where('is_active', true)
            ->whereNull('resolved_at')
            ->first();
            
        if (!$alert) {
            return 'green';
        }
        
        $hoursInCurrentState = $this->stage_updated_at ? $this->stage_updated_at->diffInHours(now()) : 0;
        
        if ($hoursInCurrentState >= $alert->critical_threshold) {
            return 'red';
        } elseif ($hoursInCurrentState >= $alert->warning_threshold) {
            return 'yellow';
        } else {
            return 'green';
        }
    }
    
    /**
     * Record a timeline event for this vehicle.
     */
    public function recordTimelineEvent($eventType, $previousValue = null, $newValue = null, $description = null, $userId = null)
    {
        return $this->timeline()->create([
            'event_type' => $eventType,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'description' => $description,
            'user_id' => $userId ?: auth()->id()
        ]);
    }
    
    /**
     * Generate a barcode for this vehicle's VIN.
     */
    public function generateBarcode()
    {
        // In a real implementation, this would generate and store a barcode image
        $this->barcode = $this->vin;
        $this->barcode_generated_at = now();
        $this->barcode_image_path = 'barcodes/' . $this->vin . '.png';
        $this->save();
        
        return $this->barcode_image_path;
    }
}
