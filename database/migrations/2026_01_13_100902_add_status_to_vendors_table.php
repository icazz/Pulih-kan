<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            
            // 1. Cek & Buat kolom EMAIL jika belum ada
            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->after('nama_mitra')->nullable();
            }

            // 2. Cek & Buat kolom NO TELEPON jika belum ada
            if (!Schema::hasColumn('vendors', 'no_telepon')) {
                $table->string('no_telepon')->after('nama_mitra')->nullable();
            }

            // 3. Cek & Buat kolom LOKASI (Provinsi/Kota) jika belum ada
            if (!Schema::hasColumn('vendors', 'provinsi')) {
                $table->string('provinsi')->nullable();
                $table->string('kota')->nullable();
            }

            // 4. Buat kolom STATUS (Tujuan utama migrasi ini)
            // Kita hapus "after('email')" agar aman jika urutan kolom beda
            if (!Schema::hasColumn('vendors', 'status')) {
                $table->string('status')->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $columns = ['status', 'email', 'no_telepon', 'provinsi', 'kota'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('vendors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};