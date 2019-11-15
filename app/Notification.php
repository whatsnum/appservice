<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\NotificationMessage;

class Notification extends Model
{
  protected $fillable = ['id', 'player_id', 'device_type', 'type', 'notifiable_type', 'notifiable_id',	'data',	'read_at'];

  public static function DeviceTokenStore_1_Signal(User $user, $device_type, $player_id){
    $oneSignalData = $user->oneSignalData();
    // dd($oneSignalData, $device_type, $player_id);

    if($oneSignalData){
      $oneSignalData->update([
        'device_type' => $device_type,
        'player_id'   => $player_id,
        'id'          => rand(),
        // 'type'        => 'onesignal'
      ]);
    } else {
      $user->notifications()->create([
        'device_type' => $device_type,
        'player_id'   => $player_id,
        'id'          => rand(),
        'type'        => 'onesignal'
      ]);
      // dd($insert_check, $device_type, $player_id);
    }
    return $oneSignalData;
  }

  public function notifiable(){
    return $this->morphTo();
  }
  //
  public static function getUserPlayerId($user_id){
    $user = User::find($user_id);
    if ($user) {
      return $user->oneSignalData();//self::where('user_id', $user_id)->where('player_id', '!=', NULL)->first();
      // return $select_all;
      // if ($select_all) {
      //   $player_id = $select_all->player_id ;
      //   if($player_id == '123456'){
      //     $player_id = 'no';
      //   }
      // } else {
      //   $player_id = 'no';
      // }
      // return $player_id;
    }

    return false;
  }

  public static function makeLikeProfile(User $user, User $other_user){
    $notificationData = self::makeNotificationData($user, $other_user, 'like_profile', 'New Profile Like', "{$user->name} Liked your profile");
    $notifications = self::getNotificationData($notificationData);
    return $notifications;
  }

  public static function makeRegard(User $user, User $other_user, $message){
    $notificationData = self::makeNotificationData($user, $other_user, 'receive_regard', 'New Regard', "{$user->name} Sent you a regard '$message'");
    $notification_arr = self::getNotificationData($notificationData);
    return $notification_arr;
  }

  public static function getNotificationData($notificationData){
    $notifications = [];
    $insert_status=NotificationMessage::createNotification($notificationData);
    if($insert_status){
      $notification_status=User::getNotificationStatus($notificationData['action_data']['other_user_id']);
      if($notification_status)
      {
        $player_id = self::getUserPlayerId($notificationData['action_data']['other_user_id']);
        if($player_id){
          $notifications=array('player_id'=>$player_id->player_id, 'title'=>$notificationData['message']['title'], 'message'=>$notificationData['message']['message'], 'action_json'=>$notificationData['action_data']);
        }
      }
    }

    return $notifications;
  }
  //
  public static function makeNotificationData(User $user, User $other_user, $action, $title, $message){
    return [
      'message' => [
        'user_name'    => $user->name,
        'action'       => $action,
        'action_id'    => 0,
        'title'        => $title,
        'message'      => $message,
      ],
      'action_data'  => [
        'user_id'       => $user->id,
        'other_user_id' => $other_user->id,
        'action_id'     => 0,
        'action'        => $action
      ],
    ];
  }

  // public static function getNotificationArrSingle($user_id, $other_user_id,$action,$action_id, $title, $title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data){
  //   $notification_arr = array();
  //   $action_json=json_encode($action_data);
  //   $insert_status=UserNotificationMessage::InsertNotification($user_id, $other_user_id, $action, $action_id, $action_json, $title, $title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4);
  //   if($insert_status == 'yes'){
  //     $notification_status=User::getNotificationStatus($other_user_id);
  //     if($notification_status == 'yes')
  //     {
  //       $player_id = self::getUserPlayerId($other_user_id);
  //       if($player_id !='no'){
  //           $notification_arr=array('player_id'=>$player_id, 'title'=>$title, 'message'=>$message, 'action_json'=>$action_data);
  //       }
  //     }
  //   }
  //
  //     if(empty($notification_arr)){
  //           $notification_arr='NA';
  //     }
  //     return $notification_arr;
  // }

}
