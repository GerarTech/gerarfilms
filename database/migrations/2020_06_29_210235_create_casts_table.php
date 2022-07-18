<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->integer('cast_id')->nullable();
            $table->string('original_name')->nullable();
            $table->integer('gender')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('profile_path')->nullable();
            $table->string('imdb_id')->nullable();
            $table->string('known_for_department')->nullable();
            $table->longText('biography')->nullable();
            $table->boolean('adult')->default(0);
            $table->string('character')->nullable();
            $table->string('birthday')->nullable();
            $table->string('credit_id')->nullable();
            $table->integer('popularity')->nullable();
            $table->integer('order')->nullable();
            $table->integer('views')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('casts');
    }
}
