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
        Schema::create('outsource_reports', function (Blueprint $table) {
            $table->integerIncrements('report_id');
            $table->integer('assignment_id');
            $table->string('kondisi');
            $table->string('kebersihan');
            $table->text('catatan')->nullable();
            $table->string('rekomendasi');
            $table->text('photos'); // JSON path list
            $table->string('video')->nullable();
            $table->text('facilities')->nullable(); // JSON verified list
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outsource_reports');
    }
};
