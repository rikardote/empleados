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
        Schema::create('fm1_forms', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('num_empleado')->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('curp', 18)->nullable();
            $table->string('sexo')->nullable();
            $table->string('escolaridad')->nullable();
            $table->string('cedula')->nullable();
            $table->text('domicilio')->nullable();
            $table->string('hijos')->nullable();
            $table->string('nacionalidad')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fm1_forms');
    }
};
