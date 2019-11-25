<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('conversation_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('reply')->unsigned()->nullable();
            // $table->bigInteger('other_user_id')->unsigned()->nullable();
            // $table->morphs('messageable');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->json('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('messages', function (Blueprint $table) {
          $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
          $table->foreign('reply')->references('id')->on('messages')->onDelete('cascade');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          // $table->foreign('other_user_id')->references('id')->on('users')->onDelete('set null');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
