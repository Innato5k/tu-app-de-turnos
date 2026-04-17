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
        Schema::table('professional_schedules', function (Blueprint $table) {
            $table->text('observations')->after('end_time')->nullable();
            $table->unsignedInteger('slot_duration')->after('observations')->default(30);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professional_schedules', function (Blueprint $table) {
            $table->dropColumn(['observations', 'slot_duration']);
        });
    }
};
