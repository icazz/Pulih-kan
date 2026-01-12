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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            
            // --- TAMBAHAN PENTING ---
            $table->string('video_url')->nullable(); // <--- INI YANG KURANG TADI
            
            $table->string('photo_path')->nullable(); // Opsional (kalau masih pakai)
            $table->string('image_before')->nullable(); // Untuk thumbnail foto
            
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Status dengan default 'verification' sesuai request kamu
            $table->enum('status', ['verification', 'pending', 'process', 'completed', 'cancelled'])->default('verification');
            
            $table->integer('price')->nullable(); // Tambahan untuk menyimpan harga
            $table->integer('progress')->default(0); // Tambahan untuk progress bar
            
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
