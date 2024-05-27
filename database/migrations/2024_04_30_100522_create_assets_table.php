<?php

 use Illuminate\Database\Migrations\Migration;
 use Illuminate\Database\Schema\Blueprint;
 use Illuminate\Support\Facades\Schema;

 return new class extends Migration
 {
     public function up(): void
     {
         Schema::create('assets', function (Blueprint $table) {
                 $table->id();
                 $table->string('Functional_Location');
                 $table->string('Switchgear_Brand');
                 $table->string('Substation_Name');
                 $table->string('Health_Status')->nullable();
                 $table->integer('TEV')->nullable();
                 $table->integer('Hotspot')->nullable();
                 $table->string('Rectify_Status')->nullable();
                 $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
