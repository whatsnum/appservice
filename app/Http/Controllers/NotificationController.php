<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }

    // public function otp_verify(){
    //   $phone= request('phone');
    //   $phone_code= request('phone_code');
    //   $otp_post= request('otp');
    //
    //   //-------------------------- check phone --------------------------
    //   $user = User::where('phone', $phone)->where('phone_code', $phone_code)
    //   ->where('delete_flag','no')->first();
    //
    //   if ($user) {
    //     $otp_verify='yes';
    //     $updatetime=date('Y-m-d H:i:s');
    //
    //     if($user->complete_profile == 'yes'){
    //         $profile_status = 'step_1';
    //     }else{
    //         $profile_status = 'step_5';
    //     }
    //
    //     $user->update([
    //       'otp_verify'     => $otp_verify,
    //       'updatetime'     => $updatetime,
    //       'profile_status' => $profile_status,
    //       'phone'          => $phone,
    //       'phone_code'     => $phone_code
    //     ]);
    //     // $user->save();
    //
    //     if(empty($user->profile_status)){
    //         $user->profile_status = "NA";
    //         $user->save();
    //     }
    //
    //     return ['success'=>'true','msg' =>array(trans('messages.msg_verified')), 'profile_status'=>$user->profile_status,'user_id'=>$user->user_id,'complete_profile'=>$user->complete_profile,'code'=>$user->phone_code, 'user' => $user];
    //
    //   } else {
    //     return array('success'=>'false','msg' =>array(trans('messages.msg_invalid_otp')));
    //   }
    }
}
