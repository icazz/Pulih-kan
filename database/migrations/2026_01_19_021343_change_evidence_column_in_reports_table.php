<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Hapus kolom lama (pastikan namanya benar 'drive_link')
            // Jika Anda ingin menyimpan data link lama, hapus baris dropColumn ini!
            if (Schema::hasColumn('reports', 'drive_link')) {
                $table->dropColumn('drive_link');
            }
            
            // Tambah kolom baru (hanya jika belum ada)
            if (!Schema::hasColumn('reports', 'evidence_files')) {
                $table->json('evidence_files')->nullable()->after('description');
            }
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('evidence_files');
            $table->string('drive_link')->nullable();
        });
    }
};
