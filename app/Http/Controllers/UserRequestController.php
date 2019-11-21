<?php

namespace App\Http\Controllers;

use App\UserRequest;
use Illuminate\Http\Request;
use App\UserPlan;
use App\Notification;
use App\User;

class UserRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = $request->user();

      return ['status' => true, 'requests' => $user->requests_pending()->paginate($request->pageSize ? $request->pageSize : 20)];
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
      $request->validate(['other_user_id' => 'required']);
      $other_user_id = $request->other_user_id;
      $user = $request->user();
      $other_user = $user->findOrFail($other_user_id);
      $msg = trans('messages.request_failed');
      $status = false;
      $notifications=[];

      // request exists between users
      if ($exists = $user->checkRequestExists($other_user)) {

        if ($exists->status == 'pending') {
          $msg = trans('messages.request_pending');
        }

        if ($exists->status == 'rejected') {
          $exists->update([
            'status' => 'pending',
          ]);
          $status = true;
          $msg = trans('messages.request_sent');
        }

        if ($exists->status == 'accepted') {
          $msg = trans('messages.request_sent_accepted');
        }
        // return ['status'=> false,'msg'=>$msg];
      } else {
        $created = $user->requests()->create([
        'other_user_id'  => $other_user_id,
        ]);

        if ($created) {
          $requestNotificationData = $user->requestNotificationData($other_user);
          $notifications[]=Notification::getNotificationData($requestNotificationData);
          // broadcast(new Newrequest($user))->toOthers();
          //------------------------------- Notification array end -----------------------
          $status = true;
          $msg = trans('messages.request_sent');
        } else {
          $msg = trans('messages.request_not_sent');
        }
      }
      return ['status'=> $status,'msg'=>$msg, 'notifications' => $notifications];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserRequest  $userRequest
     * @return \Illuminate\Http\Response
     */
    public function show(UserRequest $userRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserRequest  $userRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(UserRequest $userRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserRequest  $userRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserRequest $userRequest)
    {
      $request->validate(['action' => 'required']);
      $action = $request->action;
      $user = $request->user();
      $this->authorize('update', $userRequest);

      $update = $userRequest->update(['status' => $action ? 'accepted' : 'rejected']);
      $msg = $update ? trans('msg.updated') : trans('msg.not_updated');
      return ['status' => $update, 'msg' => $msg];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserRequest  $userRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, UserRequest $userRequest)
    {
      $user = $request->user();
      $this->authorize('delete', $userRequest);
      $delete = $userRequest->delete();
      $msg = $delete ? trans('msg.deleted') : trans('msg.not_deleted');
      return ['status' => $delete, 'msg' => $msg];
    }

    // public function state_random_users(Request $request){
    //   $user = $request->user;
    //   $random_users = $user->myRandomUser();
    //   if ($random_users) {
    //     $random_users->map(function($u){
    //       $media = $u->getFirstMedia('avatar');
    //       if ($media) {
    //         $u->avatar = $media->getUrl('thumb');
    //       } else {
    //         $u->avatar = null;
    //       }
    //     });
    //   }
    //   $users_count = User::countUsersInState($request->user->state);
    //   return ['status' => true, 'random_users' => $random_users, 'count' => $users_count];
    // }
    //
    public function random_users(Request $request){
      // $user = User::find(2801);
      $user = $request->user();
      $random_users = $user->myRandomUser($request);
      if ($random_users) {
        $random_users = $random_users->map(function($u){
          $media = $u->getFirstMedia('avatar');
          if ($media) {
            $u->avatar = $media->getUrl('thumb');
          } else {
            $u->avatar = null;
          }

          return $u->only(['avatar']);
        });
      }
      $users_count = $user->countCompleteUsers();
      return ['status' => true, 'random_users' => $random_users, 'count' => $users_count];
    }
    //
    // public function new_users(Request $request){
    //   $user = $request->user;
    //   $users = $user->newUsers($request);
    //   return ['status' => true, 'msg' => 'Fetched Users', 'users' => $users];
    // }
    //
    // public function send_request(Request $request){
    //   $validate = $this->validates($request, [
    //     'other_user_id'   => 'required',
    //   ]);
    //
    //   $user_id = $request->user_id;
    //   $other_user_id = $request->other_user_id;
    //   $user = $request->user;
    //
    //   if (!$validate['success']) {
    //     return $validate;
    //   }
    //
    //   $check_status = UserRequest::checkBetween($user_id, $other_user_id);
    //   if ($check_status) {
    //     if ($check_status->status == 'pending') {
    //       return ['success'=>'false','msg'=>array(trans('messages.msg_request_not_send'))];
    //     }
    //     return ['success'=>'false','msg'=>array(trans('messages.msg_request_not_send'))];
    //   } else {
    //     $plan_id_check = $user->current_plan;
    //     $plan_detail = $user->getMyPlanId();
    //     // $plan_type = $plan_detail->plan_type;
    //     $request_count = $user->getMyRemaningRequest();
    //
    //     // if ($plan_type == 'unlimited') {
    //     //   $check_expire = UserPlan::checkExpiry($user);
    //     //   if (!$check_expire) {
    //     //     return array('success'=>'false','msg'=>array(trans('messages.msg_today_request_not_send')),'plan_status'=>'expire');
    //     //   }
    //     // }
    //
    //     if($request_count < 1){
    //        return array('success'=>'false','msg'=>array(trans('messages.plan_expired')),'plan_status'=>'expire');
    //      } else {
    //        $status = 'pending';
    //        $delete_flag = 'no';
    //        $active_flag = 'active';
    //        $reject_time = date("Y-m-d H:i:s", strtotime('+12 hours'));
    //
    //        $save_request = $user->requests()->create([
    //          'other_user_id'  => $other_user_id,
    //          'status'         => $status,
    //          'delete_flag'    => $delete_flag,
    //          'active_flag'    => $active_flag,
    //          'reject_time'    => $reject_time,
    //        ]);
    //
    //        // deduct no of request
    //        $user->plan->deduct();
    //
    //        if ($save_request) {
    //            //------------------------------- Notification array -----------------------
    //          $user_id_notification = $user_id;
    //          $other_user_id_notification = $other_user_id;
    //          $action = 'receive_request';
    //          $action_id = '0';
    //          $title = 'New request';
    //          $title_2 = 'Iniciar sesión'; //German
    //          $title_3 = 'S identifier'; //French
    //          $title_4 = 'Anmeldung';//Spanish
    //
    //          $message = 'You have a new request from '.$user->name;
    //          $message_2 = 'Willkommen in der Schnellsuche-App.';
    //          $message_3 = 'Bienvenue à vous dans Quick Find APP.';
    //          $message_4 = 'Bienvenido a ti en la aplicación de búsqueda rápida.';
    //
    //          $action_data=array('user_id'=>$user_id_notification,'other_user_id'=>$other_user_id_notification, 'action_id'=>$action_id, 'action'=>$action);
    //
    //          $notification_arr[]=UserNotification::getNotificationArrSingle($user_id_notification, $other_user_id_notification,$action,$action_id, $title,$title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data);
    //
    //          // broadcast event
    //          // broadcast(new Newrequest($user))->toOthers();
    //          //------------------------------- Notification array end -----------------------
    //            return array('success'=>'true','msg' =>array(trans('messages.msg_request_send')),'notification_arr'=>$notification_arr);
    //        } else {
    //          return array('success'=>'false','msg'=>array(trans('messages.msg_booking_order')));
    //        }
    //      }
    //     }
    // }
    //
    // public function accept_remove_request(Request $request){
    //   $validate = $this->validates($request, [
    //     'other_user_id'   => 'required',
    //     'status'   => 'required',
    //   ]);
    //   if (!$validate['success']) {
    //     return $validate;
    //   }
    //   $user_id = $request->user_id;
    //   $other_user_id = $request->other_user_id;
    //   $user = $request->user;
    //   $status = $request->status;
    //   $check_status = UserRequest::checkRequest($other_user_id, $user_id);
    //   if ($check_status) {
    //     $request_id_arr[]=$check_status->request_id;
    //     $check_status1 = UserRequest::checkRequest($other_user_id, $user_id);
    //     if ($check_status1) {
    //       $request_id_arr[]=$check_status1->request_id;
    //       if ($check_status1->status == 'pending') {
    //         $today_date = date("Y-m-d");
    //         if ($status == 'reject') {
    //           $reject_request = $check_status1->update([
    //             'status' => $status,
    //           ]);
    //           $notification_arr = array();
    //           $user_id_notification = $user_id;
    //           $other_user_id_notification = $other_user_id;
    //           $user_name = $user->name;
    //           $action = 'reject_request';
    //   	    	$action_id = '0';
    //   	    	$title = 'Request reject';
    //   	    	$title_2 = 'Iniciar sesión'; //German
    //   	    	$title_3 = 'S identifier'; //French
    //   	    	$title_4 = 'Anmeldung';//Spanish
    //   	    	$message = 'Your request rejected by '.$user_name;
    //   	    	$message_2 = 'Willkommen in der Schnellsuche-App.';
    //   	    	$message_3 = 'Bienvenue à vous dans Quick Find APP.';
    //   	    	$message_4 = 'Bienvenido a ti en la aplicación de búsqueda rápida.';
    //   	    	$action_data=array('user_id'=>$user_id_notification,'other_user_id'=>$other_user_id_notification, 'action_id'=>$action_id, 'action'=>$action);
    //   	    	$notification_arr[]=UserNotification::getNotificationArrSingle($user_id_notification, $other_user_id_notification,$action,$action_id, $title,$title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data);
    //           return array('success'=>'true','msg' =>array(trans('messages.msg_wishlist_data_not_deleted')),'notification_arr'=>$notification_arr,'request_remove'=>'yes');
    //         } else {
    //           for ($i=0; $i < count($request_id_arr); $i++) {
    //             $request_id_check = $request_id_arr[$i];
    //             $request = UserRequest::find($request_id_check);
    //             if ($request) {
    //               $request->update([
    //                 'status' => $status,
    //               ]);
    //               $notification_arr = array();
    //               $user_id_notification = $user_id;
    //               $other_user_id_notification = $other_user_id;
    //               $user_name = $user->name;
    //               $action = 'accept_request';
    //       	    	$action_id = '0';
    //       	    	$title = 'Request accept';
    //       	    	$title_2 = 'Iniciar sesión'; //German
    //       	    	$title_3 = 'S identifier'; //French
    //       	    	$title_4 = 'Anmeldung';//Spanish
    //       	    	$message = 'Your request accepted by '.$user_name;
    //       	    	$message_2 = 'Willkommen in der Schnellsuche-App.';
    //       	    	$message_3 = 'Bienvenue à vous dans Quick Find APP.';
    //       	    	$message_4 = 'Bienvenido a ti en la aplicación de búsqueda rápida.';
    //       	    	$action_data=array('user_id'=>$user_id_notification,'other_user_id'=>$other_user_id_notification, 'action_id'=>$action_id, 'action'=>$action);
    //       	      // 	$notification_arr[]=getNotificationArrSingle($user_id_notification, $other_user_id_notification,$action,$action_id, $title,$title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data);
    //       	    	$notification_arr[]=UserNotification::getNotificationArrSingle($user_id_notification, $other_user_id_notification, $action,$action_id, $title,$title_2, $title_3, $title_4, $message, $message_2, $message_3, $message_4,$action_data);
    //     	        //------------------------------- Notification array end -----------------------
    //               return array('success'=>'true','msg'=>array(trans('messages.msg_accept_request')),'notification_arr'=>$notification_arr,'user_details'=>$user->getUserDetails($user->user_id), 'other_user' => User::getUserDetails($other_user_id)->withUserRequestStatus($user));
    //             }
    //           }
    //         }
    //       }
    //     }
    //   } else {
    //     return array('success'=>'false','msg'=>array(trans('messages.msg_data_not_found')));
    //   }
    //
    // }
    //
    // public function delete_my_contact(Request $request){
    //   $validate = $this->validates($request, [
    //     'other_user_id'   => 'required',
    //   ]);
    //   if (!$validate['success']) {
    //     return $validate;
    //   }
    //
    //   $user_id = $request->user_id;
    //   $other_user_id = $request->other_user_id;
    //   $user = $request->user;
    //
    //   $check_whatsnum_contacts = UserRequest::checkFriendship($user_id, $other_user_id);
    //   if ($check_whatsnum_contacts) {
    //     $check_whatsnum_contacts->delete();
    //     return array('success'=>'true','msg' =>array(trans('messages.delete_user_msg')), 'user_details'=>$user->getUserDetails($user->user_id));
    //   }
    //   return array('success'=>'false','msg' =>array(trans('messages.msg_file_error')));
    // }
}
