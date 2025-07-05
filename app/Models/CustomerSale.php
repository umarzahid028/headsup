<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSale extends Model
{
    protected $table = "customer_sales";
protected $fillable = [
    'user_id', 'name', 'email', 'phone', 'interest', 'notes', 'process', 'disposition','ended_at',  'appointment_id', 'forwarded_to_manager','forwarded_at'
];

public function user()
{
    return $this->belongsTo(User::class, 'user_id');

}


public function queue()
{
    return $this->belongsTo(Queue::class);
}

public function appointment()
{
    return $this->belongsTo(Appointment::class);
}



    protected $casts = [
        'process' => 'array',
    ];
}
