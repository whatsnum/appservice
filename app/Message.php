<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
  use SoftDeletes;
  protected $fillable = ['user_id', 'conversation_id',	'reply', 'message', 'deleted_at'];
  protected $casts = ['deleted_by' => 'array'];

  public function deletedBy(User $user){
    $deleted_by = $this->deleted_by ?? [];
    $deleted_by["$user->id"] = now();
    $this->deleted_by = $deleted_by;
    return $this->save();
  }

  public function flagDeleteBy(User $user){
    $deleted_by = $this->deleted_by ?? [];
    $deleted_by["$user->id"] = now();
    $this->deleted_by = $deleted_by;
    return $this;
  }

  public function deleteForAll(){
    return $this->delete();
  }

  public function conversation(){
    return $this->belongsTo(Conversation::class);
  }

  public function sender(){
    return $this->belongsTo(User::class);
  }

  public function replied(){
    return $this->belongsTo(Message::class, 'reply');
  }
}
