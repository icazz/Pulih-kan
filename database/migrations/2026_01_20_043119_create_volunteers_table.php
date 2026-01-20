<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            // Nullable karena user bisa saja mendaftar tanpa login
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('emergency_contact');
            $table->string('province');
            $table->string('city');
            $table->text('address');
            $table->string('role');       // Peran (Medis, Logistik, dll)
            $table->text('experience');   // Pengalaman
            
            // Status verifikasi (default: pending/menunggu)
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
