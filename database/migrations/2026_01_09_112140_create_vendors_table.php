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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (one-to-one)
            $table->foreignId('user_id')->constrained()->unique()->onDelete('cascade');
            
            // Data Identitas
            $table->string('nama_mitra');
            $table->string('no_telepon');
            $table->string('email');
            
            // Jenis Jasa (Gunakan text karena menyimpan array JSON)
            $table->text('jenis_jasa'); 
            $table->text('jasa_lainnya')->nullable();
            
            // Area & Lokasi
            $table->string('provinsi');
            $table->string('kota');
            $table->text('alamat');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Status & Verifikasi
            $table->boolean('is_verified')->default(false); 
            $table->boolean('agreement')->default(false);
            
            // Field lama (opsional, hapus jika tidak dipakai lagi)
            $table->string('ktp_photo_path')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};