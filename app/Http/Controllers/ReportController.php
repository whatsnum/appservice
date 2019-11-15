<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
  protected $fillable = [ 'user_id', 'reportable', 'reportable_type', 'comment'];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        //
    }

    public static function addNew($request){
      $other_user_id   = $request->other_user_id;
      $content_id      = $request->content_id;
      $comment         = $request->comment;
      $type            = $request->type;
      $user            = $request->user();

      return $user->reports()->create([
        'other_user_id'   => $other_user_id,
        // 'content_id'      => $content_id,
        'comment'         => $comment,
        // 'type'            => $type,
      ]);
    }

    public function reported_user(){
      return $this->belongsTo(User::class, 'other_user_id');
    }

    public function reporting_user(){
      return $this->belongsTo(User::class, 'user_id');
    }

    public function reported_group(){
      return $this->belongsTo(Group::class, 'content_id');
    }

    public function reporting_post(){
      return $this->belongsTo(Post::class, 'content_id');
    }

    public function reporting_profile(){
      return $this->belongsTo(User::class, 'content_id');
    }
}
