<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->bigInteger('user_id')->unsigned();
            // $table->bigInteger('other_user_id')->unsigned();
            $table->morphs('conversable');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('conversations', function (Blueprint $table) {
          // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
          // $table->foreign('other_user_id')->references('id')->on('users')->onDelete('cascade');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conversations');
    }
}
