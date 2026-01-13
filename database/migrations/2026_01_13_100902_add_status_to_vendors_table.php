<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            
            // 1. Identitas Dasar
            if (!Schema::hasColumn('vendors', 'nama_mitra')) {
                $table->string('nama_mitra')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'no_telepon')) {
                $table->string('no_telepon')->nullable();
            }

            // 2. Jasa & Layanan (INI YANG BIKIN ERROR SEBELUMNYA)
            if (!Schema::hasColumn('vendors', 'jenis_jasa')) {
                // Simpan array sebagai JSON/Text
                $table->json('jenis_jasa')->nullable(); 
            }
            if (!Schema::hasColumn('vendors', 'jasa_lainnya')) {
                $table->string('jasa_lainnya')->nullable();
            }

            // 3. Lokasi & Alamat
            if (!Schema::hasColumn('vendors', 'provinsi')) {
                $table->string('provinsi')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'kota')) {
                $table->string('kota')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'alamat')) {
                $table->text('alamat')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'latitude')) {
                $table->string('latitude')->nullable();
            }
            if (!Schema::hasColumn('vendors', 'longitude')) {
                $table->string('longitude')->nullable();
            }

            // 4. Status & Persetujuan
            if (!Schema::hasColumn('vendors', 'agreement')) {
                $table->boolean('agreement')->default(false);
            }
            if (!Schema::hasColumn('vendors', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('vendors', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $columns = [
                'nama_mitra', 'email', 'no_telepon', 
                'jenis_jasa', 'jasa_lainnya',
                'provinsi', 'kota', 'alamat', 'latitude', 'longitude',
                'agreement', 'status', 'is_verified'
            ];
            
            foreach ($columns as $col) {
                if (Schema::hasColumn('vendors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};