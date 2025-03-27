<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class GoodwillRepair extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'assigned_to',
        'vendor_id',
        'title',
        'description',
        'cost',
        'status',
        'due_date',
        'completed_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'waiver_signed',
        'waiver_signed_at',
        'signature_data',
        'signature_ip',
        'waiver_pdf_path',
        'sms_sent',
        'sms_sent_at',
        'sms_status',
        'email_sent',
        'email_sent_at',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost' => 'decimal:2',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'waiver_signed' => 'boolean',
        'waiver_signed_at' => 'datetime',
        'sms_sent' => 'boolean',
        'sms_sent_at' => 'datetime',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    /**
     * Get the vehicle associated with this goodwill repair.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user assigned to this goodwill repair.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the vendor associated with this goodwill repair.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who created this goodwill repair.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the waiver PDF URL if it exists.
     */
    public function getWaiverPdfUrlAttribute()
    {
        if ($this->waiver_pdf_path) {
            return Storage::url($this->waiver_pdf_path);
        }
        
        return null;
    }

    /**
     * Check if the repair is pending signature.
     */
    public function isPendingSignature()
    {
        return !$this->waiver_signed;
    }

    /**
     * Generate a unique signature token for this repair.
     */
    public function generateSignatureToken()
    {
        return md5($this->id . $this->vehicle_id . $this->created_at);
    }

    /**
     * Scope a query to only include pending repairs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in progress repairs.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope a query to only include completed repairs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Mark this repair as complete.
     */
    public function markComplete()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        return $this->save();
    }
}
