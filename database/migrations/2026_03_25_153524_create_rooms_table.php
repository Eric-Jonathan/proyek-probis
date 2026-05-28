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
        Schema::create('rooms', function (Blueprint $table) {
            $table->integerIncrements('room_id');
            $table->integer('user_id');
            $table->string('name');
            $table->integer('day')->default(1);
            $table->integer('capacity');
            $table->string('jenis_harga');
            $table->integer('price');
            $table->integer('min_order');
            $table->string('jenis_deposit');
            $table->integer('deposit_percent')->default(0);
            
            // Data dari LocationIQ
            $table->string('location'); // Alamat lengkap (display_name)
            $table->decimal('latitude', 10, 8);  // Koordinat Lat
            $table->decimal('longitude', 11, 8); // Koordinat Lon
            
            $table->text('rules')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(1)->comment('1 = Diajukan, 2 = Diterima, 3 = Not Available, 0 = Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
