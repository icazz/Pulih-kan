<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            // Kolom untuk harga final dari mitra (bisa kosong dulu)
            $table->decimal('final_price', 15, 2)->nullable()->after('price');
            // Kolom untuk file kontrak kerja (placeholder)
            $table->string('contract_file')->nullable()->after('final_price');
        });
    }

    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['final_price', 'contract_file']);
        });
    }
};
