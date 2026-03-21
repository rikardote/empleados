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
            $table->string('titular_area')->nullable();
            $table->string('responsable_admvo')->nullable();
            $table->string('titular_centro')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropColumn(['titular_area', 'responsable_admvo', 'titular_centro']);
        });
    }
};
