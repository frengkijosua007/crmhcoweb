<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_status',
        'to_status',
        'count',
        'average_days'
    ];

    protected $casts = [
        'count' => 'integer',
        'average_days' => 'float'
    ];

    /**
     * Get the source stage
     */
    public function fromStage()
    {
        return $this->belongsTo(PipelineStage::class, 'from_status', 'slug');
    }

    /**
     * Get the destination stage
     */
    public function toStage()
    {
        return $this->belongsTo(PipelineStage::class, 'to_status', 'slug');
    }

    /**
     * Calculate conversion rate between stages
     */
    public static function getConversionRate($fromStage, $toStage)
    {
        $fromCount = Project::where('status', $fromStage)->count();
        $toCount = Project::where('status', $toStage)->count();

        if ($fromCount === 0) {
            return 0;
        }

        return round(($toCount / $fromCount) * 100, 1);
    }
}
