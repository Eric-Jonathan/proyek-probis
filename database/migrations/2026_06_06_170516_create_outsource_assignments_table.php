<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outsource_assignments', function (Blueprint $table) {
            $table->integerIncrements('assignment_id');
            $table->integer('room_id'); 
            $table->integer('surveyor_id')->nullable(); 
            $table->integer('progress')->default(0); // 0 s/d 100%
            // Mengelola tahapan verifikasi lapangan
            $table->enum('assignment_status', ['on_the_way', 'checking', 'completed', 'canceled'])->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outsource_assignments');
    }
};
