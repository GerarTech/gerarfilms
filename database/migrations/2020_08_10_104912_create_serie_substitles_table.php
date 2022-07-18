<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSerieSubstitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('serie_substitles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->bigInteger('episode_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('link')->nullable();
            $table->string('lang')->nullable();
            $table->string('type')->nullable();
            $table->boolean('zip')->default(0);
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
        Schema::dropIfExists('serie_substitles');
    }
}
