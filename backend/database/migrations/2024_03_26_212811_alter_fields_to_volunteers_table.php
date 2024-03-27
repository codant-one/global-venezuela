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
            $table->unsignedBigInteger('profession_id')->nullable()->after('community_council_id');
            $table->unsignedBigInteger('instruction_degree_id')->nullable()->after('profession_id');
            $table->longText('instagram')->nullable()->after('address');
            $table->longText('facebook')->nullable()->after('instagram');
            $table->longText('twitter')->nullable()->after('facebook');

            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
            $table->foreign('instruction_degree_id')->references('id')->on('instruction_degrees')->onDelete('cascade');            
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
