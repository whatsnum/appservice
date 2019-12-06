<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function user_checked_notes()
    {
        return $this->hasMany('App\UserCheckedNote');
    }
    public static function sortByDate($notes) {
        return $notes->sortByDesc(function ($element) {
            return $element->data ? $element->data->updated_at : $element->updated_at;
        });
    }
}
