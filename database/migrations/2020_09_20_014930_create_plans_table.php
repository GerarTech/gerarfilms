<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
    
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price');
            $table->string('stripe_plan_id');
            $table->string('stripe_price_id');
            $table->string('pack_duration')->default(1);
            $table->string('currency_plan_app')->nullable();

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
        Schema::dropIfExists('plans');
    }
}
