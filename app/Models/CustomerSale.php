<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSale extends Model
{
protected $fillable = [
    'user_id', 'name', 'email', 'phone', 'interest', 'notes', 'process', 'disposition','served_duration	'
];

public function user()
{
    return $this->belongsTo(User::class);
}




    protected $casts = [
        'process' => 'array',
    ];
}
