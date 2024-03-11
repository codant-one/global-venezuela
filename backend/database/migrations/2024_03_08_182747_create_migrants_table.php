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
        Schema::create('migrants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gender_id');
            $table->unsignedBigInteger('parish_id');
            $table->unsignedBigInteger('community_council_id')->nullable();
            $table->string('name');
            $table->string('last_name');
            $table->string('email');
            $table->date('birthdate');
            $table->string('passport_number');
            $table->tinyInteger('passport_status')->default(0);
            $table->tinyInteger('transient')->default(0);
            $table->tinyInteger('resident')->default(0);
            $table->integer('years_in_country')->nullable();
            $table->tinyInteger('antecedents')->default(0);
            $table->tinyInteger('isMarried')->default(0);
            $table->tinyInteger('has_children')->default(0);
            $table->integer('children_number')->nullable();
            $table->string('phone')->nullable();
            $table->longText('address')->nullable();
            $table->string('file_document')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');
            $table->foreign('parish_id')->references('id')->on('parishes')->onDelete('cascade');
            $table->foreign('community_council_id')->references('id')->on('community_councils')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migrants');
    }
};
