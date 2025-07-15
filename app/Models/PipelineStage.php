<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get projects in this pipeline stage
     */
    public function projects()
    {
        $statusMapping = [
            'lead' => 'lead',
            'survey' => 'survey',
            'quotation' => 'penawaran',
            'negotiation' => 'negosiasi',
            'deal' => 'deal',
            'execution' => 'eksekusi',
            'completed' => 'selesai',
            'cancelled' => 'batal'
        ];

        $projectStatus = array_search($this->slug, $statusMapping) !== false ?
            array_search($this->slug, $statusMapping) : $this->slug;

        return Project::where('status', $projectStatus);
    }

    /**
     * Get conversion rates from this stage
     */
    public function conversionsFrom()
    {
        return $this->hasMany(PipelineConversion::class, 'from_status', 'slug');
    }

    /**
     * Get conversion rates to this stage
     */
    public function conversionsTo()
    {
        return $this->hasMany(PipelineConversion::class, 'to_status', 'slug');
    }
}
