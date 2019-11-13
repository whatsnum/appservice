<?php

namespace App\Http\Controllers;

use App\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $feeds = Activity::feeds($request);
      return ['status' => true, 'feeds' => $feeds];
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
      return $activity->details($request->user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
      if ($activity->type == 'profile') {
        $post = null;
      } else {
        $post = Post::where('id', $activity->content_id)->first();
        if($post){
          $post = $post->withPhotoUrl();
        }
      }

      return ['status' => true, 'activity' => $activity, 'post' => $post];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
      $post = Post::where('id', $activity->content_id)->first();
      $images = $request->images;
      if ($post) {
        $update = $post->update(request()->all());
        if ($update) {
          if (isset($images[0]['photo'])) {
            // add photos to post
            $postMedias = $post->getMedia('images');
            if ($postMedias) {
              $media_no = count($postMedias);
              if ( $media_no < count($images)) {
                  $updated_images = $post->addImages($images);
                  // add new photo
                // code...
              } elseif ($media_no > count($images)) {
                // delete all photos and add photos
                $updated_images = $post->addImages($images);
                foreach ($postMedias as $media) {
                  $media->delete();
                }
              } else {
                $updated_images = $post->addImages($images);
              }
            }
            // $update->updatePhotoWithUrl($request->photo);
          } else {
            $medias = $post->getMedia('images');
            if ($medias) {
              foreach ($medias as $media) {
                $media->delete();
              }
            }

            $updated_images = $post->addImages($images);
          }
        }
      }

      $update = $activity->makeUpdate($request, isset($updated_images) ? $updated_images : false);

      return [
        'a' => isset($updated_images) ? $updated_images : false,
        'status' => $update ? true : false,
        'msg' => $update ? trans('messages.post_update_success') : trans('messages.post_update_error'),
        'post' => $activity,
        'update' => $update,
        'p' => $post,
        'pm' => $post->getMedia('images'),
      ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
      $del = [];
      $post = false;
      if ($activity->type == 'profile') {
        $post = $activity->forceDelete();
      } else {
        $post = Post::where('id', $activity->content_id)->first();
        if ($post) {
          $post->forceDelete();
          if ($post) {
            $del[] = 1;
            $post = $activity->forceDelete();
            if ($post) {
              $del[] = 2;
            }
          }
        }
      }


      return [ 'status' => $post, 'debug' => $del, 'msg' => $post ? 'Post Successfully Deleted' : "Post Could Not Be Deleted" ];
    }
}
