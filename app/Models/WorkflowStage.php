<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStage extends Model
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
        'is_required',
        'target_days',
        'icon',
        'color',
    ];
    
    /**
     * Get the tasks for this stage.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
