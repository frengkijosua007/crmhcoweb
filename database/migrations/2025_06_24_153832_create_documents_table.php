<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('original_name');
            $table->string('category'); // penawaran, kontrak, invoice, survey, design, progress, other
            $table->string('type'); // pdf, image, word, excel, other
            $table->text('description')->nullable();
            $table->string('path');
            $table->bigInteger('size');
            $table->string('extension', 10);
            $table->morphs('documentable'); // polymorphic relation
            $table->foreignId('uploaded_by')->constrained('users');
            $table->boolean('is_public')->default(false);
            $table->integer('views')->default(0);
            $table->integer('downloads')->default(0);
            $table->timestamps();
            
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('category');
            $table->index('type');
            $table->index('uploaded_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
};