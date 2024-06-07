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
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('Defect1')->nullable();
            $table->string('Defect2')->nullable();
            $table->string('Defect')->nullable();
            $table->string('Date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('Defect1');
            $table->dropColumn('Defect2');
            $table->dropColumn('Defect');
            $table->dropColumn('Date');
        });
    }
};
