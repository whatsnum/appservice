<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Validator;

class IsActivated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $request->validate([
        'user_id'       => 'required',
      ]);

      $user = User::find($request->user_id);
      if (!$user) {
        return response()->json(['status' => false,  'msg'=>trans('messages.msg_user_id_not_exist')]);
      }

      $request->user = $user;
      return $next($request);
    }
}
