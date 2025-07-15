<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'type', 'location', 'start_date', 'target_date',
        'status', 'project_value', 'deal_value', 'client_id', 'pic_id', 'description'
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_date' => 'date',
        'project_value' => 'decimal:2',
        'deal_value' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->code = 'PRJ-' . date('Y') . '-' . str_pad(Project::whereYear('created_at', date('Y'))->count() + 1, 3, '0', STR_PAD_LEFT);
        });
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    public function latestSurvey()
    {
        return $this->hasOne(Survey::class)->latest();
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'lead' => 'secondary',
            'survey' => 'info',
            'penawaran' => 'warning',
            'negosiasi' => 'primary',
            'deal' => 'success',
            'eksekusi' => 'primary',
            'selesai' => 'success',
            'batal' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getProgressPercentageAttribute()
    {
        $percentages = [
            'lead' => 10,
            'survey' => 25,
            'penawaran' => 40,
            'negosiasi' => 60,
            'deal' => 75,
            'eksekusi' => 90,
            'selesai' => 100,
            'batal' => 0
        ];

        return $percentages[$this->status] ?? 0;
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
        return $this->morphMany(Document::class, 'documentable');
    }

    public function pipelineHistory()
    {
        return $this->hasMany(ProjectPipeline::class);
    }
    
}