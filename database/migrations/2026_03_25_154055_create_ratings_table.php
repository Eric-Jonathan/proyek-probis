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
        Schema::create('ratings', function (Blueprint $table) {
            $table->integerIncrements('rating_id');
            $table->integer('booking_id');
            $table->integer('item_id')->comment('ID item (room / facility)');
            $table->integer('item_type')->comment('1 = Room, 2 = Facility');
            $table->integer('kebersihan');
            $table->integer('pelayanan');
            $table->integer('kenyamanan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
