<?php

namespace App\Http\Controllers;

use App\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      // $search         = $request->search;
      // $orderBy        = $request->orderBy;
      // $pageSize       = $request->pageSize;
      //
      // $interests = new Interest();
      //
      // if ($search) {
      //   $interests->where('name', 'LIKE', '%'.$search.'%');
      // }
      //
      // $interests->paginate($request->pageSize ? $request->pageSize : null);

      return Interest::getInterests($request);
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
        'interest_ids' => 'required',
      ]);

      // $user = \App\User::findOrFail($request->user_id);
      $user = $request->user();
      $ids = $request->interest_ids;

      $attach = $user->interests()->attach($ids);
      return ['status' => true, 'msg' => 'Interests added Successfully'];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function show(Interest $interest, Request $request)
    {
      $user = \App\User::findOrFail($request->user_id);
      return $interest->load('users');
      // return $user->interests()->get();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function edit(Interest $interest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interest $interest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interest  $interest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interest $interest)
    {
        //
    }
}
