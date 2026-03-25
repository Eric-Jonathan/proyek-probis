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
        Schema::create('facilities', function (Blueprint $table) {
            $table->integerIncrements('facility_id');
            $table->integer('room_id');
            $table->string('name', 150);
            $table->integer('price')->default(0);
            $table->string('photo')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(1)->comment('1 = Available, 2 = Not Available 0 = Inactive');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
