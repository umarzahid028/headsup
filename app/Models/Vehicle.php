<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Vehicle extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Add global scope to filter vehicles for transporters
        static::addGlobalScope('transporter_access', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Transporter')) {
                $builder->whereHas('transports', function ($query) {
                    $query->where('transporter_id', auth()->user()->transporter_id)
                          ->orWhereHas('batch', function ($q) {
                              $q->where('transporter_id', auth()->user()->transporter_id);
                          });
                });
            }
        });
    }

    protected $fillable = [
        'stock_number',
        'vin',
        'year',
        'make',
        'model',
        'trim',
        'date_in_stock',
        'odometer',
        'exterior_color',
        'interior_color',
        'number_of_leads',
        'status',
        'body_type',
        'drive_train',
        'engine',
        'fuel_type',
        'is_featured',
        'has_video',
        'number_of_pics',
        'purchased_from',
        'purchase_date',
        'transmission',
        'transmission_type',
        'vehicle_purchase_source',
        'advertising_price',
        'deal_status',
        'sold_date',
        'buyer_name',
        'import_file',
        'processed_at',
        'transport_status',
    ];

    protected $casts = [
        'date_in_stock' => 'date',
        'purchase_date' => 'date',
        'sold_date' => 'date',
        'is_featured' => 'boolean',
        'has_video' => 'boolean',
        'year' => 'integer',
        'odometer' => 'integer',
        'number_of_leads' => 'integer',
        'number_of_pics' => 'integer',
        'advertising_price' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the transports for the vehicle.
     */
    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class);
    }

    /**
     * Get the gate passes for the vehicle.
     */
    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }

    /**
     * Get the inspections for the vehicle.
     */
    public function vehicleInspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }
}
