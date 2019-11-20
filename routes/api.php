<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'localization'], function(){
//   Route::middleware('auth:api')->get('/user', function (Request $request) {
//       return $request->user();
//   });
//
  Route::post('login', 'UserController@login');
//
//
//   Route::group(['middleware' => 'auth:api'], function(){
//     Route::post('details', 'UserController@details');
//   });
//
  // Route::get('contents', 'ContentController@index');
  Route::resource('contents', 'ContentController');
//   Route::resource('countries', 'CountriesController');
//   Route::get('get_all_plan.php', 'PlanController@get_all_plan');
//   Route::post('otp_verfiy.php', 'UserNotificationController@otp_verify');
//   Route::post('signup_step_5.php', 'UserController@signup_step_5');
//   Route::post('signup_step_4.php', 'UserController@signup_step_4');
//   Route::post('signup_step_3.php', 'UserController@signup_step_3');
//   Route::post('signup_step_2.php', 'UserController@signup_step_2');
//   Route::post('signup_step_1.php', 'UserController@signup_step_1');
//   Route::get('get_all_plan.php', 'PlanController@get_all_plan');
  Route::post('complete_signup', 'UserController@complete_signup');
//   Route::post('update_user_details.php', 'UserController@update_user_details');
  Route::get('users/count', 'UserController@count');
//   Route::post('users/auth_image_upload', 'UserController@authImageUpload');
//

  Route::group(['middleware' => ['activated', 'auth:api']], function(){
//     Route::get('get_my_receive_request.php', 'UserController@get_my_receive_request');
//     Route::get('get_my_pending_sent_requests.php', 'UserController@get_my_pending_sent_requests');
//     Route::get('get_my_whatsnum_contacts.php', 'UserController@get_my_whatsnum_contacts');
//     Route::get('find_friends_map.php', 'UserController@find_friends_map');
//     Route::get('user_details.php', 'UserController@user_details');
//     Route::get('send_request.php', 'UserRequestController@send_request');
//     Route::get('accept_remove_request.php', 'UserRequestController@accept_remove_request');
//     Route::get('delete_my_contact.php', 'UserRequestController@delete_my_contact');
//     Route::post('report_user.php', 'ReportUserController@report_user');
//     Route::get('search_modal.php', 'UserController@search_modal');
//     Route::get('search_location.php', 'UserController@search_location');
//     Route::get('get_all_notification.php', 'NotificationMessageController@get_all_notification');
    Route::post('notifications/read/{notification_message}', 'NotificationMessageController@read');
    Route::get('notifications', 'NotificationMessageController@index');
    Route::get('notifications/unread/count', 'NotificationMessageController@unreadCount');
//     Route::get('insert_temp_plan.php', 'PlanController@insert_temp_plan');
//     Route::get('send_purchase_plan_notification.php', 'PlanController@send_purchase_plan_notification');
//     Route::post('swap_profile_image.php', 'UserController@swap_profile_image');
//     Route::post('delete_upload_image.php', 'UserController@delete_upload_image');
//     Route::post('swap_profile_image.php', 'UserController@swap_profile_image');
//     Route::resource('groups', 'GroupController');
//     Route::get('mygroups', 'GroupController@myGroups');
//     Route::get('post_catgories', 'CategoryController@post_catgories');
//     Route::get('group_catgories', 'CategoryController@group_catgories');
//     Route::resource('posts', 'PostController');
    Route::get('users/state/random', 'UserRequestController@random_users');
    Route::get('users/map', 'UserController@latlng');
    Route::resource('users', 'UserController');
    Route::resource('job_titles', 'JobTitleController');
    Route::resource('interests', 'InterestController');
    Route::resource('activities', 'ActivityController');
//     // Route::resource('countries', 'ActivityController');
    Route::post('toggleDirectMessage', 'UserController@toggleDirectMessage');
    Route::post('image_upload', 'UserController@uploadImage');
//     Route::get('users/new', 'UserRequestController@new_users');
//     Route::get('users/valid', 'UserController@validIds');
    Route::post('users/like/{other_user}', 'UserController@like');
//     Route::post('users/regard', 'UserController@regard');
    Route::resource('reports', 'ReportController');
//     Route::resource('settings', 'SettingController');
//     Route::post('settings/change', 'SettingController@change');
//     Route::get('mail/support/view', 'MailingController@view');
//     Route::post('mail/support/send', 'MailingController@support');
//     Route::post('users/delete', 'UserController@delete');
    Route::post('users/passcode/update', 'UserController@updatePassCode');
//     // Route::get('activities/{id}/user_request', 'ActivityController@userRequest');
    // Route::post('register', 'UserController@register');
//
  });
  Route::post('register', 'UserController@register');
//
});
//
// use App\Events\NewNotification;
// use App\Events\NewPost;
use App\User;
// use App\Activity;
// use App\Plan;
// use App\UserRequest;
Route::get('test', function(Request $request){
  $user = User::findOrFail(1);
  $updates = $request->all();
  \App\Notification::DeviceTokenStore_1_Signal($user, 'android', "ee3c6b83-fc80-4d21-903a-3953659f878c");
  return $user;
});
//
// Route::get('cache-clear', function(Request $request){
//   shell_exec('composer dump-autolad');
//   Artisan::call('config:cache');
//   Artisan::call('config:clear');
//   Artisan::call('cache:clear');
//   Artisan::call('route:clear');
//   Artisan::call('config:cache');
// });
