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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('phone', 30)->after('event');
            $table->text('notes')->nullable()->after('photo');
        });

        Schema::table('booking_details', function (Blueprint $table) {
            $table->string('item_name', 255)->after('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['phone', 'notes']);
        });

        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('item_name');
        });
    }
};
