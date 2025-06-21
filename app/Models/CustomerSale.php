<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSale extends Model
{
protected $fillable = [
    'user_id', 'name', 'email', 'phone', 'interest', 'notes', 'process', 'disposition','ended_at'
];

public function user()
{
    return $this->belongsTo(User::class);
}




    protected $casts = [
        'process' => 'array',
    ];
}
