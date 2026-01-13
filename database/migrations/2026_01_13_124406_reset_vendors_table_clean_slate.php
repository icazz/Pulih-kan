<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. HAPUS TABEL LAMA (YANG BANYAK MASALAH)
        Schema::dropIfExists('vendors');

        // 2. BUAT ULANG DARI NOL (BERSIH)
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            // Relasi ke User
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Data Utama (Sesuai Register.vue)
            $table->string('nama_mitra');
            $table->string('no_telepon');
            $table->string('email');
            
            // Jasa (JSON & String)
            $table->text('jenis_jasa'); // Menyimpan array JSON
            $table->text('jasa_lainnya')->nullable();
            
            // Lokasi
            $table->string('provinsi');
            $table->string('kota');
            $table->text('alamat');
            $table->string('latitude');
            $table->string('longitude');
            
            // Status & Admin
            $table->boolean('agreement')->default(false);
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->boolean('is_verified')->default(false);
            
            // Timestamps standar
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};