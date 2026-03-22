<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fm1_import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->integer('record_count')->default(0);
            $table->string('status')->default('completed'); // completed, partial, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add batch reference to fm1_forms
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('import_batch_id')->nullable()->after('id');
            $table->foreign('import_batch_id')->references('id')->on('fm1_import_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropForeign(['import_batch_id']);
            $table->dropColumn('import_batch_id');
        });
        Schema::dropIfExists('fm1_import_batches');
    }
};
