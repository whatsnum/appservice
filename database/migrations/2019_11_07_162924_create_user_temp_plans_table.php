<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTempPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_temp_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('plan_id')->unsigned()->nullable();
            $table->date('start_plan_date');
            $table->integer('no_contact_use');
            $table->date('end_plan_date');
            $table->timestamps();
        });

        Schema::table('user_temp_plans', function (Blueprint $table) {
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('plan_id')->references('id')->on('plans')->onDelete('set null');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_temp_plans');
    }
}
