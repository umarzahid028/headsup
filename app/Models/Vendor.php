<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type', // dealer, auction, private, transportation
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'notes',
        'is_active',
        'website',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the vehicles purchased from this vendor.
     */
    public function purchasedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'purchased_from');
    }
    
    /**
     * Get tasks assigned to this vendor.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    
    /**
     * Scope a query to only include transportation vendors.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTransporters($query)
    {
        return $query->where('type', 'transportation');
    }
    
    /**
     * Scope a query to only include dealers and auctions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePurchaseLocations($query)
    {
        return $query->whereIn('type', ['dealer', 'auction']);
    }
}
