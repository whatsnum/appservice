<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Image;
use App\Post;
use App\Jobs\BroadcastNewPost;

class Activity extends Model implements HasMedia
{
  use HasMediaTrait;
  protected $fillable = ['content_id', 'action', 'type'];

  public function addImages($images, $type){
    // $type == 'profile'
    if (is_array($images)) {
      foreach ($images['images'] as $image) {
        $this->addMediaFromUrl($image)->usingName('images')->toMediaCollection('images');
      }
    } else {
      $this->addMediaFromUrl($images)->usingName('images')->toMediaCollection('images');
    }
  }

  public function updateImages($images){
    $post = $this->load('post')->post;
    if ($post) {
      $medias = $this->getMedia('images');
      if ($medias) {
        foreach ($medias as $media) {
          $media->delete();
        }
      }
      $images = $post->withPhotoUrl()->images;
      foreach ($images as $img) {
        $this->addMediaFromUrl($img['photo'])->usingName('images')->toMediaCollection('images');
      }
    }
  }

  public static function migratePosts() {
    $activities = self::all();
    $activities->map(function($activity){
      if ($activity->type == 'post') {
        $post = $activity->load('post')->post;
        if ($post) {
          $images = $post->withPhotoUrl()->images;
          foreach ($images as $img) {
            $activity->addMediaFromUrl($img['photo'])->usingName('images')->toMediaCollection('images');
          }
        }
      }
    });
  }

  public static function migrateUsers() {
    $activities = self::all();
    $activities->map(function($activity){
      if ($activity->type == 'profile') {
        $user = $activity->load('user')->user;
        if ($user) {
          $images = $user->withPhotoUrl()->images;
          foreach ($images as $img) {
            if ($img->photo) {
              $activity->addMediaFromUrl($img->photo)->usingName('images')->toMediaCollection('images');
            }
          }
        }
      }
    });
  }

  public function withPhotoUrl(){
    $mediass = $this->getMedia('images');
    $medias = [];
    if ($mediass) {
      foreach ($mediass as $media) {
        $width = '';//Image::load($media->getPath())->getWidth();
        $height = '';//Image::load($media->getPath())->getHeight();
        $medias[] = [
          'photo' => $media->getUrl(),
          'thumb' => $media->getUrl('thumb'),
          'width' => $width, 'height' => $height];
      }
    }

    $this->images = $medias;
    return $this;
  }

  public static function time_elapsed_string($datetime, $full = false) {
    $now = new \DateTime;
    $ago = new \DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
  }

  private static function location(&$q, $state, $country){
    return $q->where('state', $state)->where('country', $country);
  }

  private static function age(&$q, $min_age, $max_age){
    return $q->whereBetween('age', [$min_age, $max_age]);
  }

  private static function gender(&$q, $other_gender){
    if ($other_gender === 'both') {
      return $q->whereIn('gender', ['male', 'female']);
    } else {
      return $q->where('gender', $other_gender);
    }
  }

  private static function contact(&$q, $user){
    return $q->whereHas('accepted_requests', function($q) use($user){
      $q->where('other_user_id', $user->user_id);
    })
    ->orWhereHas('accept_requests', function($q) use($user){
      $q->where('user_id', $user->user_id);
    })
    ->orWhere('user_id', $user->user_id);
  }

  private static function activity_filter($activity_privacy, $q, $user){
    if ($activity_privacy === 'contact') {
      self::contact($q, $user);
    } else if($activity_privacy === 'everyone') {
      // self::location($q, $state, $country);
    } else {
      self::location($q, $user->state, $user->country);
    }
  }

  public static function feeds($request){
    $user           = $request->user();
    $other_gender   = $user->other_user_gender;
    $state          = $user->state;
    $country        = $user->country;
    $gender         = $user->gender;
    $min_age        = $user->other_user_min_age;
    $max_age        = $user->other_user_max_age;
    $search         = $request->search;
    $orderBy        = $request->orderBy;
    $pageSize       = $request->pageSize;

    $user->load('activity_privacy');
    $activity_privacy = $user->activity_privacy ? $user->activity_privacy->value : null;

    $feeds = self::with(['post', 'user'])
    ->where('type', 'post')
    // post.author
    ->whereHasMorph('activeable', [Post::class], function($q, $type) use($activity_privacy, $country, $state, $min_age, $max_age, $other_gender, $user){
      $q->whereHas('author', function($q) use($activity_privacy, $country, $state, $min_age, $max_age, $other_gender, $user){
        self::activity_filter($activity_privacy, $q, $user);
        self::age($q, $min_age, $max_age);
        self::gender($q, $other_gender);
      });
    })
    ->orWhere('type', 'profile')
    ->whereHasMorph('activeable', [User::class], function($q) use($activity_privacy, $country, $state, $min_age, $max_age, $other_gender, $user){
    // ->whereHas('user', function($q) use($activity_privacy, $country, $state, $min_age, $max_age, $other_gender, $user){
      self::activity_filter($activity_privacy, $q, $user);
      self::age($q, $min_age, $max_age);
      self::gender($q, $other_gender);
    });

    $user->withLiked($feeds, true);

    if ($search) {
      // $feeds = self::search($search);
      $feeds->whereHas('post', function($q) use($search){
        $q->where('text', 'LIKE', '%'.$search.'%');
      })
      ->orWhereHas('user', function($q) use($search){
        $q->where('name', 'LIKE', '%'.$search.'%');
      })
      ->orWhereHas('post.author', function($q) use($search){
        $q->where('name', 'LIKE', '%'.$search.'%');
      });
    }

    $feeds->where('created_at', '>=', Carbon::now()->subDays(14)->toDateTimeString());

    $feeds = $feeds->latest()
    ->paginate($request->pageSize ? $request->pageSize : null);
    if ($feeds) {
      $feeds->map(function($feed) use($request){
        $feed->details($request->user, true);
      });
    }
    return $feeds;
  }

  public function withTimeElapsed(){
    $this->time_passed = self::time_elapsed_string($this->created_at);
    return $this;
  }

  public function details(User $user, $loaded = false){
    if (!$loaded) {
      $this->load(['post', 'post.author', 'user']);
    }
    if ($this->type == 'post') {
      $this->text = $this->post->text;
      $this->author = $this->post->author->withPhotoUrl()->withUserRequestStatus($user);
    } else {
      $this->author = $this->user->withPhotoUrl()->withUserRequestStatus($user);
    }
    return $this->withPhotoUrl()->withTimeElapsed();
  }

  public function betaFeeds(){

  }

  public static function getActionText($request, $type, $content_id){
    if ($type == 'profile' || $type == 'images'){
      $user = User::find($content_id);
      if ($user) {
        $gender = $user->gender === 'male' ? 'his' : 'her';
      }
    }

    $action = '';
    $text = $request->text;
    $images = $type == 'images' ? $request->image : $request->images;
    $verb = $type == 'images' ? 'Added' : 'Posted';
    $append = $type == 'images' ? " to $gender profile" : '';
    if ($type == 'post' || $type == 'images') {
      switch ($request) {
        case (!$text && count($images) == 1):
          $action = "$verb a Photo$append";
          break;

        case (!$text && count($images) > 1):
          $no = count($images);
          $action = "$verb $no Photos$append";
          break;

        case ($text && count($images) > 0):
          $action = "Added a Post";
          break;

        case ($text && count($images) === 0):
          $action = "Wrote something";
          break;

        default:
          $action = "Added a Post";
          break;
      }
    }

    if ($type == 'profile') {
      switch ($type) {
        case ($type === 'profile'):
          // $user = User::find($content_id);
          if ($user) {
            // $gender = $user->gender === 'male' ? 'his' : 'her';
            $action = "Changed $gender Profile Photo";
          }
          break;
      }
    }

    return $action;
  }

  public static function addNew(String $type, $request, $content_id, $images = false, $action = false){
    $action = $action ? $action : self::getActionText($request, $type, $content_id);
    $type = $type == 'images' ? 'profile' : $type;
    $activity = self::create([
      'content_id' => $content_id,
      'action' => $action,
      'type' => $type,
    ]);

    if ($images) {
      $activity->addImages($images, $type);
    }

    // $activity->load(['post.author', 'user']);
    //
    // if ($activity->type == 'post') {
    //   $activity->text = $activity->post->text;
    //   $activity->author = $activity->post->author->withPhotoUrl();
    // } else {
    //   $activity->author = $activity->user->withPhotoUrl();
    // }
    // $activity->withPhotoUrl()->withTimeElapsed();

    // event(new \App\Events\NewPost($activity));
    BroadcastNewPost::dispatch($activity);

    return $activity;
  }

  public function makeUpdate($request, $images = false){
    $action = $this::getActionText($request, 'post', null);

    $this->updateImages($images);

    return $this->update([
      'action' => $action,
    ]);
  }

  public function registerMediaCollections(){
    $this->addMediaCollection('images')
    ->acceptsFile(function (File $file) {
      return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
    })->useDisk('activity_images');
  }

  public function registerMediaConversions(Media $media = null){
    $this->addMediaConversion('thumb')
    ->width(368)->height(232)//->sharpen(10)
    ->performOnCollections('images');

    $this->addMediaConversion('medium')
    ->width(400)->height(400)//->sharpen(10)
    ->performOnCollections('images');
  }

  public function activeable(){
    return $this->morphTo();
  }

  public function post(){
    return $this->activeable()->where('activeable_type', Post::class);
  }

  public function user(){
    return $this->activeable()->where('activeable_type', Post::class);
  }
}
