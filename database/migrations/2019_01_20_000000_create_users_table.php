<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {

            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('role')->default('user');
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('premuim');
            $table->boolean('manual_premuim');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('pack_name')->nullable();
            $table->string('pack_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('start_at')->nullable();
            $table->string('expired_in')->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
