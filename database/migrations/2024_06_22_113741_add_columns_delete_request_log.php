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
        Schema::table('delete_request_log', function (Blueprint $table) {
            $table->string('Approved');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delete_request_log', function (Blueprint $table) {
            $table->dropColumn('Approved');

        });
    }
};
