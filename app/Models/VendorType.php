<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_on_site',
        'has_system_access',
        'is_active'
    ];

    protected $casts = [
        'is_on_site' => 'boolean',
        'has_system_access' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
