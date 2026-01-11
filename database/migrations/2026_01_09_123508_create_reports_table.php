<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pelapor
            $table->string('title'); // Judul keluhan (misal: Atap Bocor)
            $table->text('description'); // Detail kronologi
            $table->string('photo_path')->nullable(); // Lokasi file foto bukti
            $table->string('location')->nullable(); // Alamat lokasi kejadian
            $table->decimal('latitude', 10, 8)->nullable(); // Titik Garis Lintang
            $table->decimal('longitude', 11, 8)->nullable(); // Titik Garis Bujur
            $table->string('image_before')->nullable();
            $table->enum('status', ['pending', 'process', 'completed', 'cancelled'])->default('pending'); // Status laporan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
