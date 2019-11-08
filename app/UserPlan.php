<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Plan;
use App\UserNotification;

class UserPlan extends Model
{
  protected $fillable = [
    'id', 'plan_id', 'user_id', 'transaction_id', 'start_plan_date', 'no_contact_use',
    'end_plan_date'];

  // public static function checkExpiry(User $user){
  //     $today = date("Y-m-d");
  //     return self::where('user_id', $user->user_id)->where('end_plan_date', '>=', $today)
  //     ->where('plan_id', $user->current_plan_id)->orderBy('user_plan_id', 'DESC')->first();
  //   }
  //
  //   public static function getUserPlanCountData(User $user){
  //     $plan = self::getUserPlan($user);
  //     if ($plan) {
  //       $plan = $plan->count();
  //     } else {
  //       $plan = 0;
  //     }
  //     return $plan;
  //   }
  //
    public static function getUserPlan(User $user){
      return $user->plan();
      // ->latest()->first();
    }
  //
  //   public function deduct($count = 1){
  //     $this->no_contact_use = (int)$this->no_contact_use - $count;
  //     return $this->save();
  //   }
  //
  //   public static function triggerNotification($user){
  //     $plan = $user->plan->plan;
  //     $user_name = $user->name;
  //     $plan_name = $plan->plan_name;
  //     $sub_plan_name = $plan->sub_plan_name;
  //     if ($sub_plan_name == 'Free'){
  //       $set_plan_name = $plan_name;
  //     }else if ($sub_plan_name == 'Upgrade to ELITES'){
  //       $set_plan_name = $plan_name;
  //     }else if ($sub_plan_name == 'Upgrade to PRO'){
  //       $set_plan_name = $plan_name;
  //     }else if ($sub_plan_name == 'Upgrade to DIAMOND'){
  //       $set_plan_name = $plan_name;
  //     }else if ($sub_plan_name == 'BLACK'){
  //       $set_plan_name = $plan_name;
  //     }
  //
  //       $action = 'purchase_plan';
  //       $action_id = '0';
  //       $title = 'Plan Purchase';
  //       $title_2 = 'Iniciar sesión'; //German
  //       $title_3 = 'S identifier'; //French
  //       $title_4 = 'Anmeldung';//Spanish
  //       //Your PRO Membership Plan was upgraded successfully!
  //       $message = 'Your '.$set_plan_name.' Membership Plan was upgraded successfully!';
  //       $message_2 = 'Willkommen in der Schnellsuche-App.';
  //       $message_3 = 'Bienvenue à vous dans Quick Find APP.';
  //       $message_4 = 'Bienvenido a ti en la aplicación de búsqueda rápida.';
  //
  //       $action_data=array('user_id'=>$user->user_id,'other_user_id'=>$user->user_id, 'action_id'=>$action_id, 'action'=>$action);
  //       return $notification_arr[]= UserNotification::getNotificationArrSingle($user->user_id, $user->user_id,$action,$action_id, $title,$title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data);
  //   }
  //
  //   public static function updatePlan(User $user, Plan $plan){
  //     $plan_id = (int)$plan->plan_id+1;
  //
  //     $user->plan->update([
  //       'plan_id' => $plan_id,
  //       'no_contact_use'   => (int)$user->plan->no_contact_use + (int)$plan->no_of_contact,
  //     ]);
  //
  //     $user = $user->find($user->user_id);
  //     $user->current_plan_id = $plan_id;
  //     $user->save();
  //
  //     return self::triggerNotification($user);
  //   }

    public function user(){
      return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(){
      return $this->belongsTo(Plan::class, 'plan_id');
    }
}
