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
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->date('fecha_final')->after('fecha_movimiento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropColumn('fecha_final');
        });
    }
};
