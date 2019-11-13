<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\File;
use Spatie\MediaLibrary\Models\Media;
use Spatie\Image\Image;

class Post extends Model implements HasMedia
{
  use HasMediaTrait;
  protected $fillable = ['user_id', 'text'] ;

  public function withPhotoUrl(){
    $mediass = $this->getMedia('images');
    $medias = [];
    if ($mediass) {
      foreach ($mediass as $media) {
        $medias[] = [
          'photo' => $media->getUrl(), 'thumb' => $media->getUrl('thumb'),
        ];
      }
    }

    $this->images = $medias;
    return $this;
  }

  public function addImages($images){
    $photos = [];
    $thumbs = [];
    foreach ($images as $image) {
      if (isset($image['photo'])) {
        $medias = $this->getMedia('images');
        if ($medias) {
          foreach ($medias as $media) {
            $url = $media->getUrl();
            if ($url === $image['photo']) {
              $this->addMediaFromUrl($image['photo'])->usingName('images')->toMediaCollection('images');
              $media->delete();
            }
          }
        }
      } else {
        $data = str_replace('data:image/png;base64,', '', $image);
        $data = str_replace(' ', '+', $data);
        $data = base64_decode($data);
        $file_name = rand() . '.png';
        $file = base_path().'/'.'images/'.$file_name;
        $success = file_put_contents($file, $data);
        $this->addMedia($file)->usingName('images')->usingFileName($file_name)->toMediaCollection('images');
        $this->save();
      }
    }
    $medias = $this->getMedia('images');
    foreach ($medias as $media) {
      $photos[] = $media->getUrl();
      $thumbs[] = $media->getUrl('thumb');
    }
    // $photos = $media->getUrl();;
    // $thumbs = [];
    return ['success' => true, 'images' => $photos, 'thumbs' => $thumbs];
  }

  public function activity(){
    return $this->morphOne(Activity::class, 'activeable');
  }

  public function author(){
    return $this->belongsTo(User::class, 'user_id');
  }

  public function registerMediaCollections(Media $media = null){
    $this->addMediaCollection("images")
    ->acceptsFile(function (File $file) {
      return ($file->mimeType === 'image/png' || $file->mimeType === 'image/jpeg');
    })->useDisk('user_post_images');
  }

  public function registerMediaConversions(Media $media = null){
    $this->addMediaConversion('thumb')
    ->width(368)->height(232)//->sharpen(10)
    ->performOnCollections('images');
    // ->nonOptimized();
    $this->addMediaConversion('medium')
    ->width(400)->height(400)//->sharpen(10)
    ->performOnCollections('images');
  }

  public function reports(){
    return $this->hasMany(Report::class, 'reportable');
  }
}
