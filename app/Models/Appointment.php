<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
     protected $fillable = [
        'created_by', 'salesperson_id', 'customer_name', 'customer_phone', 'date', 'time', 'status', 'notes'
    ];

    public function manager() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function salesperson() {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

public function queue()
{
    return $this->belongsTo(Queue::class, 'queue_id'); // optional if linked
}

public function customerSales()
{
    return $this->hasMany(CustomerSale::class, 'appointment_id');
}

}
