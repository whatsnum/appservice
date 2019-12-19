<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
  protected $fillable = [
    'user_id', 'other_user_id', 'status',
  ];

  public static function myRequests(User $user){
    return self::where('status', 'pending')->where('other_user_id', $user->id)
    ->latest()->get();
  }

  public static function mySentRequests(User $user){
    return self::where('status', 'pending')->where('user_id', $user->id)
    ->latest()->get();
  }

  public static function myContacts(User $user){
    return self::where('status', 'accept')->where(function($q) use ($user){
      $q->where('user_id', $user->id)
      ->orWhere('other_user_id', $user->id);
    })->latest()->get();
  }

  public static function requestSatus($user_id, $other_user_id){
    return self::where('status', 'pending')->where(function($q) use ($user_id, $other_user_id){
      $q->where('user_id', $user_id)->where('other_user_id', $other_user_id)
      ->orWhere('user_id', $other_user_id)->where('other_user_id', $user_id);
    })->first();
  }

  public static function usersRequestStatus(User $user, User $other_user){
    $user_id = $user->id;
    $other_user_id = $other_user->id;
    $status = self::where(function($q) use ($user_id, $other_user_id){
      $q->where('user_id', $user_id)->where('other_user_id', $other_user_id);
    })->orWhere(function($q) use ($other_user_id, $user_id){
      $q->where('user_id', $other_user_id)->where('other_user_id', $user_id);
    })->latest()->first();

    if (!$status) {
      return 'no';
    }

    if ($status->status == 'pending') {
      if ($status->user_id == $user_id) {
        return 'sent';
      } else {
        return 'received';
      }
    } else if ($status->status == 'accept') {
      return 'yes';
    } else {
      return 'no';
    }
  }

  public static function withRequestDetail($stmt){
    return $stmt->with('requester')->select()->addSelect(\DB::raw("(CASE WHEN user_requests.status = 'pending' THEN 'sent' ELSE CASE WHEN user_requests.status = 'accepted' THEN 'yes' ELSE 'no' END END) AS request_detail"));
  }

  public static function checkFriendship($user_id, $other_user_id){
    return self::where('status', 'accept')->where(function($q) use ($user_id, $other_user_id){
      $q->where('user_id', $user_id)->where('other_user_id', $other_user_id)
      ->orWhere('user_id', $other_user_id)->where('other_user_id', $user_id);
    })->latest()->first();
  }

  // public function checkBetween(User $other_user){
  //   return $this->requests()->where('other_user_id', $other_user->id)
  //   ->orWhere('user_id', $other_user->id)->where('status', 'pending')
  //   ->orWhere('status', 'accepted')->first();
  // }

  public static function checkRequest($other_user_id, $user_id){
    return self::where('user_id', $other_user_id)->where('other_user_id', $user_id)
    ->where('status', 'pending')->latest();
  }

  public static function getWhatsNumContactCount(User $user){
    return self::myContacts($user)->count();
  }

  public function requester(){
    return $this->belongsTo(User::class, 'user_id');
  }




}
