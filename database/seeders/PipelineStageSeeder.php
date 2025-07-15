<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PipelineStage;

class PipelineStageSeeder extends Seeder
{
    public function run()
    {
        $stages = [
            ['name' => 'Lead Masuk', 'slug' => 'lead', 'color' => '#6c757d', 'order' => 1],
            ['name' => 'Penjadwalan Survey', 'slug' => 'scheduling', 'color' => '#17a2b8', 'order' => 2],
            ['name' => 'Survey Dilakukan', 'slug' => 'survey', 'color' => '#ffc107', 'order' => 3],
            ['name' => 'Penawaran Dibuat', 'slug' => 'quotation', 'color' => '#fd7e14', 'order' => 4],
            ['name' => 'Negosiasi', 'slug' => 'negotiation', 'color' => '#6f42c1', 'order' => 5],
            ['name' => 'Deal/Kontrak', 'slug' => 'deal', 'color' => '#28a745', 'order' => 6],
            ['name' => 'Eksekusi Proyek', 'slug' => 'execution', 'color' => '#007bff', 'order' => 7],
            ['name' => 'Selesai', 'slug' => 'completed', 'color' => '#20c997', 'order' => 8],
        ];

        foreach ($stages as $stage) {
            PipelineStage::create($stage);
        }
    }
}