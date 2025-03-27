<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'is_system',
        'order'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
        'order' => 'integer',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($tag) {
            // Auto-generate slug if not provided
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
    
    /**
     * Get all vehicles with this tag.
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_tags');
    }
}
