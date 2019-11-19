<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Interest;

class CreateInterestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->timestamps();
        });

        // $this->createData();
    }

    // public function createData(){
    //   $interests = [
    //     'Writing/Blogging','Catering/Baking','Music','Wine','Bi-Sexuals' ,'Threesome','Partying','Night Clubs','Business','Fun' ,'Investing','Love making','Sex partner','New relationship','Dating','Lesbian Dating','Gay Dating','Meeting New people','Public Speaking','Programming/Coding','Drive Others Around' ,'Fitness','Decorating Homes',
    //     'Reviewing Things','Pet Sitting','Local Guide','Chatting','Buying And Selling','Giving Advice Or Opinions','Children','Yoga','Life Coaching','Bicycling','Arts And Crafts','marriage' ,'Latest Gist','Makeups','Dog Training','Road Biking','Reading','Singing','Playing Chess','DIYS','Boxing','Dog Walking','Church' ,'Mosque',
    //     'Hindu','Hookah Smoking','Car Racing','Offroading','Firearm' ,'Hunting','Rock Climbing','Flying','Shooting/Marksmanship','Hiking','Base Jumping','Alcohol','Barbecuing','Gaming','Whittling','Snorkeling','Touring Wineries','Massage','Babysitting','Squash','Ballroom Dancing','Board Game Club','Volunteering','Sailing','Tennis','Star Gazing','Homesteading','Canoeing','Bowling','Sports','Entertaining & Event Hosting','Learning A Foreign Language',
    //     'Recipe Creation','Picnicking','Dancing','Chocolate','Pottery','Night Outs','Weekend Hangouts','Dinner Parties','Road Trips','Cooking','Keeping Fit','Drawing','Loosing Weight','Gaining Weight','Trekking','Knitting','Skating','Poetry' ,'Sketching','Jewelry','Juggling','Skateboarding','Acrobatics','Baseball','Basketball','Scouting','Gymnastics','Gambling','Table Tennis','Inventing','Football','Club Match' ,'Cars','Outdoor Games',
    //     'Indoor','Sushi','Storytelling','Carving','Camping','Surfing','Swimming','One Night Stand','Hookup','Paint balling','Drone Flying','Traveling','Food','Water Ski','Wrestling','Flying Drones','Beach Volleyball','Photography','Skydiving','Travel Partner','Room mates','Tour Skating','Skiing/Snowboarding','Painting','Charity',
    //     'Racing','Upholstery','Wood burning','Smoking' ,'Coffee' ,'Teaching'
    //   ];
    //   foreach ($interests as $key => $interest) {
    //     try {
    //       Interest::create(['name' => $interest]);
    //     } catch (\Exception $e) {
    //       print($e->getMessage());
    //     }
    //   }
    // }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interests');
    }
}
