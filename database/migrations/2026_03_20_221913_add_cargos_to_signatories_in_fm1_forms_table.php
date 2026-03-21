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
            $table->string('cargo_titular_area')->nullable();
            $table->string('cargo_responsable_admvo')->nullable();
            $table->string('cargo_titular_centro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropColumn(['cargo_titular_area', 'cargo_responsable_admvo', 'cargo_titular_centro']);
        });
    }
};
