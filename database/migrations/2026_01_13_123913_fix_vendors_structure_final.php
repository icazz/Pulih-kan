<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            
            // 1. JINAKKAN ERROR 'shop_name' (Penyebab Gagal Insert)
            // Di database lama kolom ini ada & wajib diisi. Kita ubah jadi Boleh Kosong (Nullable).
            if (Schema::hasColumn('vendors', 'shop_name')) {
                $table->string('shop_name')->nullable()->change();
            }

            // 2. PASTIKAN KOLOM UTAMA ADA (Sesuai file create Anda)
            
            if (!Schema::hasColumn('vendors', 'nama_mitra')) {
                $table->string('nama_mitra')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'no_telepon')) {
                $table->string('no_telepon')->nullable();
            }

            if (!Schema::hasColumn('vendors', 'email')) {
                $table->string('email')->nullable();
            }

            // INI YANG SEBELUMNYA HILANG (Penyebab Error 1054)
            if (!Schema::hasColumn('vendors', 'jenis_jasa')) {
                // Kita pakai text agar bisa menampung array JSON panjang
                $table->text('jenis_jasa')->nullable(); 
            }

            if (!Schema::hasColumn('vendors', 'jasa_lainnya')) {
                $table->text('jasa_lainnya')->nullable();
            }

            // 3. AREA & LOKASI
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
                $table->decimal('latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('vendors', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable();
            }

            // 4. STATUS & VERIFIKASI (Tambahan Wajib untuk Admin Panel)
            if (!Schema::hasColumn('vendors', 'status')) {
                $table->string('status')->default('pending'); // pending, verified, rejected
            }
            
            if (!Schema::hasColumn('vendors', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            
            if (!Schema::hasColumn('vendors', 'agreement')) {
                $table->boolean('agreement')->default(false);
            }
        });
    }

    public function down()
    {
        // Tidak perlu rollback spesifik karena tujuannya hanya melengkapi
    }
};