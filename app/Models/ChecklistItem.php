<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ready_to_post_checklist_id',
        'name',
        'description',
        'is_completed',
        'completed_at',
        'completed_by',
        'notes',
        'order',
        'is_required'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_completed' => 'boolean',
        'is_required' => 'boolean',
        'completed_at' => 'datetime',
        'order' => 'integer',
    ];
    
    /**
     * Get the checklist this item belongs to.
     */
    public function checklist(): BelongsTo
    {
        return $this->belongsTo(ReadyToPostChecklist::class, 'ready_to_post_checklist_id');
    }
    
    /**
     * Get the user who completed this item.
     */
    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
    
    /**
     * Mark the item as complete.
     */
    public function markAsComplete(int $userId, ?string $notes = null): bool
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->completed_by = $userId;
        
        if ($notes) {
            $this->notes = $notes;
        }
        
        return $this->save();
    }
}
