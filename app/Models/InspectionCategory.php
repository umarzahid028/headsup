<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'requires_photos',
        'requires_vendor',
        'requires_cost',
        'icon',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'requires_photos' => 'boolean',
        'requires_vendor' => 'boolean',
        'requires_cost' => 'boolean',
    ];

    /**
     * Get the inspection items for this category.
     */
    public function inspectionItems()
    {
        return $this->hasMany(InspectionItem::class, 'category_id');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order categories by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
