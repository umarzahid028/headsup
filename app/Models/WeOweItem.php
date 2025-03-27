<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeOweItem extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'details',
        'description',
        'type',
        'cost',
        'status',
        'assigned_to',
        'vendor_id',
        'due_date',
        'completed_at',
        'has_waiver',
        'waiver_signed',
        'waiver_signed_at',
        'sms_sent',
        'sms_sent_at'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'waiver_signed_at' => 'datetime',
        'sms_sent_at' => 'datetime',
        'cost' => 'decimal:2',
        'has_waiver' => 'boolean',
        'waiver_signed' => 'boolean',
        'sms_sent' => 'boolean',
    ];
    
    /**
     * Get the vehicle that owns the we-owe item.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the user assigned to this we-owe item.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    
    /**
     * Get the vendor associated with this we-owe item.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    
    /**
     * Scope a query to only include pending we-owe items.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }
    
    /**
     * Scope a query to only include completed we-owe items.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
    
    /**
     * Scope a query to only include we-owe type items.
     */
    public function scopeWeOwe($query)
    {
        return $query->where('type', 'we_owe');
    }
    
    /**
     * Scope a query to only include goodwill type items.
     */
    public function scopeGoodwill($query)
    {
        return $query->where('type', 'goodwill');
    }
}
