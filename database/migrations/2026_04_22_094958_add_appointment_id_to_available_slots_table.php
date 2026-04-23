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
        Schema::table('available_slots', function (Blueprint $column) {
            $column->foreignId('appointment_id')
                  ->nullable() 
                  ->after('status') // Lo ponemos después del status para mantener el orden
                  ->constrained('appointments')
                  ->nullOnDelete(); 
        });
    }

    public function down(): void
    {
        Schema::table('available_slots', function (Blueprint $column) {
            $column->dropForeign(['appointment_id']);
            $column->dropColumn('appointment_id');
        });
    }
};
