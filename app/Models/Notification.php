<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'notifiable_id',
        'notifiable_type',
        'is_read',
        'read_at',
        'action_url',
        'icon',
        'color'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];
    
    /**
     * Get the user this notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the notifiable entity.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Mark the notification as read.
     */
    public function markAsRead(): bool
    {
        $this->is_read = true;
        $this->read_at = now();
        return $this->save();
    }
    
    /**
     * Create a notification for a vehicle entering a user's bucket.
     */
    public static function createBucketNotification(Vehicle $vehicle, User $user, string $bucketName): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'vehicle_bucket',
            'message' => "Vehicle {$vehicle->stock_number} ({$vehicle->year} {$vehicle->make} {$vehicle->model}) has entered your {$bucketName} bucket",
            'notifiable_id' => $vehicle->id,
            'notifiable_type' => get_class($vehicle),
            'is_read' => false,
            'action_url' => route('vehicles.show', $vehicle),
            'icon' => 'car',
            'color' => 'blue'
        ]);
    }
    
    /**
     * Create a notification for a status change.
     */
    public static function createStatusNotification(Vehicle $vehicle, User $user, string $oldStatus, string $newStatus): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => 'status_change',
            'message' => "Vehicle {$vehicle->stock_number} status changed from {$oldStatus} to {$newStatus}",
            'notifiable_id' => $vehicle->id,
            'notifiable_type' => get_class($vehicle),
            'is_read' => false,
            'action_url' => route('vehicles.show', $vehicle),
            'icon' => 'status',
            'color' => 'green'
        ]);
    }
    
    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
