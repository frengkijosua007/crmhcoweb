<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('type', ['kantor', 'showroom', 'kafe', 'restoran', 'outlet', 'other']);
            $table->text('location');
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->enum('status', ['lead', 'survey', 'penawaran', 'negosiasi', 'deal', 'eksekusi', 'selesai', 'batal']);
            $table->decimal('project_value', 15, 2)->nullable();
            $table->decimal('deal_value', 15, 2)->nullable();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('pic_id')->constrained('users');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};