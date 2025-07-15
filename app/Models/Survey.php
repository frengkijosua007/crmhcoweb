<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'surveyor_id', 'scheduled_date', 'actual_date',
        'status', 'latitude', 'longitude', 'address', 'notes', 'checklist_data'
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_date' => 'datetime',
        'checklist_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function photos()
    {
        return $this->hasMany(SurveyPhoto::class);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForSurveyor($query, $surveyorId)
    {
        return $query->where('surveyor_id', $surveyorId);
    }
    public function getChecklistDataAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setChecklistDataAttribute($value)
    {
        $this->attributes['checklist_data'] = json_encode($value);
    }

    // Add this accessor for formatted checklist
    public function getFormattedChecklistAttribute()
    {
        $data = $this->checklist_data;
        
        return [
            'electricity' => [
                'value' => $data['electricity'] ?? null,
                'label' => $data['electricity'] == 'yes' ? 'Ada' : 'Tidak Ada',
                'notes' => $data['electricity_notes'] ?? null
            ],
            'water' => [
                'value' => $data['water'] ?? null,
                'label' => $data['water'] == 'yes' ? 'Ada' : 'Tidak Ada',
                'notes' => $data['water_notes'] ?? null
            ],
            'road_access' => [
                'value' => $data['road_access'] ?? null,
                'label' => $this->getRoadAccessLabel($data['road_access'] ?? null),
                'notes' => null
            ],
            'permit_status' => [
                'value' => $data['permit_status'] ?? null,
                'label' => $this->getPermitStatusLabel($data['permit_status'] ?? null),
                'notes' => null
            ],
            'existing_condition' => [
                'value' => $data['existing_condition'] ?? null,
                'label' => $this->getExistingConditionLabel($data['existing_condition'] ?? null),
                'notes' => null
            ],
            'area_size' => [
                'value' => $data['area_size'] ?? null,
                'label' => ($data['area_size'] ?? 0) . ' m²',
                'notes' => null
            ]
        ];
    }

    private function getRoadAccessLabel($value)
    {
        $labels = [
            'easy' => 'Mudah - Mobil besar bisa masuk',
            'medium' => 'Sedang - Hanya mobil kecil',
            'difficult' => 'Sulit - Hanya motor/jalan kaki'
        ];
        
        return $labels[$value] ?? '-';
    }

    private function getPermitStatusLabel($value)
    {
        $labels = [
            'complete' => 'Lengkap',
            'process' => 'Dalam Proses',
            'none' => 'Belum Ada'
        ];
        
        return $labels[$value] ?? '-';
    }

    private function getExistingConditionLabel($value)
    {
        $labels = [
            'good' => 'Baik',
            'medium' => 'Sedang',
            'bad' => 'Buruk',
            'empty' => 'Tanah Kosong'
        ];
        
        return $labels[$value] ?? '-';
    }

}