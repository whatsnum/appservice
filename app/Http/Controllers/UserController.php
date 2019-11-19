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

  		$notification =  Notification::DeviceTokenStore_1_Signal($user, $device_type, $player_id);

      $token =  $user->createToken('MyApp')->accessToken;
      return ['status' => true, 'msg' => trans('messages.msg_phone_inserted'), 'token' => $token,'notification'=>$notification,'user'=>$user->myDetails()];

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

      return array('status'=>true,'msg' =>trans('messages.msg_phone_inserted'), 'user'=>$user);
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
    $other_gender     = $request->other_user_gender;
    $min_age          = 17;//$request->other_user_min_age;
    $max_age          = 70;//$request->other_user_max_age;
    $interest         = 'New Friends';//$request->interest;
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
    if ($interest || $min_age) {
      $myPlan = UserPlan::getUserPlan($user);
      $select_plan = Plan::free();
      if (!$myPlan) {
        if ($select_plan) {
          //----------insert plan
          $user_plan = $user->plan()->create([
            'plan_id'           => $select_plan->id,
            'transaction_id'    => 0,
            'no_contact_use'    => $select_plan->no_of_contact,
            'start_plan_date'   => date("Y-m-d"),
            'end_plan_date'     => date('Y-m-d', strtotime("+30 days")),
          ]);
        }
      }
      $update = array_merge($update, [
        'other_user_min_age'  => 17,
        'other_user_max_age'  => 70,
        'other_user_gender'   => 'both',
        'profile_step'        => 5,
      ]);
    }

    if ($interest_ids) {
      $user->interests()->attach($interest_ids);
      $update = array_merge($update, [
        'profile_step'        => 5,
      ]);
    }

    $user->update($update);

    return ['status' => true,'user'=>$user, 'msg' =>trans('messages.msg_signup_succes'),'notifications' =>[]];
  }

  public function updatePassCode(Request $request){
    $user = $request->user;
    $passcode = request('passcode');

    $user = Setting::updateSetting($user, 'passcode', $passcode, function($user){
      if ($user && $user->profile_step < 6) {
        $user->profile_step = 6;
        $user->save();
      }
    });

    return ['status' => !!$user, 'msg' => 'Update Successful', 'user' => $user];
  }

  public function complete_signup(Request $request){
    $request->validate([
      'user_id'       => 'required',
      'image'       => 'required',
      'type'       => 'required',
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
        $user->profile_step = 7;
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

  // public function validIds(Request $request){
  //   $request->validate([
  //     'ids' => 'required|array',
  //   ]);
  //
  //   return User::whereIn('user_id', $request->ids)->pluck('user_id');
  // }
  //
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
  //
  // public function regard(Request $request){
  //   $request->validate([
  //     'other_user_id' => 'required',
  //     'message' => 'required|min:5|max:99',
  //   ]);
  //
  //   $other_user = User::findOrFail($request->other_user_id);
  //   $user = $request->user;
  //   $message = $request->message;
  //
  //   $notification_arr[] = UserNotification::makeRegard($request->user, $other_user, $message);
  //
  //   return ['notification_arr' => $notification_arr, 'status' => true, 'msg' => 'Regard Sent'];
  // }
  //
  // public function count(){
  //   $count = User::count();
  //   return ['status' => true, 'count' => $count];
  // }
  //
  // public function delete(Request $request){
  //   $request->validate([
  //     'reason' => 'required',
  //   ]);
  //
  //   $user = $request->user;
  //   $reason = $request->reason;
  //   //
  //   Message::create([
  //     'user_name' => $user->name,
  //     'message' => $reason,
  //     'type' => 'account_deletion',
  //   ]);
  //
  //   $user->deleteMyActivities();
  //   $user->forceDelete();
  //
  //   return ['status' => true, 'msg' => 'Account Deleted Successfully'];
  // }
  //
  // public function index(Request $request){
  //   $user = $request->user;
  //   $users = $user->Users($request)->paginate($request->pageSize ? $request->pageSize : 20);
  //   $user->usersWithRequestPhoto($users->items());
  //   return ['status' => true, 'users' => $users];
  // }
  //

  //

  //
  // // private function iAmGood(){
  // //   if ($this->complete_profile == 'yes' && $this->profile_status === ) {
  // //     return true;
  // //   }
  // //   if ($this->name && $this->interest && ) {
  // //     return true;
  // //   }
  // //   return false;
  // // }
  // /**
  //  * details api
  //  *
  //  *
  //  */
  // public function details() {
  //   $user = Auth::user();
  //   return response()->json(['success' => $user], $this-> successStatus);
  // }
  //
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
        Activity::addNew('images', $request, $user, $photos);
      } else {
        $media_file = Media::uploadImage($image);
        $image = ($user->uploadImage($media_file, $type, $request))['images'];
        Activity::addNew('profile', $request, $user, $image);
      }
      if ($user) {
        $user->myDetails();
      }
      return ['status'=>true,'msg'=>trans('messages.msg_update_profile'),'user'=>$user, 'image' => is_array($image) ? $image : $image, 'type' => $type];
    }

    return ['status'=>false,'msg'=>trans('messages.msg_user_id')];
  }
  //
  // public function delete_upload_image(Request $request){
  //   $validate = $this->validates($request, [
  //     'image_type'   => 'required',
  //   ]);
  //   if (!$validate['success']) {
  //     return $validate;
  //   }
  //
  //   $user_id = $request->user_id;
  //   $image_type = $request->image_type;
  //   $user = $request->user;
  //   $status = NULL;
  //   $index = $request->index;
  //
  //   $delete_photo = $user->deletePhoto($image_type, $index);
  //   if ($delete_photo) {
  //     $user_details = User::getUserDetails($user->user_id);
  //     $image_details = [];
  //     // $user->getAllUserImage();
  //     return array('success'=>'true','msg' =>array(trans('messages.msg_image_deleted')),'user_details'=>$user_details,'image_arr'=>$image_details);
  //   } else {
  //     return array('success'=>'false','msg' =>array(trans('messages.msg_file_error')));
  //   }
  // }
  //
  // public function swap_profile_image(Request $request){
  //   $validate = $this->validates($request, [
  //     'image_type'   => 'required',
  //   ]);
  //   if (!$validate['success']) {
  //     return $validate;
  //   }
  //   $image_type = $request->image_type;
  //   $user = $request->user;
  //
  //   $swap = $user->swapProfile($image_type);
  //   if ($swap) {
  //     return array('success'=>'true','msg' =>array(trans('messages.msg_image_deleted')),'user_details'=>$user->myDetails());
  //   } else {
  //     return array('success'=>'false','msg' =>array(trans('messages.msg_file_error')));
  //   }
  // }
  //
  // public function update_user_details(Request $request){
  //   $validator = Validator::make($request->all(), [
  //     'user_id'       => 'required',
  //   ]);
  //
  //   $user_id  = $request->user_id;
  //   $user = User::getActivatedUser($user_id);
  //
  //   if ($user) {
  //     $fillable = $user->getMyFillable();
  //     try {
  //       foreach ($fillable as $field) {
  //         switch ($field) {
  //           case 'min_age':
  //             $field = 'other_user_min_age';
  //             break;
  //           case 'max_age':
  //             $field = 'other_user_max_age';
  //             break;
  //         }
  //         if ($value = $request->$field) {
  //           $user->$field = $value;
  //         }
  //       }
  //       $user->save();
  //       $user = $user->getUserDetails($user->user_id);
  //     } catch (\Exception $e) {
  //       return $this->err($e);
  //     }
  //
  //     return ['success'=>'true','msg' =>array(trans('messages.msg_update_profile')),'user_details'=>$user];
  //   } else {
  //     return ['success'=>'false','msg'=>array(trans('messages.msg_user_id_not_exist'))];
  //   }
  // }
  //
  // public function get_my_receive_request(Request $request){
  //   $user_requests = UserRequest::myRequests($request->user);
  //   $user = $request->user;
  //   if($user_requests) {
  //     $user_requests->map(function ($req) use($user){
  //       $other_user    = User::find($req->user_id);
  //       if ($other_user) {
  //         $req->name     = $other_user->name;
  //         $req->image    = $other_user->image;
  //         $req->age      = $other_user->age;
  //         $req->verified = $other_user->user_verfication;
  //         $req->images   = $other_user->withProfileUrl()->images;
  //         $req->request_detail   = $other_user->withUserRequestStatus($user)->request_detail;
  //       } else {
  //         $req->name     = null;
  //         $req->image    = null;
  //         $req->age      = null;
  //         $req->verified = null;
  //         $req->images   = [];
  //         $req->request_detail   = 'no';
  //       }
  //     });
  //
  //     if(!$user_requests){
  //         $user_requests='NA';
  //     }
  //     return array('success'=>'true', 'msg' =>'request get successfully', 'users'=>$user_requests);
  //   } else {
  //     return ['success'=>'false','msg'=>array(trans('messages.msg_no_data_found'))];
  //   }
  // }
  //
  // public function get_my_pending_sent_requests(Request $request){
  //   $user = $request->user;
  //   $user_sent_requests = UserRequest::mySentRequests($user);
  //   if ($user_sent_requests) {
  //     $user_sent_requests->map(function ($req){
  //       $other_user_data = User::find($req->other_user_id);
  //       if ($other_user_data) {
  //         $req->name     = $other_user_data->name;
  //         $req->image    = $other_user_data->image;
  //         $req->age      = $other_user_data->age;
  //         $req->verified = $other_user_data->user_verfication;
  //       }
  //     });
  //
  //     return array('success'=>'true', 'msg' =>'request get successfully', 'users'=>$user_sent_requests);
  //   } else {
  //     return ['success'=>'false','msg'=>array(trans('messages.msg_no_data_found'))];
  //   }
  //
  // }
  //
  // public function get_my_whatsnum_contacts(Request $request) {
  //   $get_all_request_arr=array();
  //   $user = $request->user;
  //   $user_contacts = UserRequest::myContacts($user);
  //   if ($user_contacts) {
  //     $other_user_id_arr = array();
  //     $user_contacts->map(function ($contact) use($user, &$other_user_id_arr) {
  //       if($contact->user_id == $user->user_id){
  //           $contact->other_user_id = $contact->other_user_id;
  //       } else if($contact->other_user_id == $user->user_id){
  //           $contact->other_user_id = $contact->user_id;
  //       }
  //
  //       // $contact->request_id =
  //       if(!in_array($contact->other_user_id,$other_user_id_arr)){
  //         $other_user_id_arr[]=$contact->other_user_id;
  //         $other_user_data = User::find($contact->other_user_id);
  //         if($other_user_data){
  //           $contact->name = $other_user_data->name;
  //           $contact->image = $other_user_data->image;
  //           $contact->age = $other_user_data->age;
  //           $contact->images = $other_user_data->withProfileUrl()->images;
  //         }
  //       }
  //     });
  //     $no_of_contact = $user_contacts->count();
  //   } else {
  //     $user_contacts='NA';
  //     $no_of_contact = 0;
  //   }
  //   return array('success'=>'true','msg' =>'request get successfully','contacts_arr'=>$user_contacts,'count'=>$no_of_contact);
  // }
  //
  // public function find_friends_map(Request $request){
  //   $request->validate([
  //     // 'user_id'       => 'required',
  //   ]);
  //
  //   $user           = $request->user;
  //   $orderBy        = $request->orderBy;
  //   $pageSize       = $request->pageSize;
  //
  //   $stmt = $user->Users($request);
  //
  //   $users = $stmt->paginate($request->pageSize ? $request->pageSize : null);
  //   // ->orderBy(($request->orderBy ? $request->orderBy : 'created_at'), 'DESC')
  //
  //
  //   if ($users) {
  //     $users->map(function (&$user) use($request) {
  //       $user->myDetails();
  //       $user->withUserRequestStatus($request->user);
  //     });
  //   } else {
  //     $users = 'NA';
  //     return array('success'=>'false');
  //   }
  //
  //   return array('success'=>'true','msg' =>array(trans('messages.msg_data_found')),'users'=>$users, 'count' => $users->count());
  // }
  //
  // public function user_details(Request $request){
  //   $request->validate([
  //     'other_user_id'   => 'required',
  //   ]);
  //
  //   $user = $request->user;
  //   $user_id = $request->user_id;
  //   $other_user_id = $request->other_user_id;
  //
  //   $user_detail_arr = $user->getDetails($other_user_id);
  //   if(!$user_detail_arr || $user_detail_arr == 'NA'){
  //       $user_detail_arr='NA';
  //   } else {
  //     $user_detail_arr->withUserRequestStatus($request->user);
  //   }
  //
  //   return array('success'=> $user_detail_arr != "NA", 'user_detail_arr'=>$user_detail_arr, 'ads' => [], 'ad_count' => null);
  // }
  //
  // public function search_modal(Request $request){
  //   $validate = $this->validates($request, [
  //     // 'other_user_id'   => 'required',
  //     // 'state'           => 'required',
  //   ]);
  //
  //   if (!$validate['success']) {
  //     return $validate;
  //   }
  //
  //   $user_id = $request->user_id;
  //   $state = $request->state;
  //   $gender = $request->gender;
  //   $other_gender = $request->other_gender;
  //   $location = strtolower($request->location);
  //   $min_age = $request->min_age;
  //   $max_age = $request->max_age;
  //   $country = $request->country;
  //   $searchQuery = $request->searchQuery;
  //   $searchLocationTerm = '%' . $searchQuery . '%';
  //   $friends = User::where("user_id", "!=", $user_id)->where("user_type", 'user')->where('country', $country)
  //   ->where('other_user_min_age', '>=', $min_age)->where('other_user_max_age', '<=', $max_age)
  //   ->where("delete_flag", 'no')->where('active_flag', 'active')->where('complete_profile', 'yes')
  //   ->orderBy('user_id', 'ASC');
  //   // ->where('other_user_min_age', '>=', $min_age)
  //   // ->where('other_user_max_age', '<=', $max_age)->where('state', $state)->where('country', $country)
  //   // ->where("user_type", 'user')->where("delete_flag", 'no')->where('active_flag', 'active')->where('complete_profile', 'yes')
  //   // ->orderBy('user_id', 'ASC');
  //
  //   if (!$location) {
  //     $friends->where('state', $state);
  //     if ($other_gender === 'both') {
  //       $friends->where(function($q){
  //         $q->where('gender', 'male')->orWhere('gender', 'female');
  //       })->where(function($q) use($gender){
  //         $q->where('other_user_gender', $gender)->orWhere('other_user_gender', 'both');
  //       });
  //     } else {
  //       $friends->where('gender', $other_gender)->where(function($q) use($gender){
  //         $q->where('other_user_gender', $gender)->orWhere('other_user_gender', 'both');
  //       });
  //     }
  //   }
  //   else
  //   {
  //     $friends->whereBetween('gender', ['male', 'female'])->where(function($q) use($searchLocationTerm){
  //       $q->where('city', 'like', $searchLocationTerm)->orWhere('state', 'like', $searchLocationTerm);
  //     });
  //     if ($other_gender === 'both') {
  //       $friends->whereBetween('other_user_gender', [$gender, 'both']);
  //     } else {
  //       $friends->where('gender', $other_gender)->whereBetween('other_user_gender', [$gender, 'both']);
  //     }
  //   }
  //   $friends = $friends->get();
  //   if ($friends) {
  //     foreach ($friends as $friend) {
  //       if (!$friend->image) {
  //           $friend->image = 'NA';
  //       };
  //     }
  //     return array('success'=>'true', 'users' => $friends, 'count' => $friends->count());
  //   } else {
  //     return array('success'=>'false');
  //   }
  // }
  //
  // public function search_location(Request $request){
  //   $location = $request->location;
  //   $user_id = $request->user_id;
  //   $searchLocationTerm = '%' . $location . '%';
  //   $friends = User::where("user_id", "!=", $user_id)->where("user_type", 'user')
  //   ->where('city', 'like', $searchLocationTerm)->orWhere('state', 'like', $searchLocationTerm)
  //   ->where("delete_flag", 'no')->where('active_flag', 'active')->where('complete_profile', 'yes')
  //   ->orderBy('user_id', 'ASC')->get();
  //   if ($friends) {
  //     foreach ($friends as $friend) {
  //       if (!$friend->image) {
  //           $friend->image = 'NA';
  //       };
  //     }
  //     return array('success'=>'true', 'users' => $friends, 'count' => $friends->count());
  //   } else {
  //     return array('success'=>'false');
  //   }
  // }
  //
  public function toggleDirectMessage(Request $request){
    $request->validate(['bool' => 'required']);
    return ['user' => $request->user()->toggleDirectMessage($request->bool)];
  }
  //
}
