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
        Schema::table('assets', function (Blueprint $table) {
            $table->timestamp('ongoing_status')->nullable();
            $table->timestamp('completed_status')->nullable();
            $table->string('rectifier_name')->nullable();
            $table->date('progress_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('ongoing_status');
            $table->dropColumn('completed_status');
            $table->dropColumn('rectifier_name');
            $table->dropColumn('progress_date');
        });
    }
};
