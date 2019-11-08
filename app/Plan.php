<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
  protected $fillable = [
    'id', 'name', 'sub_name', 'type', 'no_of_contact',
    'amount', 'plan_days', 'description'
  ];

  public static function getUserPlanId($plan_id){
    return self::find($plan_id);
  }

  public static function free(){
    return self::where('amount', 0)->first();
  }

  public static function getPlans(){
    return self::where('amount', '!=', 0)->get();
  }

  public function user_plans(){
    return $this->hasMany(UserPlan::class, 'plan_id');
  }
}
