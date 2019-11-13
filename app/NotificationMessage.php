<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class NotificationMessage extends Model
{
  protected $fillable = [
    'id', 'user_id', 'other_user_id', 'action', 'action_id', 'action_json',
    'title', 'message', 'read_status',
  ];
  // protected $hidden = [
  //
  // ];

  public static function createNotification($notificationData){
    $action_json=json_encode($notificationData['action_data']);
    $notificationData['message']['action_json'] = $action_json;
    $notification = [];
    foreach ($notificationData['message'] as $key => $value) {
      $notification[$key] = $value;
    }

    foreach ($notificationData['action_data'] as $key => $value) {
      $notification[$key] = $value;
    }

    $insert = self::create($notification);

    if ($insert) {
      $other_user = User::find($notificationData['action_data']['other_user_id']);
      if ($other_user) {
        // event(new \App\Events\NewNotification($other_user, $notificationData['message']['message']));
      }
    }
    return $insert;
  }

  public static function InsertNotification($user_id, $other_user_id, $action, $action_id, $action_json, $title, $title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4){
    $insert = self::create([
      'user_id'         => $user_id,
      'other_user_id'   => $other_user_id,
      'action'          => $action,
      'action_id'       => $action_id,
      'action_json'     => $action_json,
      'title'           => $title,
      'message'         => $message,
      'read_status'     => 'no',
    ]);

    if ($insert) {
      $other_user = User::find($other_user_id);
      if ($other_user) {
        // event(new \App\Events\NewNotification($other_user, $message));
      }
    }

    return $insert;
  }

  public function withAvatar(){
    $this->avatar = $this->getUserAvatarUrl();
    return $this;
  }
  //
  public function getUserAvatarUrl($conversion = 'thumb'){
    $user = $this->receiver;
    if ($user) {
      $media = $user->getFirstMedia('avatar');
      if ($media) {
        return $media->getUrl($conversion);
      }
    }
    return null;
  }
  //
  // public static function getUserNotificationById($user_id,$notification_id){
  //   return self::where('delete_flag', 'no')->where('other_user_id', $user_id)->where('notification_message_id', $notification_id)->first();
  // }
  //
  public static function getNotifications(User $user){
    return $user->notification_messages();
  }

  public function receiver(){
    return $this->belongsTo(User::class, 'other_user_id');
  }

  public function sender(){
    return $this->hasOne(User::class, 'user_id');
  }


  public static function unread(User $user){
    return self::where('other_user_id', $user->id)->where('read_status', 'no')->latest()->get();
  }

  public static function unreadCount(User $user){
    return self::unread($user)->count();
  }
}
