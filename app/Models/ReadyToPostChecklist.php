<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReadyToPostChecklist extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'is_complete',
        'completed_at',
        'completed_by',
        'notes'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_complete' => 'boolean',
        'completed_at' => 'datetime',
    ];
    
    /**
     * Get the vehicle this checklist belongs to.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the user who completed this checklist.
     */
    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
    
    /**
     * Get the checklist items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }
    
    /**
     * Check if all required items are completed.
     */
    public function canBeCompleted(): bool
    {
        $requiredItemsCount = $this->items()->where('is_required', true)->count();
        $completedRequiredItemsCount = $this->items()->where('is_required', true)->where('is_completed', true)->count();
        
        return $requiredItemsCount === $completedRequiredItemsCount;
    }
    
    /**
     * Mark the checklist as complete.
     */
    public function markAsComplete(int $userId): bool
    {
        if (!$this->canBeCompleted()) {
            return false;
        }
        
        $this->is_complete = true;
        $this->completed_at = now();
        $this->completed_by = $userId;
        
        return $this->save();
    }
}
