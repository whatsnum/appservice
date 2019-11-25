<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
  use SoftDeletes;
  protected $fillable = ['deleted_at'];

  public function last_message(){
    return $this->hasOne(Message::class)->latest();
  }

  public function participants(){
    return $this->belongsToMany(User::class, 'conversation_users');
  }

  public function messages(){
    return $this->hasMany(Message::class);
  }

  public function unread(){
    $user = auth()->user();
    return $this->hasMany(Message::class)->where('user_id', '!=', $user->id )->where('read_at', null);
  }

  public function author(){
    return $this->belongsTo(User::class);
  }

  public function user(){
    return $this->belongsTo(User::class, 'other_user_id');
  }
}
