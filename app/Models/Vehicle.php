<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

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
}
