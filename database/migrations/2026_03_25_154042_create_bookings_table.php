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
        Schema::create('bookings', function (Blueprint $table) {
            $table->integerIncrements('booking_id');
            $table->integer('user_id');
            $table->integer('total');
            $table->string('method_payment', 50);
            $table->string('photo')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('status')->default(1)->comment('1 = Booked, 2 = Occupied, 0 = Cancel');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
