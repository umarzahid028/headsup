<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleTag extends Pivot
{
    use HasFactory;
    
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'tag_id',
        'created_by'
    ];
    
    /**
     * Get the user who created this tag association.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the vehicle.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    
    /**
     * Get the tag.
     */
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
