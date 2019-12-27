<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneBook extends Model
{
  protected $fillable = ['user_id', 'other_user_id'];

  protected static $testNumbers = [
    '234-8110000606', '255-5445665484', '500-27049054', '1876-71466469', '234-4862635'
  ];

  public static function sync(array $contacts){
    $user = auth()->user();
    $phone_code = $user->phone_code;
    $books = [];
    $numsUsers = [];

    foreach ($contacts as $contact) {
      $extracted = self::extract($contact);
      $books[] = ['phone_code' => $extracted[0], 'phone' => $extracted[1]];
    }

    foreach ($books as $book) {
      $otherUser = User::where('phone_code', $book['phone_code'])->where('phone', $book['phone'])->first();
      if ($otherUser) {
        if(!$user->inPhoneBook($otherUser)){
          $user->addPhoneBook($otherUser);
        }
        $numsUsers[] = $otherUser->id;
      }
    }
    return $numsUsers;
  }

  public static function extract($phoneNumber){
    return explode('-', $phoneNumber);
  }
}
