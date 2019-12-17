<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Image;
use \getID3;
use App\Media as MMedia;

class Message extends Model implements HasMedia
{
  use SoftDeletes, HasMediaTrait;
  protected $fillable = ['user_id', 'delivered_at', 'read_at', 'conversation_id',	'reply', 'message', 'gif', 'deleted_at'];
  protected $casts = ['deleted_by' => 'array'];
  protected $hidden = ['media'];

  public function saveImage($image){
    $collection = 'image';
    $name = $collection;
    $type = strpos($image, ';');
    $type = explode(':', substr($image, 0, $type))[1];
    $ext = '.jpg';
    switch ($type) {
      case 'image/png':
        $ext = '.png';
        break;
      case 'image/gif':
        $ext = '.gif';
        break;
    }
    $file_name = rand().$ext;

    $media = $this->addMediaFromBase64($image)
    ->usingName($name)->usingFileName($file_name)
    ->toMediaCollection($collection);
    $this->withImageUrl($media);
    return $media;
  }

  public function saveVideos($videos){
    $getID3 = new getID3;

    $medias = [];
    foreach ($videos as $video) {
      $collection = 'videos';
      $name = $collection;
      $ext = $video->getClientOriginalExtension();
      $file_name = rand().'.'.$ext;

      $media = $this->addMedia($video)
      ->usingName($name)->usingFileName($file_name)
      ->withCustomProperties([
        'size' => MMedia::formatBytes($video->getSize()),
      ])
      ->toMediaCollection($collection);
      $ThisFileInfo = $getID3->analyze($media->getPath());
      $duration = date('H:i:s.v', $ThisFileInfo['playtime_seconds']);
      $media->setCustomProperty('length', $duration);
      $media->save();
      $medias[] = $media;
    }

    $this->withVideos($medias);
    // dd($medias);
    return $medias;
  }

  public function withVideos($medias = null){
    if (!$medias) {
      $medias = $this->getMedia('videos');
    }

    if ($medias) {
      $videos = [];
      for ($i=0; $i < sizeof($medias); $i++) {
        $media = $medias[$i];
        $video = new \stdClass();
        $video->thumb = $media->getUrl('thumb');
        $video->url = $media->getUrl();
        $video->size = $media->custom_properties['size'];
        $video->metas = $media->custom_properties;
        $videos[] = $video;
      }
      if ($videos) {
        $this->videos = $videos;
      }
    }

    return $this;
  }

  public function withMedia(){
    $this->withVideos();
    $this->withImageUrl();
  }

  public function withImageUrl($media = null){
    if (!$media) {
      $media = $this->getFirstMedia('image');
    }

    if ($media) {
      $this->image = new \stdClass();
      $this->image->thumb = $media->getUrl('thumb');
      $this->image->url = $media->getUrl();
    }
    return $this;
  }

  public function deletedBy(User $user){
    $deleted_by = $this->deleted_by ?? [];
    $deleted_by["$user->id"] = now();
    $this->deleted_by = $deleted_by;
    return $this->save();
  }

  public function flagDeleteBy(User $user){
    $deleted_by = $this->deleted_by ?? [];
    $deleted_by["$user->id"] = now();
    $this->deleted_by = $deleted_by;
    return $this;
  }

  public function deleteForAll(){
    return $this->delete();
  }

  public function conversation(){
    return $this->belongsTo(Conversation::class);
  }

  public function sender(){
    return $this->belongsTo(User::class);
  }

  public function replied(){
    return $this->belongsTo(Message::class, 'reply');
  }

  public function medias(){
    return $this->morphMany(Media::class, 'model');
  }

  public function media_images(){
    return $this->medias()->where('name', 'image');
  }

  public function registerMediaCollections(Media $media = null){
    $this->addMediaCollection('image')
    ->acceptsMimeTypes(['image/jpeg', 'image/png'])
    // ->acceptsFile(function (File $file) {
    //   return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
    // })
    ->singleFile()->useDisk('msg_images');

    $this->addMediaCollection('videos')
    ->acceptsMimeTypes(['video/mp4', 'video/3gpp', 'video/x-msvideo', '	video/x-flv'])
    ->singleFile()->useDisk('msg_videos');

    // ->withCustomProperties(['mime-type' => 'image/jpeg'])

    // $this->addMediaCollection('file')
    // ->acceptsFile(function (File $file) {
    //   return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
    // })->singleFile()->useDisk('msg_files');

  }

  public function registerMediaConversions(Media $media = null){
    $this->addMediaConversion('thumb')
    ->withResponsiveImages()
    ->width(50)->height(50)->blur(5)
    ->performOnCollections('image');

    $this->addMediaConversion('thumb')->queued()
    ->width(250)
    ->height(250)
    ->extractVideoFrameAtSecond(2)
    ->performOnCollections('videos');
  }
}
