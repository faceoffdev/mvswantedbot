<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('ovd');
            $table->string('category');
            $table->string('first_name_u');
            $table->string('last_name_u');
            $table->string('middle_name_u');
            $table->string('first_name_r');
            $table->string('last_name_r');
            $table->string('middle_name_r');
            $table->string('first_name_e');
            $table->string('last_name_e');
            $table->dateTime('birth_date');
            $table->string('sex');
            $table->string('lost_date');
            $table->string('lost_place');
            $table->string('article_crim');
            $table->string('restraint');
            $table->string('contact');

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
