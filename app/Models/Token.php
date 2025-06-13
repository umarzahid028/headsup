<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Token extends Model
{
  

    protected $fillable = [
        'user_id',
        'serial_number',
        'customer_name',
        'status',
        'assigned_at',
        'completed_at',
        // other fillable columns...
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // agar relationship hai salesperson se
    public function salesperson()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
