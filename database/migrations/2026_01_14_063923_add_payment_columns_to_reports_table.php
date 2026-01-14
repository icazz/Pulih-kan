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
            // Tambahkan pengecekan agar tidak error jika kolom sudah ada
            if (!Schema::hasColumn('reports', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('reports', 'contract_file')) {
                $table->string('contract_file')->nullable();
            }
            if (!Schema::hasColumn('reports', 'payment_proof')) {
                $table->string('payment_proof')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
};
