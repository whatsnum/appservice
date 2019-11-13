<?php

namespace App\Http\Controllers;

use App\NotificationMessage;
use Illuminate\Http\Request;
use App\User;

class NotificationMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = $request->user();
      $notifications = NotificationMessage::getNotifications($user);
      if ($notifications) {
        $notifications->map(function($notification){
          $notification->withAvatar();
        });
        // $user_data = User::getUserDetails($user->user_id);
        // if($user_data!='NA'){
        //     $notifications->name = $user_data->name_capital;
        //     $notifications->image = $user_data->image;
        // }
        // $notification_count =
        return array('status' => true, 'msg' => trans('messages.data_found'), 'notifications'=>$notifications, 'notification_count'=>$notifications->count() );
      } else {
        return array('status' => false, 'msg' => trans('messages.msg_no_data_found'));
      }
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
     * @param  \App\NotificationMessage  $notificationMessage
     * @return \Illuminate\Http\Response
     */
    public function show(NotificationMessage $notificationMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NotificationMessage  $notificationMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(NotificationMessage $notificationMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NotificationMessage  $notificationMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NotificationMessage $notificationMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NotificationMessage  $notificationMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(NotificationMessage $notificationMessage)
    {
        //
    }

    // public function get_all_notification(Request $request){
    //   $notifications = $this->get_notifications($request);
    //   if ($notifications['success'] == 'false') {
    //     return $notifications;
    //   } else {
    //     $notification_arr = [];
    //     foreach ($notifications as $notification) {
    //       $request->notification_id = $notification->notification_message_id;
    //       $read = $this->read_my_notification($request);
    //       if ($read['success'] == 'true') {
    //         $notification_arr[] = $read['notification'];
    //       }
    //     }
    //     return array('success'=>'true','msg' =>'data found','get_notification_arr'=>$notification_arr, 'notification_count'=>count($notification_arr));
    //   }
    // }
    //
    // public function get_notifications(Request $request){
    //
    // }
    //
    public function read(Request $request, NotificationMessage $notification_message){
      // $user = $request->user();
      if ($notification_message) {
        $notification_message->update([
          'read_status' => true,
        ]);
        return ['status'=> true, 'msg' =>'notification read successfully', 'notification_message' => $notification_message];
      } else {
        return ['status'=> false,'msg' =>trans('messages.msg_data_not_found')];
      }
    }

    public function unreadCount(Request $request){
      return NotificationMessage::unreadCount($request->user());
    }
}
