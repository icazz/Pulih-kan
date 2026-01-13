<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            
            // 1. FIX KRUSIAL: Cek & Buat 'nama_mitra' jika hilang
            // Hapus 'after' agar tidak error SQLSTATE[42S22]
            if (!Schema::hasColumn('vendors', 'nama_mitra')) {
                $table->string('nama_mitra')->nullable();
            }

            // 2. Cek & Buat kolom EMAIL
            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable();
            }

            // 3. Cek & Buat kolom NO TELEPON
            if (!Schema::hasColumn('vendors', 'no_telepon')) {
                $table->string('no_telepon')->nullable();
            }

            // 4. Cek & Buat kolom LOKASI
            if (!Schema::hasColumn('vendors', 'provinsi')) {
                $table->string('provinsi')->nullable();
                $table->string('kota')->nullable();
            }
            
            // 5. Cek Alamat (Jaga-jaga jika hilang juga)
            if (!Schema::hasColumn('vendors', 'alamat')) {
                $table->text('alamat')->nullable();
            }

            // 6. Cek Koordinat
            if (!Schema::hasColumn('vendors', 'latitude')) {
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
            }

            // 7. Buat kolom STATUS (Tujuan utama)
            if (!Schema::hasColumn('vendors', 'status')) {
                $table->string('status')->default('pending');
            }
            
            // 8. Cek is_verified
            if (!Schema::hasColumn('vendors', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Daftar kolom yang mungkin ingin dihapus saat rollback
            $columns = ['status', 'email', 'no_telepon', 'provinsi', 'kota', 'nama_mitra', 'latitude', 'longitude'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('vendors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};