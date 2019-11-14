<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
  protected $fillable = ['user_master_id', 'name', 'value'];

  public static function updateMeta(User $user, $name, $value, $callback = false){
    $metas = $user->load(['metas' => function($q) use($name){
      $q->where('name', $name)->latest();
    }]);

    if (count($metas->metas) > 0) {
        $option = $metas->metas->first();
        $option->value = $value;
        $option->save();
    } else {
      $user->metas()->create([
        'name' => $name,
        'value' => $value,
      ]);
    }
    if($callback) $callback($user);
    return $user->myDetails();
  }

  public function user(){
    return $this->belongsTo(User::class, 'user_id');
  }
}
