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
            $table->bigIncrements('id');
            $table->string('name', 40)->nullable();
            $table->string('email')->unique()->nullable();
            $table->integer('phone_code')->nullable();
            $table->bigInteger('phone');
            $table->string('age', 2)->nullable();
            $table->date('dob')->nullable();
            $table->integer('other_user_min_age')->nullable();
            $table->integer('other_user_max_age')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('other_user_gender', 10)->nullable();
            // $table->boolean('direct_message')->default(false);
            $table->string('interest')->nullable();
            // $table->string('bio', 121)->nullable();
            // $table->string('otp');
            // $table->enum('otp_verify', ['no', 'yes']);
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city', 200)->nullable();
            $table->string('lat', 100)->nullable();
            $table->string('lng', 100)->nullable();
            // $table->enum('notification_status', ['on', 'off'])->default('on');
            $table->integer('profile_step')->nullable();
            // $table->integer('plan_id')->unsigned();
            $table->enum('user_verfication', ['no', 'yes']);
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Schema::table('users', function (Blueprint $table) {
        //   $table->foreign('plan_id')->references('id')->on('user_plans')->onDelete('cascade');
        //  });
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
