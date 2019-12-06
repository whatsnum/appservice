<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
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
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

    public function change(Request $request){
      $user = $request->user();
      $request->validate([
        'name' => 'required',
        'value' => 'required',
      ]);

      $name = $request->name;
      $value = $request->value;
      $value = $value == 'true' ? true : ($value == 'false' ? false : $value);
      // dd($value);

      if(in_array($name, ['show_on_map', 'max_distance', 'read_receipt', 'last_seen', 'show_online'])){
        if ($request->name == 'show_on_map') {
          try {
            $user = Setting::toggleShowOnMap($user);
          } catch (\Exception $e) {
            return $this->err($e);
          }
        } else {
          // $user = Setting::updateSetting($user, $name, $value);
          $user->updateSetting($name, $value);
        }
        return ['status' => !!$user, 'user' => $user];
      } else {
        return ['status' => false, 'msg' => trans('msg.not_settings')];
      }
    }
}
