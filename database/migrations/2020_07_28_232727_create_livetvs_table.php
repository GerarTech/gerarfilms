<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivetvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livetvs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('overview')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('link')->nullable();
            $table->integer('featured')->default(0)->nullable();
            $table->integer('embed')->default(0)->nullable();
            $table->integer('status')->default(1);
            $table->integer('live')->default(1);
            $table->integer('hls')->default(0);
            $table->integer('active')->default(1);
            $table->integer('vip')->default(0)->nullable();
            $table->integer('views')->unsigned()->default(0);
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
        Schema::dropIfExists('livetvs');
    }
}
