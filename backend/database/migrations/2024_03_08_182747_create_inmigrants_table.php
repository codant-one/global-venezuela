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
        Schema::create('inmigrants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('community_council_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gender_id');
            $table->string('name');
            $table->string('last_name');
            $table->date('birthdate');
            $table->string('passport_number')->nullable();
            $table->tinyInteger('transient')->nullable();
            $table->tinyInteger('resident')->nullable();
            $table->integer('years_in_country')->nullable();
            $table->tinyInteger('antecedents')->nullable();
            $table->tinyInteger('married_venezuela')->nullable();
            $table->tinyInteger('has_venezuelan_children')->nullable();
            $table->integer('number_of_children')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('file_document')->nullable();
            $table->timestamps();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('community_council_id')->references('id')->on('community_councils')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inmigrants');
    }
};
