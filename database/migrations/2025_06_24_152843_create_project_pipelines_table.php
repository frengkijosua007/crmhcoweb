<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by')->constrained('users');
            $table->datetime('changed_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['project_id', 'changed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_pipelines');
    }

    
};