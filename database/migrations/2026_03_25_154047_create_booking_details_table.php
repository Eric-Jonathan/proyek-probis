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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->integerIncrements('bd_id');
            $table->integer('booking_id');
            $table->integer('item_id')->comment('ID item (room / facility)');
            $table->integer('item_type')->comment('1 = Room, 2 = Facility');
            $table->integer('item_price');
            $table->integer('status')->default(1)->comment('1 = Active, 0 = Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
