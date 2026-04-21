<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('available_slots', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('capacity');
            
            $table->index(['user_id', 'start_time', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('available_slots', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'start_time', 'status']);
            $table->dropColumn('notes');
        });
    }
};