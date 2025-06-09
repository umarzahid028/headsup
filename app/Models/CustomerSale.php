<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSale extends Model
{
      protected $fillable = [
        'name', 'email', 'phone', 'interest', 'notes', 'process', 'disposition'
    ];

    protected $casts = [
        'process' => 'array',
        'disposition' => 'array',
    ];
}
