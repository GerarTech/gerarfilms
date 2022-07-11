<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeaturedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('featureds', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->integer('featured_id')->nullable();
            $table->string('title');
            $table->string('poster_path')->nullable();
            $table->string('type')->nullable();
            $table->string('genre')->nullable();
            $table->boolean('premuim')->default(0);
            $table->boolean('enable_miniposter')->default(0);
            $table->string('miniposter')->nullable();
            $table->boolean('custom')->default(0);
            $table->string('custom_link')->nullable();
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
        Schema::dropIfExists('featureds');
    }
}
