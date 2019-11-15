<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
  protected $fillable = [ 'user_id', 'reportable_id', 'reportable_type', 'comment'];

  public function reported_user(){
    return $this->belongsTo(User::class, 'other_user_id');
  }

  public function reporting_user(){
    return $this->belongsTo(User::class, 'user_id');
  }

  public function reported_group(){
    return $this->belongsTo(Group::class, 'content_id');
  }

  public function reporting_post(){
    return $this->belongsTo(Post::class, 'content_id');
  }

  public function reporting_profile(){
    return $this->belongsTo(User::class, 'content_id');
  }
}
