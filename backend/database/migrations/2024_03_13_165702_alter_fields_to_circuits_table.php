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
        Schema::table('circuits', function (Blueprint $table) {
            $table->dropForeign(['parish_id']);
            $table->dropColumn('parish_id');

            $table->unsignedBigInteger('municipality_id')->after('id');

            $table->foreign('municipality_id')->references('id')->on('municipalities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circuits', function (Blueprint $table) {
            $table->foreign('parish_id')->references('id')->on('parishes')->onDelete('cascade');
            $table->dropColumn('municipality_id');
        });
    }
};
