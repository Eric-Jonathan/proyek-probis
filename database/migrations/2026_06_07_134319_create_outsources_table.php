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
        Schema::create('outsources', function (Blueprint $table) {
            $table->id('outsource_id');
            
            // Detail Profil & Legalitas
            $table->string('company_name');
            $table->string('nib', 13);
            $table->string('npwp');
            $table->string('business_type');
            $table->text('company_address');
            
            // Person In Charge (PIC)
            $table->string('pic_name');
            $table->string('pic_position');
            $table->string('pic_email');
            $table->integer('pic_phone');
            
            // Finansial
            $table->string('bank_name');
            $table->string('bank_account');
            
            // Status Kemitraan (1 = Aktif, 0 = Nonaktif)
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outsources');
    }
};
