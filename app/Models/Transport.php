<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'transporter_id',
        'origin',
        'destination',
        'pickup_date',
        'delivery_date',
        'status',
        'transporter_name',
        'transporter_phone',
        'transporter_email',
        'notes',
        'batch_id',
        'gate_pass_path',
        'qr_code_path',
        'batch_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pickup_date' => 'date',
        'delivery_date' => 'date',
    ];

    /**
     * Get the vehicle associated with the transport.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the transporter associated with the transport.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Check if a gate pass has been uploaded
     */
    public function hasGatePass(): bool
    {
        return !empty($this->gate_pass_path);
    }

    /**
     * Get the gate pass URL if available
     */
    public function getGatePassUrl(): ?string
    {
        if ($this->hasGatePass()) {
            return asset('storage/' . $this->gate_pass_path);
        }

        return null;
    }

    /**
     * Get the QR code URL if available
     */
    public function getQrCodeUrl(): ?string
    {
        if (!empty($this->qr_code_path)) {
            return asset('storage/' . $this->qr_code_path);
        }

        return null;
    }

    /**
     * Get all transports for a specific batch
     */
    public static function getByBatchId(string $batchId)
    {
        return self::where('batch_id', $batchId)->with('vehicle')->get();
    }
} 