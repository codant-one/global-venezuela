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
        Schema::table('migrants', function (Blueprint $table) {

            $table->tinyInteger('process_saime')->after('resident')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('migrants', function (Blueprint $table) {
            $table->dropColumn('process_saime');
        });
    }
};
