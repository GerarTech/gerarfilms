<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimeSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_seasons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('tmdb_id')->unsigned()->unique()->nullable();
            $table->bigInteger('anime_id')->unsigned();
            $table->integer('season_number')->unsigned();
            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->date('air_date')->nullable();
            $table->foreign('anime_id')->references('id')->on('animes')->onDelete('cascade');
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
        Schema::dropIfExists('anime_seasons');
    }
}
