<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {
            Schema::table('patients', function (Blueprint $table) {
                $table->boolean('is_deleted')->default(false)->after('is_active');
            });

            Schema::table('patients', function (Blueprint $table) {
                $table->softDeletes()->after('is_deleted');
            });

            $table->dropUnique(['cuil']);

            $table->unique(['cuil', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
        Schema::table('patients', function (Blueprint $table) {
            // Paso 1 para revertir: Eliminar el índice único compuesto
            $table->dropUnique(['cuil', 'deleted_at']);
        });
    }
};
