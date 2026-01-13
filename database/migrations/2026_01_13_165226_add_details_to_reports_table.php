<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Kolom untuk menampung array checkbox (JSON)
            if (!Schema::hasColumn('reports', 'damage_types')) {
                $table->json('damage_types')->nullable(); 
            }
            
            // Kolom untuk Luas Rumah
            if (!Schema::hasColumn('reports', 'house_size')) {
                $table->string('house_size')->nullable();
            }

            // Kolom khusus Drive Link (agar tidak rancu dengan video_url)
            if (!Schema::hasColumn('reports', 'drive_link')) {
                $table->string('drive_link')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['damage_types', 'house_size', 'drive_link']);
        });
    }
};
