<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('other_user_id')->unsigned()->nullable();
            $table->text('action')->nullable();
            $table->integer('action_id')->nullable();
            $table->text('action_json')->nullable();
            $table->text('title')->nullable();
            $table->text('message')->nullable();
            $table->enum('read_status', ['no', 'yes'])->default('no');
            $table->timestamps();
        });

        Schema::table('notification_messages', function (Blueprint $table) {
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('other_user_id')->references('id')->on('users')->onDelete('set null');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_messages');
    }
}
