<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
     protected $fillable = [
        'user_id',
        'is_checked_in',
        'checked_in_at',
        'checked_out_at',
        'took_turn_at'
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}


}
