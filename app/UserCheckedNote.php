<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCheckedNote extends Model
{
    //
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
