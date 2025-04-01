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
        'origin',
        'destination',
        'pickup_date',
        'delivery_date',
        'status',
        'transporter_name',
        'transporter_phone',
        'transporter_email',
        'notes',
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
} 