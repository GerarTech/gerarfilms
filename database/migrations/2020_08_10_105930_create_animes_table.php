<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('tmdb_id')->unsigned()->unique()->nullable();
            $table->string('name');
            $table->string('original_name');
            $table->string('imdb_external_id');
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('preview_path')->nullable();
            $table->string('views')->nullable();
            $table->float('vote_average')->unsigned()->nullable();
            $table->float('vote_count')->unsigned()->nullable();
            $table->float('popularity')->unsigned()->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('is_anime')->default(1);
            $table->boolean('premuim')->default(0);
            $table->boolean('pinned')->default(0);
            $table->boolean('newEpisodes')->default(0);
            $table->boolean('featured')->default(0);
            $table->date('first_air_date')->nullable();
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
        Schema::dropIfExists('animes');
    }
}
