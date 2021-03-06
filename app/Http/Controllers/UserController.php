<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notification;
use App\UserPlan;
use App\Plan;
use App\Setting;
use App\Media;
use App\Activity;
use App\UserMeta;
use Carbon\Carbon;

class UserController extends Controller
{
  public function count(){
    $count = User::count();
    return ['status' => true, 'count' => $count];
  }

  public function login(Request $request){
    $request->validate([
      'device_type' => 'required',
      'player_id' => 'required',
      'phone' => 'required',
      'phone_code' => 'required',
    ]);

    $phone = $request->phone;
    $phone_code = $request->phone_code;
    $lat = $request->lat;
    $lng = $request->lng;
    $city = $request->city;
    $country= $request->country;
    $state = $request->state;
    $device_type = $request->device_type;
    $player_id = $request->player_id;

    $user = User::where('phone', $phone)->where('phone_code', $phone_code)->first();

    if ($user) {
      // $new_otp = $user->generateRandomOTP(4);
      $user->update([
        // 'otp' => $new_otp,
        'lat' => $lat,
        'lng' => $lng,
        'city' => $city,
        'state' => $state,
        'country' => $country,
      ]);

  		$notification[] =  Notification::DeviceTokenStore_1_Signal($user, $device_type, $player_id);

      $token =  $user->createToken('MyApp');

      return ['status' => true, 'msg' => trans('messages.msg_phone_inserted'),
        'token' => $token->accessToken,
        'token_type' => 'Bearer',
        'expires_at' => Carbon::parse(
            $token->token->expires_at
        )->toDateTimeString(),
        'notifications'=>$notification,'user'=>$user->myDetails()
      ];

    } else {
      // $otp=User::generateRandomOTP(4);
    	// insert phone number
      $user = User::create([
        'phone' => $phone,
        // 'otp' => $otp,
        'otp_verify' => 'yes',
        'profile_step' => 1,
        'phone_code' => $phone_code,
        'lat' => $lat,
        'lng' => $lng,
        // append
        'city' => $city,
        'state' => $state,
        'country' => $country,
      ]);

      if (!$user) {
        return array('status'=>false,'msg'=>trans('messages.msg_phone_not_insert'));
      }

      //------------------------- update user player_id for push notifications ---------------------
      Notification::DeviceTokenStore_1_Signal($user, $device_type, $player_id);
      $token =  $user->createToken('MyApp');

      return ['status'=>true,'msg' =>trans('messages.msg_phone_inserted'), 'user'=>$user,
        'token' => $token->accessToken,
        'token_type' => 'Bearer',
        'expires_at' => Carbon::parse(
            $token->token->expires_at
        )->toDateTimeString(),
      ];
    }
  }

    /**
     * Register api
     *
     *
     */
  public function register(Request $request) {
    // $request->validate([
    //   'name'             => 'required',
    //   'age'              => 'required',
    //   'gender'           => 'required',
    //   'interest_ids'     => 'required',
    //   'other_gender'     => 'required',
    // ]);

    $user_id          = $request->user_id;
    $user             = User::findOrFail($user_id);
    $name             = $request->name;
    $age              = $request->age;
    $gender           = $request->gender;
    $interest_ids     = $request->interest_ids;
    $job_title        = $request->job_title;
    $update = [];

    // update user name gender age
    if ($name) {
      $update = array_merge($update, [
        'name'              => $name,
        'age'               => $age,
        'gender'            => $gender,
        'profile_step'      => 4
      ]);
    }

    // interest
    if ($interest_ids) {
      $myPlan = $user->plans()->first(); //UserPlan::getUserPlan($user);
      $select_plan = Plan::free();
      if (!$myPlan && $select_plan) {
        //----------insert plan
        $user_plan = $user->plans()->create([
          'plan_id'           => $select_plan->id,
          'transaction_id'    => 0,
          'no_contact_use'    => $select_plan->no_of_contact,
          'start_plan_date'   => date("Y-m-d"),
          'end_plan_date'     => date('Y-m-d', strtotime("+30 days")),
        ]);
      }

      $user->interests()->attach($interest_ids);
      $update = array_merge($update, [
        'profile_step'        => 5,
      ]);
    }

    if ($job_title) {
      $update = array_merge($update, [
        'profile_step'        => 6,
      ]);

      $user->updateMeta('job_title', $job_title, function() use($user, $update){
        $user->update($update);
      });
    } else {
      $user->update($update);
    }

    return ['status' => true, 'user'=>$user, 'msg' =>trans('messages.msg_signup_succes'),'notifications' => []];
  }

  public function updatePassCode(Request $request){
    $user = $request->user();
    $passcode = request('passcode');

    $user = Setting::updateSetting($user, 'passcode', $passcode, function($user){
      if ($user && $user->profile_step < 7) {
        $user->profile_step = 7;
        $user->save();
      }
    });

    return ['status' => !!$user, 'msg' => 'Update Successful', 'user' => $user];
  }

  public function complete_signup(Request $request){
    $request->validate([
      'user_id'     => 'required',
      'image'       => 'required',
      'type'        => 'required',
    ]);

    $user_id = $request->user_id;
    $image = $request->image;
    $type = $request->type;

    $user = User::find($user_id);

    if ($user) {
      try {
        $media_file = Media::uploadImage($image);
        // $user->uploadImage($image, $type, false, 'Joined WhatsNum');
        $user->uploadImage($media_file, $type, false, 'Joined WhatsNum');
        $user->profile_step = 100;
        $user->save();
        $user->myDetails();
        // if ($type == 'profile') {
        //   BroadcastNewUser::dispatch($user);
        //   // event(new \App\Events\NewUser($user));
          $notificationData = $this->notificationData($user);
          $notifications[] = Notification::getNotificationData($notificationData);
        // }
        // $user->image_upload($image, $type);
        return ['status'=>true,'msg'=>trans('messages.msg_update_profile'),'user'=>$user, 'notifications' => $notifications];
      } catch (\Exception $e) {
        return $this->err($e);
      }
    } else {
      $user = User::find($user_id);
      if ($user) {
        $active_status = $user->checkAccountDeleteDeactivate();
        if($active_status == 'deactivate'){
          $record=array('success'=>'false','msg' =>array(trans('messages.msg_account_delete')), 'account_delete_status'=>'yes');
          return($record);
        }

        //----------------------------- check account activate or not ----------------------
        $active_status = $user->checkAccountActivateDeactivate();
        if($active_status == 'deactivate'){
        	$record=array('success'=>'false','msg' =>array(trans('messages.msg_account_deactive')), 'account_active_status'=>$active_status);
        	return ($record);
        }

      }
      return ['success'=>'false','msg'=>array(trans('messages.msg_user_id_not_exist'))];
    }
  }

  public function like(Request $request, User $other_user){
    // $other_user = User::findOrFail($request->other_user_id);
    $user = $request->user();

    // check duplicate
    $sent = $user->sent_notifications()->where('action', 'like_profile')->where('other_user_id', $other_user->id)->first();
    if ($sent) {
      return ['status' => false, 'msg' => 'Already Liked User Profile'];
    }

    $notifications[] = Notification::makeLikeProfile($user, $other_user);

    return ['notifications' => $notifications, 'status' => !!$notifications, 'msg' => $notifications ? 'Profile Liked' : 'Error Occured'];
  }

  public function index(Request $request){
    $request->validate([
      'search'    => 'string',
      'pageSize'  => 'int',
      'location'  => 'string',
      'orderBy'   => 'string',
    ]);
    $user = $request->user();

    $users = $user->Users($request)->paginate($request->pageSize ? $request->pageSize : 20);
    $users->map(function($u) use($user){
      $u->withUserRequestStatus($user);
    });
    return ['status' => true, 'users' => $users];
  }

  public function latlng(Request $request){
    $user = $request->user();
    // $user = User::find(2801);
    $users = $user->Users($request)->get()->map->only(['lat', 'lng']);
    return ['status' => true, 'users' => $users];
  }

  public function update(Request $request) {
    $user = $request->user();
    $updates = $request->all();
    $keys = array_keys($updates);
    $settings = ['job_title', 'activity_privacy', 'relationship_status', 'education', 'company'];
    $arr = array_intersect($keys, $settings);
    if ($arr) {
      foreach ($arr as $value) {
        $name = $value;
        break;
      }
      $value = $request->$name;

      $update = UserMeta::updateMeta($user, $name, $value);
    } else {
      $update = $user->update($updates);
      $user->myDetails();
    }
    return response()->json([
        'status' => !!$update,
        'user' => $user
    ], 201);
  }

  //
  public function notificationData($user){
    //------------------------------- Notification array -----------------------
    return [
      'message' => [
        'user_name'    => $user->name,
        'action'       => 'signup',
        'action_id'    => '0',
        'title'        => 'Welcome',
        'message'      => 'Welcome to the WhatsNum Community, Add new friends, add or join groups.',
      ],
      'action_data'  => [
        'user_id'       => $user->id,
        'other_user_id' => $user->id,
        'action_id'     => 0,
        'action'        => 'signup'
      ],
    ];
  }
  //
  // public function authImageUpload(Request $request){
  //   return $this->image_upload($request);
  // }
  //
  public function uploadImage(Request $request){
    $request->validate([
      'image'         => 'required',
      'type'          => 'required',
    ]);

    $image    = $request->image;
    $type     = $request->type;

    $user = $request->user();

    if ($user) {
      if(is_array($image)){
        $images = [];
        foreach ($image as $key => $value) {
          $media_file = Media::uploadImage($value);
          $images[] = $user->uploadImage($media_file, "images", $request, true);
        }
        $image = $images;
        $photos['images'] = [];
        foreach ($image as $media) {
          $photos['images'][] = $media['images'];
        }
        // Activity::addNew('images', $request, $user, $photos);
      } else {
        $media_file = Media::uploadImage($image);
        $image = ($user->uploadImage($media_file, $type, $request))['images'];
        // Activity::addNew('profile', $request, $user, $image);
      }
      if ($user) {
        $user->myDetails();
      }
      return ['status'=>true,'msg'=>trans('messages.msg_update_profile'),'user'=>$user, 'image' => is_array($image) ? $image : $image, 'type' => $type];
    }

    return ['status'=>false,'msg'=>trans('messages.msg_user_id')];
  }

  public function show(Request $request, User $user){
    $otherUser = $user->myDetails();
    $user = $request->user();
    $otherUser->load('interests')->withUserRequestStatus($user)
    ->withInterestsCount($user)
    ->withDistatnce($user)
    ->withMessageImageCount($user);

    return ['success'=> true, 'otherUser'=>$otherUser];
  }

  public function attemptRequest(Request $request, User $otherUser){
    $user = $request->user();
    $action = $request->action;
    $user_request = $otherUser->request()->where('status', 'pending')->where('other_user_id', $user->id)->first();
    if($user_request){
      $update = $user_request->update(['status' => $action == true ? 'accepted' : 'rejected']);
      if ($update && $action == true) {
        // create contact
        $otherUser->contacts()->create(['other_user_id' => $user->id]);
      }
      $msg = $update ? trans('msg.updated') : trans('msg.not_updated');
      return ['status' => $update, 'msg' => $msg, 'user_request' => $user_request];
    } else {
      return ['status' => false, 'msg' => trans('msg.no_request')];
    }
  }

  public function cancelRequest(Request $request, User $otherUser){
    $user = $request->user();
    $user_request = $user->request()->where('status', 'pending')->where('other_user_id', $otherUser->id)->first();
    if($user_request){
      $deleted = $user_request->delete();
      $msg = $deleted ? trans('msg.deleted') : trans('msg.not_deleted');
      return ['status' => $deleted, 'msg' => $msg, 'user_request' => $user_request];
    } else {
      return ['status' => false, 'msg' => trans('msg.no_request_pending')];
    }
  }


  public function toggleDirectMessage(Request $request){
    $request->validate(['bool' => 'required']);
    return ['user' => $request->user()->toggleDirectMessage($request->bool)];
  }
  //
}
