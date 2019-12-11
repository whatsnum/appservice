<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Image;

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

    // ->withCustomProperties(['mime-type' => 'image/jpeg'])

    // $this->addMediaCollection('video')
    // ->acceptsMimeTypes(['video/avi', 'video/mpeg', 'video/quicktime'])
    // ->acceptsFile(function (File $file) {
    //   return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
    // })->singleFile()->useDisk('msg_videos');

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
  }
}
