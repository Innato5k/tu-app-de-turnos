<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $blueprint) {
            $blueprint->id();
            
            // Identificación
            $blueprint->string('name');
            $blueprint->string('last_name');
            $blueprint->string('cuil', 20)->nullable()->unique();
            $blueprint->date('birth_date')->nullable();
            $blueprint->string('gender', 20)->nullable();
            
            // La HC que dejamos preparada para el futuro
            $blueprint->string('medical_history')->nullable()->unique();
            
            // Contacto
            $blueprint->string('email')->nullable();
            $blueprint->string('phone', 50)->nullable();
            $blueprint->string('phone_opt', 50)->nullable();
            
            // Ubicación
            $blueprint->string('address')->nullable();
            $blueprint->string('city')->nullable();
            $blueprint->string('province')->nullable();
            $blueprint->string('postal_code', 15)->nullable();
            
            // Datos Médicos (Strings por ahora)
            $blueprint->string('medical_coverage')->nullable(); // Obra Social
            $blueprint->string('affiliate_number')->nullable(); // Nro de Afiliado
            $blueprint->string('preferred_modality')->nullable(); // Presencial / Virtual
            $blueprint->text('observations')->nullable();
            
            // Multi-tenancy (Campos simples para futura expansión)
            $blueprint->unsignedBigInteger('institution_id')->nullable()->index();
            $blueprint->unsignedBigInteger('created_by_id')->nullable()->index();

            // Auditoría nativa de Laravel
            $blueprint->timestamps();
            $blueprint->softDeletes(); // Habilita deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};