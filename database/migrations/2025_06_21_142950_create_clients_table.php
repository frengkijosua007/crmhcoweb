<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->text('address');
            $table->enum('status', ['prospek', 'aktif', 'selesai'])->default('prospek');
            $table->foreignId('pic_id')->constrained('users');
            $table->enum('source', ['referral', 'website', 'walk-in', 'social-media', 'other']);
            $table->string('source_detail')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};