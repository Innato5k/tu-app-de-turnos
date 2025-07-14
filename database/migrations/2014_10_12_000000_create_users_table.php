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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('cuil')->unique();            
            $table->string('password');
            $table->string('national_md_lic')->nuleable();
            $table->string('provincial_md_lic')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_opt')->nullable();
            $table->string('speciality')->nullable();
            $table->string('picture')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
