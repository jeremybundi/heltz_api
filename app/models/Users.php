<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $name;
    public $phone;
    public $email;
    public $username;
    public $password;
    

  // Add a new user
  public static function addItem($userData)
  {
      $user = new self();
      $user->name = $userData['name'];
      $user->phone = $userData['phone'];
      $user->email = $userData['email'];
      $user->username = $userData['username'];
      $user->password = $userData['password'];

      if ($user->save()) {
          return true;
      } else {
          return false;
      }
  }
}   