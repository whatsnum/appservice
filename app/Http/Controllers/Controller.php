<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Validator;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public function paginate($request, $size = 20){
    return $request->pageSize ? $request->pageSize : $size;
  }

  public function validates($request, $rules){
    $validator = Validator::make($request->all(), $rules,[
      'required' => "messages.msg_".":attribute",
    ]);

    if ($validator->fails()) {
      return array('success'=> 'false', 'status' => false, 'msg' =>trans($validator->messages()->first()));
    } else {
      return ['status' => true ];
    }
  }

  public function err($e){
    return ['status' => false, 'msg' =>$e->getMessage(), 'e' => $e->getMessage(), 'debug' => $e->getTrace()];
  }
}
