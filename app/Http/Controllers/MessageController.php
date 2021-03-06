<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use App\Media;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      // $user = $request->user();
      // $messages = $user->messages()->paginate($this->paginate($request));
      // return ['status' => true, 'messages' => $messages];
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
      $user = $request->user();
      $request->validate([
        'other_user_id' => 'required',
        'message' => 'string|nullable',
        'image' => 'imageable|nullable',
        'gif' => 'url|nullable',
        'videos' => 'array',
        'videos.*' => 'mimetypes:video/mp4,video/x-msvideo,video/3gpp,video/avi,video/mpeg,video/quicktime|max:40000'
      ]);

      // extract
      $image = $request->image;
      $videos = $request->videos;
      $gif = $request->gif;
      $text = $request->message;

      if (!$image && !$videos && !$text && !$gif) {
        return ['status' => false, 'msg' => trans('msg.empty_msg')];
      }

      $otherUser = $user->findOrFail($request->other_user_id);

      $conversation = $user->conversations($otherUser)->first();

      if (!$conversation) {
        $conversation = $user->converse()->create();
        $user->conversations()->attach($conversation);
        $otherUser->conversations()->attach($conversation);
      }

      if ($conversation) {
        $message = $conversation->messages()->create([
          'user_id' => $user->id,
          'reply' => $request->reply,
          'message' => $text,
          'gif' => $gif,
        ]);

        // $message = new Message;
        // dd($message);

        if ($image) {
          $message->saveImage($image);
        } else if ($videos) {
          $message->saveVideos($videos);
        }

        return ['status' => !!$message, 'message' => $message, 'msg' => trans($message ? 'msg.created' : 'msg.not_created')];
      }

      return ['status' => false, 'msg' => trans('msg.conversation_error')];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Message $message)
    {
      $user = $request->user();
      $this->authorize('view', $message);
      return $message;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Message $message)
    {
      $user = $request->user();
      $everyone = $request->everyone;
      $this->authorize('delete', $message);

      if ($everyone) {
        $this->authorize('deleteAll', $message);
        $message->deleteForAll();
      } else {
        $message->deletedBy($user);
      }

      return ['status' => true, 'message' => $message->withImageUrl()];
    }
}
