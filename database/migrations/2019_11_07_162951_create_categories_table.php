<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Category;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            // $table->morphs('categorizeable');
            $table->string('type');
            $table->timestamps();
        });

        // $group_categories = ['Business', 'Food & Drink', 'Events', 'Culture & Art', 'Family', 'Health & Fitness', 'Home & Garden', 'Mens Topics', 'Money', 'Self Help', 'Sports', 'Style, Fashion & Beauty', 'Technology', 'Womens Topics'];
        // foreach ($group_categories as $cat) {
        //   Category::create([
        //     'name' => $cat,
        //     'type' => 'App\Group'
        //   ]);
        // }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
