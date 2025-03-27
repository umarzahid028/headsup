<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'path',
        'mime_type',
        'size',
        'type', // release_form, gate_pass, etc.
        'documentable_id',
        'documentable_type',
    ];
    
    /**
     * Get the parent documentable model (vehicle, task, etc).
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get URL for the document.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
    
    /**
     * Get a human-readable file size.
     *
     * @return string
     */
    public function getHumanSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size;
        $i = 0;
        
        while ($size >= 1024 && $i < 4) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get document type label.
     *
     * @return string
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            'release_form' => 'Release Form',
            'gate_pass' => 'Gate Pass',
            'bill_of_sale' => 'Bill of Sale',
            'title' => 'Title',
            'invoice' => 'Invoice',
        ];
        
        return $types[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }
}
