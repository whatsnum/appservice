<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
  protected $fillable = ['other_user_id', 'type'];

  // public function blockers(){
  //   return $this->belongsTo(User::class, 'other_user_id')->where('type' => 'block');
  // }

  public function user(){
    return $this->belongsTo(User::class, 'user_id')->first();
  }

  public function otherUser(){
    return $this->belongsTo(User::class, 'other_user_id')->first();
  }

  public function blocker(){
    return $this->belongsTo(User::class)->where('type', 'block');
  }
  // public function contacts(){
  //   return $this->belongsTo(User::class)->orWhere('other');
  // }
}
