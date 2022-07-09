<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSerieVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serie_videos', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('episode_id')->unsigned();
            $table->string('server')->nullable();
            $table->string('header')->nullable();
            $table->string('useragent')->nullable();
            $table->string('link')->nullable();
            $table->string('lang')->nullable();
            $table->string('video_name')->nullable();
            $table->boolean('embed')->default(0);
            $table->boolean('youtubelink')->default(0);
            $table->boolean('hls')->default(0);
            $table->boolean('supported_hosts')->default(0);
            $table->boolean('drm')->default(0);
            $table->string('drmuuid')->nullable();
            $table->string('drmlicenceuri')->nullable();
            $table->boolean('status')->default(1);
            $table->foreign('episode_id')->references('id')->on('episodes')->onDelete('cascade');
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
        Schema::dropIfExists('serie_videos');
    }
}
