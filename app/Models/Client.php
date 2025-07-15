<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'whatsapp', 'address',
        'status', 'pic_id', 'source', 'source_detail', 'notes'
    ];

    // Relationships
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'prospek' => 'warning',
            'aktif' => 'success',
            'selesai' => 'secondary'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }
}