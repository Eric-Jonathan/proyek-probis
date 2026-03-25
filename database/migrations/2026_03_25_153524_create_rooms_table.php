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
            $table->integer('capacity');
            $table->integer('price');
            $table->integer('deposit_percent')->default(0);
            $table->string('location');
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
