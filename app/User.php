<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Laravel\Passport\HasApiTokens;
use Spatie\Image\Image;

// use App\UserImage;
// use App\Plan;
// use App\UserPlan;
// use App\Countries;
// use App\States;
// use App\UserRequest;
// use App\Group;
use App\Activity;
// use Carbon\Carbon;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, Notifiable, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
        'phone','phone_code','lat','lng','city','state','age','country','plan_id',
        'gender','interest','other_user_gender','other_user_max_age','other_user_min_age',
        'profile_step','user_verfication'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function notifications(){
    //   return $this->hasMany(Notification::class);
    // }

    public static function generateRandomOTP($length = 6) {
      PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    	$hash = '';
    	$chars = '123456789';
    	$max = strlen($chars) - 1;
    	For($i = 0; $i < $length; $i++) {
    		$hash .= $chars[mt_rand(0, $max)];
    	}
        return $hash;
    }

    public static function getUserDetails($user_id, $user = false){
      $self = self::where('user_id', $user_id)->where('otp_verify', 'yes')->where('delete_flag', 'no')->first();
      if ($self) {
        $self->load(['settings', 'plan']);
        $self//->withPhotoUrl()
        // ->withFeedsPhotoUrl()
        ->withMetas();
        if ($user) $self->load(['liked_profile' => function($q) use($user) {
          $q->where('user_id', $user->user_id);
        }]);

        // $plan = $self->plan();
        // $plan = Plan::getUserPlanId($self->current_plan_id);
        // if ($plan) {
        //   $self->plan_name = $plan->plan_name;
        //   $self->plan_sub_name = $plan->plan_sub_name;
        //   $self->plan_contact = $plan->no_of_contact;
        // }
        return $self;
      }
      return $self;
    }

    public function getDetails($other_user_id){
      return self::getUserDetails($other_user_id, $this);
    }

    public function myDetails(){
      $this->load(['settings', 'plan']);
      // $this->withPhotoUrl()->withFeedsPhotoUrl();
      $this->withMetas();
      // $plan = $this->plan();

      // Plan::getUserPlanId($this->current_plan_id);
      // if ($plan) {
      //   $this->plan_name = $plan->plan_name;
      //   $this->plan_sub_name = $plan->plan_sub_name;
      //   $this->plan_contact = $plan->no_of_contact;
      // }
      // $this->makeHidden(['media', 'metas', 'mysqltime', 'image1', 'image2', 'image3', 'image4', 'image5', 'email']);
      return $this;
    }

    public function withPhotoUrl(){
      $images = new \stdClass();
      $types = ['cover', 'avatar', 'images'];

      foreach ($types as $image) {
        if ($image === 'images') {
          $medias = $this->getMedia($image);
          $imgs = [];
          foreach ($medias as $media) {
            // $width = Image::load($media->getPath())->getWidth();
            // $height = Image::load($media->getPath())->getHeight();
            $obj = new \stdClass();
            $obj->thumb = $media->getUrl('thumb');
            $obj->photo = $media->getUrl();
            $imgs[] = $obj;
            // $obj->width = $width;
            // $obj->height = $height;
          }
          $images->$image = $imgs;
        } else {
          $media = $this->getFirstMedia($image);
          if ($media) {
            $thumb = $media->getUrl('thumb');
            $image_url = $media->getUrl();
            // $width = Image::load($media->getPath())->getWidth();
            // $height = Image::load($media->getPath())->getHeight();
            $obj = new \stdClass();
            $obj->thumb = $thumb;
            $obj->photo = $image_url;
            $images->$image = $obj;
            // $obj->width = $width;
            // $obj->height = $height;
          } else {
            if ($image === 'images') {
              $images->$image = [];
            } else {
              $obj = new \stdClass();
              $obj->thumb = '';
              $obj->photo = '';
              $images->$image = $obj;
            }
          }
        }
      }
      $this->images = $images;
      // dd($this);
      return $this;
    }

    public function withFeedsPhotoUrl(){

      $feeds = Activity::where('type', 'profile')->whereHas('user', function($q){
        $q->where('user_id', $this->user_id);
      })->orWhere('type', 'post')
      ->whereHas('post.author', function($q){
        $q->where('user_id', $this->user_id);
      })
      ->where('created_at', '>=', Carbon::now()->subDays(14)->toDateTimeString())
      ->get();

      $items = [];
      // $this->load(['feeds.post', 'feeds.author'])->whereHas;
      $feeds->map(function($feed) use(&$items){
        // $feed->text = null;
        if ($feed->type == 'post') {
          $imgs = $feed->post->withPhotoUrl();
          foreach ($imgs->images as $img) {
            $items[] = $img;
          }
        } else {
          $imgs = $feed->user->withProfileUrl();
          foreach ($imgs->images as $img) {
            $items[] = $img;
          }
        }
      });

      $this->feedImages = $items;
      // dd($this);
      return $this;
    }

    public function withMetas(){
      $metas = $this->load('metas');
      // $metas->metas->map(function ($meta) {
      //   $name = $meta->name;
      //   $this->$name = $meta->value;
      // });
      return $this;
    }

    public function settings(){
      return $this->hasMany(Setting::class);
    }

    public function notifications(){
      return $this->morphMany(Notification::class, 'notifiable');
    }

    public function uploadImage($media_file, $type, $request = false, $action = false){
      $file_name = rand();
      switch ($type) {
        case 'profile':
          $name = 'image';
          // $this->$name = $media_file['file_name'];
          $collection = 'avatar';
          break;
        case 'cover':
          $name = 'cover';
          // $this->$name = $file_name;
          $collection = 'cover';
          break;
        case 'images':
          $name = 'images';
          // $this->$name = $file_name;
          $collection = 'images';
          break;
        default:
          // $this->$type = $file_name;
          $name = $type;
          $collection = $type;
          break;
      }

      // $this->addMediaFromBase64($media_file)->usingName($name)->usingFileName($file_name)->toMediaCollection($collection);
      $this->addMedia($media_file)->usingName($name)->usingFileName($file_name)->toMediaCollection($collection);
      // if ($type != 'cover') {
      //   $this->save();
      // }
      $media = $this->getFirstMedia($collection);
      $thumb = $media->getUrl('thumb');
      $image_url = $media->getUrl();

      // if(!$action && ($type == 'profile')){
      //   $activity = Activity::addNew('profile', $request, $this->user_id, $image_url, $action);
      // }

      return ['success' => true, 'images' => $image_url, 'image' => $file_name, 'thumb' => $thumb];
    }

    // public static function distance($query, $latitude, $longitude){
    //   $latName = 'lat';
    //   $lonName = 'lng';
    //   $calc = 1.1515 * 1.609344;
    //
    //   $sql = "((ACOS(SIN($latitude * PI() / 180) * SIN($latName * PI() / 180) + COS($latitude * PI() / 180) * COS($latName * PI() / 180) * COS(($longitude - $lonName ) * PI() / 180)) * 180 / PI()) * 60 * $calc) as distance";
    //   $query->selectRaw("*, ".$sql);
    //   return $query;
    // }
    //
    // public function deleteMyActivities(){
    //   $activities = $this->activities;
    //   $activities->map(function($a){
    //     $a->forceDelete();
    //   });
    //   $posts = $this->posts;
    //   $posts->map(function($p){
    //     $p->activity->forceDelete();
    //     $p->forceDelete();
    //   });
    //   return [$posts, $activities];
    //   // $activities->forceDelete();
    //   // $this->activities()->forceDelete();
    // }
    //
    // private function withSearch($search, $stmt){
    //   if ($search) {
    //     // $stmt->search($search);
    //     $stmt->where('name', 'LIKE', '%'.$search.'%')
    //     ->orWhere('gender', 'LIKE', '%'.$search.'%')
    //     ->orWhere('phone', 'LIKE', '%'.$search.'%')
    //     ->orWhere('interest', 'LIKE', '%'.$search.'%')
    //     ->orWhere('state', 'LIKE', '%'.$search.'%')
    //     ->orWhere('city', 'LIKE', '%'.$search.'%')
    //     ->orWhere('country', 'LIKE', '%'.$search.'%');
    //   }
    // }
    //
    // public function withFilters($request, $stmt){
    //   $state                = $request->state; //$this->state;
    //   $country              = $request->country; //$this->country;
    //   $direct_message       = $request->direct_message;
    //   $interest             = $request->interest;
    //   $relationship_status  = $request->relationship_status;
    //
    //   if ($state) {
    //     $stmt->where('state', $state);
    //   }
    //   if ($country) {
    //     $stmt->where('country', $country);
    //   }
    //   if ($direct_message) {
    //     $stmt->where('direct_message', true);
    //   }
    //   if ($relationship_status) {
    //     $stmt->whereHas('user_metas', function($q) use($relationship_status){
    //       $q->where('name', 'relationship_status')->where('value', $relationship_status);
    //     });
    //   }
    //   if ($interest) {
    //     $stmt->where('interest', $interest);
    //   }
    //
    //   return $stmt;
    // }
    //
    // public function Users($request){
    //   $gender         = $this->gender;
    //   $other_gender   = $this->other_user_gender;
    //   $min_age        = $this->other_user_min_age;
    //   $max_age        = $this->other_user_max_age;
    //
    //   $search         = $request->search;
    //
    //   $lat            = $this->lat;
    //   $lng            = $this->lng;
    //
    //   $stmt = User::whereBetween('age', [$min_age, $max_age])->where('user_id', '!=', $this->user_id)
    //   ->where('user_type', 'user')->where('delete_flag', 'no')->where('active_flag', 'active')
    //   ->where('complete_profile', 'yes')
    //   ->where('lat', '!=', NULL)->where('lng', '!=', NULL);
    //
    //   $this->withFilters($request, $stmt);
    //
    //   $stmt = User::distance($stmt, (string)$lat, (string)$lng)
    //   ->orderBy('distance', 'ASC');
    //
    //   $this->withSearch($search, $stmt);
    //
    //   if ($other_gender === 'both') {
    //     $stmt->whereIn('other_user_gender', [$gender, 'both']);
    //   } else {
    //     $stmt->where('gender', $other_gender)->whereIn('other_user_gender', ['both', $gender]);
    //   }
    //   return $stmt;
    // }
    //
    // public function usersWithRequestPhoto($users){
    //   if ($users) {
    //     foreach($users as $user){
    //       $user = $user->withCover()->withProfileUrl()->withUserRequestStatus($this);
    //       $user->joined_interval = Activity::time_elapsed_string($user->createtime);
    //     }
    //   }
    // }
    //
    // public function newUsers($request){
    //   $gender         = $this->gender;
    //   $other_gender   = $this->other_user_gender;
    //   $state          = $this->state;
    //   $country        = $this->country;
    //   $min_age        = $this->other_user_min_age;
    //   $max_age        = $this->other_user_max_age;
    //
    //   $search         = $request->search;
    //   $orderBy        = $request->orderBy;
    //   $pageSize       = $request->pageSize;
    //
    //   $stmt = $this->whereBetween('age', [$min_age, $max_age])->where('user_id', '!=', $this->user_id)
    //   ->where('user_type', 'user')->where('delete_flag', 'no')->where('active_flag', 'active')
    //   ->where('complete_profile', 'yes')->where('state', $state)->where('country', $country)
    //   ->where('createtime', '>=', Carbon::now()->subDays(1)->toDateTimeString());
    //
    //   $this->withSearch($search, $stmt);
    //
    //   if ($other_gender === 'both') {
    //     $stmt->whereIn('other_user_gender', [$gender, 'both']);
    //   } else {
    //     $stmt->where('gender', $other_gender)->whereIn('other_user_gender', ['both', $gender]);
    //   }
    //
    //   $users = $stmt->latest()->get();
    //   if ($users) {
    //     $users->map(function($user){
    //       $user->withCover()->withProfileUrl()->withUserRequestStatus($this);
    //       $user->joined_interval = Activity::time_elapsed_string($user->createtime);
    //     });
    //   }
    //   return $users;
    // }
    //
    // public function myGroups(){
    //   return Group::getMine($this);
    // }
    //
    // public function withLiked($q, $author = false){
    //   $relation = $author ? 'post.author.liked_profile' : 'liked_profile' ;
    //   $q->with([$relation => function($q){
    //     $q->where('user_id', $this->user_id);
    //   }]);
    //   return $q;
    // }
    //
    // public function withUserRequestStatus(User $user){
    //   $status = UserRequest::usersRequestStatus($this, $user);
    //   $this->request_detail = $status;
    //   return $this;
    // }
    //
    // public function withProfileUrl(){
    //   $media = $this->getFirstMedia('avatar');
    //   $obj = new \stdClass();
    //   if ($media) {
    //     $obj->thumb = $media->getUrl('thumb');
    //     $obj->photo = $media->getUrl();
    //   }
    //   if (isset($this->images)) {
    //     $this->images->avatar = $obj;
    //   } else {
    //     $this->images = new \stdClass();
    //     $this->images->avatar = $obj;
    //   }
    //   return $this;
    // }
    //
    // public function MyMediaUrl($collection = 'avatar', $conversion = 'thumb'){
    //   $media = $this->getFirstMedia($collection);
    //   if ($media) {
    //     return $media->getUrl($conversion);
    //   }
    //   return null;
    // }
    //
    // public function withCover(){
    //   $media = $this->getFirstMedia('cover');
    //   $obj = new \stdClass();
    //   if ($media) {
    //     $obj->thumb = $media->getUrl('thumb');
    //     $obj->photo = $media->getUrl();
    //   }
    //   if ($this->images) {
    //     $this->images->cover = $obj;
    //   } else {
    //     $this->images = new \stdClass();
    //     $this->images->cover = $obj;
    //   }
    //   return $this;
    // }
    //


    //
    public static function getNotificationStatus($user_id){
      return true;
      // self::where('user_id', $user_id)->where('notification_status', 'on')->first();
    }
    //
    // public static function getUser($user_id){
    //   return self::where('user_id', $user_id)
    //   // ->where('otp_verify', 'yes')
    //   ->where('delete_flag', 'no')->first();
    // }
    //

    //

    //

    //
    // // public function image_upload($image, $type){
    // //   // profile
    // //   $data = str_replace('data:image/png;base64,', '', $image);
    // //   $data = str_replace(' ', '+', $data);
    // //   $data = base64_decode($data);
    // //   $file_name = rand() . '.png';
    // //   $file = 'images/'.$file_name;
    // //   $success = file_put_contents(base_path().'/'.$file, $data);
    // //
    // //   try {
    // //     if ($type === 'profile') {
    // //         $this->image = $file_name;
    // //     } elseif ($type === 'image1') {
    // //       $this->image1 = $file_name;
    // //     } elseif ($type === 'image2') {
    // //       $this->image2 = $file_name;
    // //     } elseif ($type === 'image3') {
    // //       $this->image3 = $file_name;
    // //     } elseif ($type === 'image4') {
    // //       $this->image4 = $file_name;
    // //     } elseif ($type === 'image5') {
    // //       $this->image5 = $file_name;
    // //       $this->profile_status = 'step_1';
    // //       $this->complete_profile = 'yes';
    // //
    // //     }
    // //     $this->save();
    // //
    // //     return ['success' => true, 'image' => $file_name];
    // //   } catch (\Exception $e) {
    // //     throw new \Exception($e->getMessage(), $e);
    // //   }
    // // }
    //
    // public function deletePhoto($image_type, $index = false){
    //   $image_type = $image_type == 'profile' ? 'image' : $image_type;
    //   $this->$image_type = null;
    //   if ($index) {
    //     $medias = $this->getMedia($image_type);
    //     foreach ($medias as $ind => $media) {
    //       if ($index == $ind) {
    //         $media->delete();
    //       }
    //     }
    //   } else {
    //     $mdeia = $this->getFirstMedia($image_type);
    //     if ($mdeia) {
    //       $mdeia->delete();
    //     }
    //   }
    //
    //   return true;
    // }
    //
    // public function swapProfile($image_type){
    //   $image = $this->$image_type;
    //   $this->image = $image;
    //   return $this->save();
    // }
    //
    // public static function getActivatedUser($user_id){
    //   return self::where('delete_flag', 'no')->where('active_flag', 'active')
    //   ->where('user_id', $user_id)->where('otp_verify', 'yes')->where('delete_flag', 'no')->first();
    // }
    //
    // public static function stateRandomUser($state){
    //   return self::where("image", '!=', NULL)->where("image", "<>", '')->where("state",$state)
    //   ->where("user_type", 'user')->where("delete_flag", 'no')->where('active_flag', 'active')
    //   ->where('complete_profile', 'yes')->inRandomOrder()->limit(3)->get();
    // }
    //
    // public function myRandomUser(){
    //   return $this->where("user_id", '!=', $this->user_id)->where("image", '!=', NULL)->where("image", "<>", '')
    //   // ->where("state", $this->state)
    //   ->where("user_type", 'user')->where("delete_flag", 'no')->where('active_flag', 'active')
    //   ->where('complete_profile', 'yes')->inRandomOrder()->limit(10)->get();
    // }
    //
    // public static function countUsersInState($state){
    //   return self::where('state', $state)->where("user_type", 'user')->where("delete_flag", 'no')
    //   ->where('active_flag', 'active')->where('complete_profile', 'yes')->orderBy('user_id')->count()-1;
    // }
    //
    // public static function countCompleteUsers(){
    //   return self::where("user_type", 'user')->where("delete_flag", 'no')
    //   ->where('active_flag', 'active')->where('complete_profile', 'yes')->orderBy('user_id')->count()-1;
    // }
    //
    // public function getMyPlanId(){
    //   return Plan::where('delete_flag', 'no')->where('plan_id', $this->current_plan_id)->first();
    // }
    //
    // public function getMyRemaningRequest(){
    //   return $this->plan->no_contact_use;
    // }
    //
    // public function getMyPlanData(){
    //   return UserPlan::where('delete_flag', 'no')->where('user_id', $this->user_id)->latest('updatetime')->first();
    // }
    //
    // public function getMyTodayRequest(){
    //   $check_request_data = UserRequest::where('delete_flag', 'no')->where('user_id', $this->user_id)->orderBy('request_id', 'DESC')->get();
    //   $check_request_data_num_row = $check_request_data->count();
    // 	return $check_request_data_num_row;
    // }
    //
    // public function toggleDirectMessage(){
    //   $this->direct_message = !$this->direct_message;
    //   $this->save();
    //   return $this->myDetails();
    // }
    //
    public function registerMediaCollections(Media $media = null){
      $this->addMediaCollection('avatar')
      ->acceptsFile(function (File $file) {
        return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
      })->singleFile()->useDisk('user_avatars');

      $this->addMediaCollection('images')
      ->acceptsFile(function (File $file) {
        return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
      })->useDisk('user_galleries');

      $this->addMediaCollection('cover')
      ->acceptsFile(function (File $file) {
        return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
      })->singleFile()->useDisk('user_covers');
    }

    public function registerMediaConversions(Media $media = null){
      $this->addMediaConversion('thumb')
      ->width(368)->height(232)//->sharpen(10)
      // ->withResponsiveImages()
      ->performOnCollections('avatar', 'cover', 'images');

      $this->addMediaConversion('medium')
      ->width(400)->height(400)//->sharpen(10)
      // ->withResponsiveImages()
      ->performOnCollections('avatar', 'cover', 'images');
        //'image1', 'image2', 'image3', 'image4', 'image5',
      // ->nonOptimized();
    }
    //
    public function getMyFillable(){
      return $this->fillable;
    }
    //
    //

    //
    // protected $return_fields = [
    //   'age', 'bio', 'city', 'complete_profile', 'country', 'createtime', 'current_plan_id', 'direct_message', 'distance', 'feedImages', 'gender', 'image', 'images',
    //   'interest', 'lat', 'lng', 'name', 'notification_status', 'other_user_gender', 'other_user_max_age',
    //   'other_user_min_age', 'phone', 'phone_code', 'plan_name', 'profile_status', 'request_detail', 'settings', 'state', 'updatetime', 'user_id', 'user_verfication',
    // ];
    //
    public function groups(){
      return $this->hasMany(Group::class);
    }

    public function activities(){
      return $this->hasMany(Activity::class, 'content_id');
    }

    // public function activities(){
    //   return $this->morphMany(Activity::class, 'content_id');
    // }

    public function posts(){
      return $this->hasMany(Post::class);
    }

    public function reports(){
      return $this->hasMany(PostReport::class, 'user_id');
    }

    public function reported(){
      return $this->hasMany(PostReport::class, 'other_user_id');
    }

    public function reported_profile(){
      return $this->hasMany(PostReport::class, 'content_id');
    }



    public function metas(){
      return $this->hasMany(UserMeta::class);
    }

    public function requests(){
      return $this->hasMany(UserRequest::class, 'user_id');
    }

    public function accepted_requests(){
      return $this->hasOne(UserRequest::class, 'user_id')->where('status', 'accept');
    }

    public function accept_requests(){
      return $this->hasOne(UserRequest::class, 'other_user_id')->where('status', 'accept');
    }

    public function plan(){
      return $this->hasOne(UserPlan::class)->latest();
    }

    public function plans(){
      return $this->hasMany(UserPlan::class);
    }

    public function sent_notifications(){
      return $this->hasMany(UserNotificationMessage::class, 'user_id');
    }

    public function received_notifications(){
      return $this->hasMany(UserNotificationMessage::class, 'other_user_id');
    }

    public function likes(){
      return $this->hasMany(UserNotificationMessage::class, 'other_user_id')->where('action', 'like_profile');
    }

    public function activity_privacy(){
      return $this->hasOne(UserMeta::class)->where('name', 'activity_privacy');
    }

    public function liked_profile(){
      return $this->hasOne(UserNotificationMessage::class, 'other_user_id')->where('action', 'like_profile');
    }

    public function oneSignalData(){
      return $this->morphOne(Notification::class, 'notifiable')->where('type', 'onesignal')->where('player_id', '!=', NULL)->latest()->first();
    }

    public function notification_messages(){
      return $this->hasMany(NotificationMessage::class, 'other_user_id')->latest()->get();
    }

    // public function contact(){
    //   return $this->hasOne(UserRequest::class, 'user_id')->where('status', 'accept');
    // }
    //
    // // this is a recommended way to declare event handlers
    // public static function boot() {
    //     parent::boot();
    //
    //     static::deleting(function($user) { // before delete() method call this
    //          // $user->activities()->forceDelete();
    //          $user->deleteMyActivities();
    //          // do the rest of the cleanup...
    //     });
    //
    //     // static::saved(function($user) { // before delete() method call this
    //     //   if ($user->complete_profile === 'yes') {
    //     //     return $user->getUserDetails($user->user_id);
    //     //   }
    //     // });
    //     //
    //     // static::updated(function($user) { // before delete() method call this
    //     //   if ($user->complete_profile === 'yes') {
    //     //     return $user->getUserDetails($user->user_id);
    //     //   }
    //     // });
    //
    // }
}
