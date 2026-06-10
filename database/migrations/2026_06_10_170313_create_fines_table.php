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
        Schema::create('fines', function (Blueprint $table) {
            $table->increments('fine_id');
            $table->unsignedInteger('booking_id');
            $table->string('jenis_denda');
            $table->decimal('nominal_denda', 15, 2);
            $table->text('keterangan');
            $table->text('bukti_denda'); // JSON array of uploaded file paths
            $table->integer('status')->default(0); // 0 = Pending, 1 = Approved, 2 = Rejected
            $table->integer('is_dismissed')->default(0); // 0 = Active warning, 1 = Dismissed by renter
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
