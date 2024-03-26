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
        Schema::table('volunteers', function (Blueprint $table) {
            $table->unsignedBigInteger('parish_id')->nullable()->after('circuit_id');
            $table->unsignedBigInteger('community_council_id')->nullable()->after('parish_id');
            $table->longText('address')->nullable()->after('phone');

            $table->foreign('parish_id')->references('id')->on('parishes')->onDelete('cascade');
            $table->foreign('community_council_id')->references('id')->on('community_councils')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            //
        });
    }
};
