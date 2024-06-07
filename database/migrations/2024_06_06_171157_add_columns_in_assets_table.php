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
            $table->string('Defect1')->nullable();
            $table->string('Defect2')->nullable();
            $table->string('Defect')->nullable();
            $table->date('Target_Date')->nullable();
            $table->date('Date');
            $table->text('Action_Taken')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('Defect1');
            $table->dropColumn('Defect2');
            $table->dropColumn('Defect');
            $table->dropColumn('Target_Date');
            $table->dropColumn('Date');
            $table->dropColumn('Action_Taken');
        });
    }
};
