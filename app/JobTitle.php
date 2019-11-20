<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
  protected $fillable = ['name'];

  public static function titles($request){
    $search         = $request->search;
    $orderBy        = $request->orderBy;
    $pageSize       = $request->pageSize;

    if ($search) {
      return self::where('name', 'LIKE', '%'.$search.'%')->paginate($request->pageSize ? $request->pageSize : null);
    } else {
      return self::paginate($request->pageSize ? $request->pageSize : null);
    }
  }
}
