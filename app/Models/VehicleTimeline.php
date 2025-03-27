<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTimeline extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'event_type',
        'previous_value',
        'new_value',
        'description',
        'user_id'
    ];
    
    /**
     * Get the vehicle this timeline entry belongs to.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the user who created this timeline entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get a formatted representation of the timeline event.
     */
    public function getFormattedEventAttribute(): string
    {
        $user = $this->user ? $this->user->name : 'System';
        
        switch ($this->event_type) {
            case 'status_change':
                return "{$user} changed status from {$this->previous_value} to {$this->new_value}";
            case 'tag_added':
                return "{$user} added tag: {$this->new_value}";
            case 'tag_removed':
                return "{$user} removed tag: {$this->previous_value}";
            case 'note_added':
                return "{$user} added a note";
            default:
                return $this->description ?: "{$user} performed action: {$this->event_type}";
        }
    }
}
