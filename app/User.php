<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Image;
use Laravel\Passport\HasApiTokens;

use App\Plan;
use App\UserPlan;
use App\Countries;
use App\States;
use App\UserRequest;
use App\Group;
use App\Activity;
use App\Setting;
use App\UserMeta;
use Carbon\Carbon;

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
        'email_verified_at',
        'media', 'pivot'
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

    public function updateMeta($name, $value){
      return UserMeta::updateMeta($this, $name, $value);
    }

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
        $self->withPhotoUrl()
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
      $this->withPhotoUrl();
      $this->withJobTitle();
      $this->withMetas();
      return $this;
    }

    public function withPhotoUrl(){
      $images = new \stdClass();
      $types = ['avatar', 'images'];

      foreach ($types as $image) {
        if ($image === 'images') {
          $medias = $this->getMedia($image);
          $imgs = [];
          foreach ($medias as $media) {
            $obj = new \stdClass();
            $obj->thumb = $media->getUrl('thumb');
            $obj->photo = $media->getUrl();
            $imgs[] = $obj;
          }
          $images->$image = $imgs;
        } else {
          $media = $this->getFirstMedia($image);
          if ($media) {
            $thumb = $media->getUrl('thumb');
            $image_url = $media->getUrl();
            $obj = new \stdClass();
            $obj->thumb = $thumb;
            $obj->photo = $image_url;
            $images->$image = $obj;
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

    public function withInterestsCount(User $user = null){
      if ($user) {
        return $this->loadCount(['interests' => function($q) use($user){
          $q->whereIn('interest_id', $user->interests()->pluck('interest_id'));
        }]);
      } else {
        return $this->loadCount('interests');
      }
    }

    public function withMessageMediaCount(User $user = null){
      if ($user) {
        return $this;
      } else {
        return $this;
      }
    }

    public function uploadImage($media_file, $type, $request = false, $action = false){
      $file_name = rand();
      switch ($type) {
        case 'profile':
          $name = 'image';
          $collection = 'avatar';
          break;
        case 'cover':
          $name = 'cover';
          $collection = 'cover';
          break;
        case 'images':
          $name = 'images';
          $collection = 'images';
          break;
        default:
          $name = $type;
          $collection = $type;
          break;
      }

      $this->addMedia($media_file)->usingName($name)->usingFileName($file_name)->toMediaCollection($collection);
      $media = $this->getFirstMedia($collection);
      $thumb = $media->getUrl('thumb');
      $image_url = $media->getUrl();

      // if(!$action && ($type == 'profile')){
      //   $activity = Activity::addNew('profile', $request, $this->user_id, $image_url, $action);
      // }

      return ['success' => true, 'images' => $image_url, 'image' => $file_name, 'thumb' => $thumb];
    }

    public static function distance($query, $latitude, $longitude){
      $latName = 'lat';
      $lonName = 'lng';
      $calc = 1.1515 * 1.609344;

      $sql = "ROUND(((ACOS(SIN($latitude * PI() / 180) * SIN($latName * PI() / 180) + COS($latitude * PI() / 180) * COS($latName * PI() / 180) * COS(($longitude - $lonName ) * PI() / 180)) * 180 / PI()) * 60 * $calc)) as distance";
      $query->selectRaw("*, ".$sql);
      return $query;
    }

    public function withDistatnce(User $otherUser){
      $lat = $this->lat;
      $lng = $this->lng;

      $lati = $otherUser->lat;
      $lngi = $otherUser->lng;

      $calc = 1.1515 * 1.609344;

      $distance = round((acos(sin($lati * pi() / 180) * sin($lat * pi() / 180) + cos($lati * pi() / 180) * cos($lat * pi() / 180) * cos(($lati - $lng) * pi() / 180)) * 180 / pi()) * 60 * $calc, 0);

      $this->distance = $distance;
      return $this;
    }

    public function conversationMedias(User $user, Array $mediaType = []){
      $conversation = $this->conversations($user)->first();
      if (!$conversation) {
        return $conversation;
      }
      return $conversation->messages()->whereHas('media');
    }

    public function imageMessages(User $user){
      $msgs = $this->conversationMedias($user);
      if (!$msgs) {
        return $msgs;
      }
      return $msgs->whereHas('media_images');
    }

    public function imageMessagesCount(User $user){
      $msgs = $this->imageMessages($user);
      if (!$msgs) {
        return 0;
      }
      return $msgs->count();
    }

    public function withMessageImageCount(User $user){
      $this->message_images_count = $this->imageMessagesCount($user);
      return $this;
    }

    private function withSearch($search, $stmt){
      if ($search) {
        return $stmt->where('name', 'LIKE', '%'.$search.'%')
        ->orWhere('gender', 'LIKE', '%'.$search.'%')
        ->orWhere('state', 'LIKE', '%'.$search.'%')
        ->orWhere('city', 'LIKE', '%'.$search.'%')
        ->orWhere('country', 'LIKE', '%'.$search.'%');
      }
      return $stmt;
    }

    public function withJobTitle($stmt = false){
      if ($stmt) {
        return $stmt
        // ->with(['metas' => function ($q) {
        //   $q->where('name', 'job_title')->select()->addSelect('value as job_title');
        // }]);
        ->leftJoin('user_metas', 'user_metas.user_id', '=', 'users.id')
        ->addSelect(['user_metas.value as job_title', 'users.name as name'])
        ->where('user_metas.name', 'job_title');
      } else {
        $job_title = $this->job_title()->first();
        if ($job_title) {
          $this->job_title = $job_title ?? $job_title->value;
        }
        return $this;
      }
    }

    public function withMyInterest($stmt, $interests){
      return $stmt->whereHas('interests', function($q) use($interests){
        $q->whereIn('name', $interests);
      });
    }
    //

    public function withMyLocation($user, $stmt){
      $state                = $user->state; //$this->state;
      $country              = $user->country; //$this->country;

      $stmt->where('state', $state)->where('country', $country);

      return $stmt;
    }

    public function withFilters($request, $stmt){
      $state                = $request->state; //$this->state;
      $country              = $request->country; //$this->country;
      $location             = $request->location;

      if ($location) {
        if ($location == 'country') {
          $stmt->where('country', $this->country)->where('state', '!=', $this->state);
        }
        if ($location == 'world') {
          $stmt->where('country', '!=', $this->country);
        }
      }
      return $stmt;
    }
    //
    public function Users($request){
      $orderBy        = $request->orderBy;
      $gender         = $request->gender;
      $search         = $request->search;
      $location       = $request->location;
      $lat            = $this->lat;
      $lng            = $this->lng;
      $interests      = $this->interests->pluck('name');

      $stmt = $this
      ->where('users.id', '!=', $this->id)
      ->where('profile_step', 100)
      ->where('lat', '!=', NULL)->where('lng', '!=', NULL);

      $this->distance($stmt, (string)$lat, (string)$lng);

      $this->withMyInterest($stmt, $interests);

      $this->withFilters($request, $stmt);

      if (!$location) {
        $this->withMyLocation($this, $stmt);
      }

      $this->withSearch($search, $stmt);

      if ($gender) {
        $stmt->where('gender', $gender);
      }

      $this->withJobTitle($stmt);

      if ($orderBy) {
        switch ($orderBy) {
          case 'job_title':
            $stmt
            // ->select([
            //   'user_metas.value as job_title',
            //   'users.*'
            // ])
            // ->join('user_metas', 'user_metas.user_id', '=', 'users.id')
            // ->where('user_metas.name', 'job_title')
            ->orderBy('user_metas.value', 'ASC');
            break;
          // case 'name':
          default:
            $stmt->orderBy($orderBy);
            break;
        }
      } else {
        $stmt->orderBy('distance', 'ASC');
      }

      return $stmt;
    }

    public function withLiked($q, $author = false){
      // $relation = $author ? 'post.author.liked_profile' : 'liked_profile' ;
      if ($author) {
        $q->with(['activeable' => function($q){
          $q->morphWith([
            Post::class => function($q){
              $q->where('id', $this->id);
          }]);
        }]);
      } else {
        $q->with(['liked_profile' => function($q){
          $q->where('id', $this->id);
        }]);
      }


      return $q;
    }

    public function withUserRequestStatus(User $user){
      $this->request_detail = UserRequest::usersRequestStatus($this, $user);
      return $this;
    }
    //
    public static function getNotificationStatus($user_id){
      return true;
    }

    public function myRandomUser($request){
      return $this->Users($request)
      // whereHas('image')
      ->inRandomOrder()->limit(10)->get();
    }
    //
    public function countCompleteUsers(){
      return $this->where('profile_step', 100)->count()-1;
    }

    public function isContact(User $user){
      return Contact::where('type', 'friend')->where(function($q) use($user){
        $q->where(function($q) use($user){
          $q->where('user_id', $this->id)->where('other_user_id', $user->id);
        })
        ->orWhere(function($q) use($user){
          $q->where('other_user_id', $this->id)->where('user_id', $user->id);
        });
      })
      ->first();
    }

    public function checkBetween(User $other_user){
      return UserRequest::where(function($q) use($other_user){
        $q->where('user_id', $this->id)->where('other_user_id', $other_user->id)->where(function($q){
          $q->where('status', 'pending')->orWhere('status', 'accepted');
        });
      })
      ->orWhere(function($q) use($other_user){
        $q->where('user_id', $other_user->id)->where('other_user_id', $this->id)->where(function($q){
          $q->where('status', 'pending')->orWhere('status', 'accepted');
        });
      });
    }

    public function checkRequestExists(User $other_user){
      return $this->checkBetween($other_user);
    }

    public function requestNotificationData(User $other_user){
      return [
        'message' => [
          'user_name'    => $this->name,
          'action'       => 'receive_request',
          'action_id'    => '0',
          'title'        => 'New request',
          'message'      => 'You have a new request from '.$this->name,
        ],
        'action_data'  => [
          'user_id'       => $this->id,
          'other_user_id' => $other_user->id,
          'action_id'     => 0,
          'action'        => 'receive_request'
        ],
      ];
    }

    public function toggleDirectMessage($bool){
      Setting::updateSetting($this, 'direct_message', $bool);
      return $this;
    }

    public function updateSetting($name, $value){
      Setting::updateSetting($this, $name, $value);
      return $this;
    }


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

      // $this->addMediaConversion('medium')
      // ->width(400)->height(400)//->sharpen(10)
      // // ->withResponsiveImages()
      // ->performOnCollections('avatar', 'cover', 'images');
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
    // public function groups(){
    //   return $this->hasMany(Group::class);
    // }

    // public function conversation(User $otherUser){
    //   return $this->conversations()->whereHas('participants', function($q) use($otherUser){
    //     $q->where('user_id', $otherUser->id);
    //   })->first();
    // }

    public function conversations(User $otherUser = null){
      if ($otherUser) {
        return $this->conversations()->whereHas('participants', function($q) use($otherUser){
          $q->where('user_id', $otherUser->id);
        });
      } else {
        return $this->belongsToMany(Conversation::class, 'conversation_users')->withTimestamps();
      }
    }

    // public function messages(){
    //   return $this->hasManyThrough(Message::class, ConversationUser::class);
    // }

    public function settings(){
      return $this->hasMany(Setting::class);
    }

    public function notifications(){
      return $this->morphMany(Notification::class, 'notifiable');
    }

    public function conversation_messages(){
      return $this->hasMany(Message::class);
    }

    public function converse(){
      return $this->morphMany(Conversation::class, 'conversable');
    }

    public function contacts(){
      return $this->hasMany(Contact::class)->where('type', 'friend')->orWhere(function($q){
        $q->where('type', 'friend')->where('other_user_id', $this->id);
      });
    }

    public function blocked(){
      return $this->hasMany(Contact::class)->where('type', 'block');
    }

    public function activities(){
      return $this->morphMany(Activity::class, 'activeable');
    }

    public function posts(){
      return $this->hasMany(Post::class);
    }

    public function reports(){
      return $this->hasMany(Report::class);
    }

    public function job_title(){
      return $this->hasOne(UserMeta::class)->where('name', 'job_title');
    }

    public function reported(){
      return $this->morphMany(Report::class, 'reportable');
    }

    public function reported_profile(){
      return $this->hasMany(PostReport::class, 'content_id');
    }

    public function metas(){
      return $this->hasMany(UserMeta::class);
    }

    public function requested(){
      return $this->hasMany(UserRequest::class);
    }

    public function requested_pending(){
      return $this->requested()->where('status', 'pending');
    }

    public function requests(){
      return $this->hasMany(UserRequest::class, 'other_user_id');
    }

    public function request(){
      return $this->hasMany(UserRequest::class, 'user_id');
    }

    public function requests_pending(){
      return $this->requests()->where('status', 'pending');
    }

    // public function accepted_requests(){
    //   return $this->hasOne(UserRequest::class, 'user_id')->where('status', 'accept');
    // }
    //
    // public function accept_requests(){
    //   return $this->hasOne(UserRequest::class, 'other_user_id')->where('status', 'accept');
    // }

    public function plan(){
      return $this->hasOne(UserPlan::class)->latest();
    }

    public function plans(){
      return $this->hasMany(UserPlan::class);
    }

    public function interests(){
      return $this->belongsToMany(Interest::class, 'interest_users')->withTimestamps();
    }

    public function sent_notifications(){
      return $this->hasMany(NotificationMessage::class, 'user_id');
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
    public function notes()
    {
        return $this->hasMany('App\Note');
    }
    public function user_checked_notes()
    {
        return $this->hasMany('App\UserCheckedNote');
    }
}
