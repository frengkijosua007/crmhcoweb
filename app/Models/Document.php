<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'original_name', 'category', 'type', 'description',
        'path', 'size', 'extension', 'uploaded_by', 'is_public',
        'views', 'downloads'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'size' => 'integer',
        'views' => 'integer',
        'downloads' => 'integer'
    ];

    // Relationships
    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getCategoryBadgeAttribute()
    {
        $badges = [
            'penawaran' => 'primary',
            'kontrak' => 'success',
            'invoice' => 'warning',
            'survey' => 'info',
            'design' => 'secondary',
            'progress' => 'dark',
            'other' => 'light'
        ];
        
        return $badges[$this->category] ?? 'secondary';
    }

    public function getIconAttribute()
    {
        $icons = [
            'pdf' => 'bi-file-pdf',
            'image' => 'bi-file-image',
            'word' => 'bi-file-word',
            'excel' => 'bi-file-excel',
            'powerpoint' => 'bi-file-ppt',
            'archive' => 'bi-file-zip',
            'video' => 'bi-file-play',
            'audio' => 'bi-file-music',
            'other' => 'bi-file-earmark'
        ];
        
        return $icons[$this->type] ?? 'bi-file-earmark';
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Delete file when model is deleted
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($document) {
            Storage::disk('public')->delete($document->path);
        });
    }
}