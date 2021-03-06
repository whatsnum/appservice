<?php

namespace App\Http\Controllers;

use App\Conversation;
use Illuminate\Http\Request;
use App\User;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = $request->user();
      $conversations = $user->conversations()->with(['last_message', 'participant_id' => function($q) use($user){
        $q->where('conversation_users.user_id', '!=', $user->id)->select('conversation_users.user_id as id');
      }])->withCount(['unread'])->get();



      $conversations->map(function ($conv) {
        // dd($conv->last_message->medias);
        // dd($conv->last_message->medias()->exists());
        if ($conv->last_message->medias()->exists()) {
          $conv->last_message->withMedia();
        }
      });

      return ['status' => true, 'conversations' => $conversations];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
        'other_user_id' => 'required'
      ]);

      $user = $request->user();
      $otherUser = $user->findOrFail($request->other_user_id);
      // check exists
      if ($user->conversations($otherUser)->first()) {
        return ['status' => false, 'msg' => trans('msg.creation_exists')];
      }

      $conversation = $user->converse()->create();
      $user->conversations()->attach($conversation);
      $otherUser->conversations()->attach($conversation);

      return ['status' => !!$conversation, 'msg' => trans($conversation ? 'msg.created' : 'msg.not_created'), 'conversation' => $conversation];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Conversation $conversation)
    {
      $user = $request->user();
      $this->authorize('view', $conversation);
      $unread = $conversation->unread();

      $unread->update(['read_at' => now()]);

      $messages = $conversation->messages()->with('replied')->latest()
      // ->where("deleted_by->$user->id", null)
      ->paginate($this->paginate($request));
      $messages->map(function ($msg) {
        if ($msg->medias()->exists()) {
          $msg->withMedia();
        }
      });

      return ['status' => true, 'messages' => $messages];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Conversation $conversation)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Conversation  $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Conversation $conversation)
    {
      $user = $request->user();
      $this->authorize('delete', $conversation);
      $messages = $conversation->messages()->where("deleted_by->$user->id", null)->get();
      $messages->map(function($msg) use($user){
        $msg->deletedBy($user);
      });

      return ['status' => true, 'msg' => trans('msg.deleted'), 'conversation' => $conversation];

    }

    public function images(Request $request, User $otherUser){
      $user = $request->user();
      $images = $user->imageMessages($otherUser);
      if ($images) {
        $images = $images->get()->map(function($img){
          return $img->withImageUrl()->image;
        });
      }

      return ['status' => true, 'images' => $images ?? []];
    }
}
