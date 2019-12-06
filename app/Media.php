<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Media extends Image
{
  protected $fillable = ['id',	'model_type',	'model_id',	'collection_name',	'name',	'file_name',	'mime_type',	'disk',	'size',	'manipulations',	'custom_properties',	'responsive_images',	'order_column'];

  public static function uploadImage($images){
    $image = $images;
    // public function uploadImage($image, $type, $request = false, $action = false){
    $data = str_replace('data:image/png;base64,', '', $image);
    $data = str_replace(' ', '+', $data);
    $data = base64_decode($data);
    $file_name = rand() . '.png';
    $path = base_path().'/'.'images/'.$file_name;

    // $file64 = self::image64($image);
    $success = file_put_contents($path, $data);
    // return $file64;
    return $path;
    // [
    //   'file' => $path,
    //   'file_name' => $file_name,
    // ];
  }

  public static function ImageUpload($imageInput){
    // $image = self::make($imageInput);

    return self::make($imageInput);
  }

  public static function test($imageInput){
    dd(self::ImageUpload($imageInput));
  }

  public static function image64($image) {
    // $png_url = "product-".time().".png";
    // $path = public_path().'img/designs/' . $png_url;

    // $file =
    return file_get_contents($image);

    // Image::make(file_get_contents($data->base64_image))->save($path);
    // $response = array(
    //     'status' => 'success',
    // );
    // return Response::json( $response  );
 }
}
