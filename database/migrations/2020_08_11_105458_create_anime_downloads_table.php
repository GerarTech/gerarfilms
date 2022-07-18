<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimeDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_downloads', function (Blueprint $table) {
            
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('anime_episode_id')->unsigned();
            $table->string('server');
            $table->string('header');
            $table->string('useragent');
            $table->string('link');
            $table->string('lang')->nullable();
            $table->string('video_name')->nullable();
            $table->boolean('youtubelink')->default(0);
            $table->boolean('external')->default(0);
            $table->boolean('supported_hosts')->default(0);
            $table->boolean('status')->default(1);
            $table->foreign('anime_episode_id')->references('id')->on('anime_episodes')->onDelete('cascade');
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
        Schema::dropIfExists('anime_downloads');
    }
}
