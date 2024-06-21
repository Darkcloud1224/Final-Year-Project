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
        Schema::create('delete_request_log', function (Blueprint $table) {
            $table->id();
            $table->string('Functional_Location');
            $table->string('Switchgear_Brand');
            $table->string('Substation_Name');
            $table->string('TEV');
            $table->string('Hotspot');
            $table->date('Date');
            $table->string('Defect')->nullable();
            $table->string('Defect1')->nullable();
            $table->string('Defect2')->nullable();
            $table->date('Target_Date')->nullable();
            $table->string('completed_status')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delete_request_log');
    }
};
