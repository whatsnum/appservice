<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use App\User;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $user = $request->user();
      $contacts = $user->contacts()->paginate($request->pageSize ? $request->pageSize : 20);
      return ['status' => true, 'msg' => trans('msg.retrieved'), 'contacts' => $contacts];
    }

    public function blocked(Request $request){
      $user = $request->user();
      $blocked = $user->blocked()->paginate($request->pageSize ? $request->pageSize : 20);
      return ['status' => true, 'msg' => trans('msg.retrieved'), 'blocked' => $blocked];
    }

    public function block(Request $request, User $otherUser){
      $user = $request->user();
      // check blocked
      if ($user->blocked()->where('other_user_id', $otherUser->id)->first()) {
        return ['status' => false, 'msg' => trans('msg.blocked_b4')];
      }

      $blocked = $user->blocked()->create(['other_user_id' => $otherUser->id, 'type' => 'block']);
      return ['status' => !!$blocked, 'msg' => trans($blocked ? 'msg.blocked' : 'msg.block_failed')];
    }

    public function unBlock(Request $request, User $otherUser){
      $user = $request->user();
      // check blocked
      if ($contact = $user->blocked()->where('other_user_id', $otherUser->id)->first()) {
        $contact->delete();
        return ['status' => true, 'msg' => trans('msg.unblocked')];
      } else {
        return ['status' => false, 'msg' => trans('msg.not_blocked_b4')];
      }
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
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Contact  $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Contact $contact)
    {
      $user = $request->user();
      $this->authorize('delete', $contact);
      $otherUser = $contact->user_id == $user->id ? $contact->user() : $contact->otherUser();
      if ($exists = $user->checkRequestExists($otherUser)) {
        $exists->map(function($exist){
          $exist->delete();
        });
      }
      $deleted = $contact->delete();
      return ['status' => $deleted, 'msg' => trans($deleted ? 'msg.deleted' : 'msg.not_deleted')];
    }
}
