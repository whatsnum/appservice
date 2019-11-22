<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Setting extends Model
{
  protected $fillable = ['user_id', 'name', 'value'];
  protected $setting_names = ['show_on_map'];

  public static function toggleShowOnMap(User $user){
    $settings = $user->load(['settings' => function($q){
      $q->where('name', 'show_on_map')->latest();
    }]);
    if (count($settings->settings)) {
      // return [$settings];
        $option = $settings->settings[0];
        $option->value = !$option->value;
        $option->save();
    } else {
      $user->settings()->create([
        'name' => 'show_on_map',
        'value' => false,
      ]);
    }
    return $user->myDetails();
  }

  public static function updateSetting(User $user, $name, $value, $callback = false){
    $settings = $user->load(['settings' => function($q) use($name){
      $q->where('name', $name)->latest();
    }]);

    if (count($settings->settings) > 0) {
      // return [$settings];
        $option = $settings->settings[0];
        $option->value = $value;
        $option->save();
    } else {
      $user->settings()->create([
        'name' => $name,
        'value' => $value,
      ]);
    }
    if($callback) $callback($user);
    return $user->myDetails();
  }

  public static function getSettings($user, $name){
    return $user->load(['settings' => function($q) use($name){
      $q->where('name', $name)->latest();
    }]);
  }

  public function user(){
    return $this->belongsTo(User::class);
  }
}
