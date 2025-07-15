<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SurveyPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id', 'filename', 'path', 'thumbnail_path', 'caption', 'order'
    ];

    // Relationships
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    // Accessors
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getThumbnailUrlAttribute()
    {
        return Storage::url($this->thumbnail_path ?? $this->path);
    }

    // Delete file when model is deleted
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($photo) {
            Storage::disk('public')->delete($photo->path);
            if ($photo->thumbnail_path) {
                Storage::disk('public')->delete($photo->thumbnail_path);
            }
        });
    }
}