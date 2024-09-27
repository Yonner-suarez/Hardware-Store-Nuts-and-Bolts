<?php


require_once(__DIR__ . "/../models/User.php");
class UserController{
  public function GetUsersList(){
    $users = [
      new User(1, "John Doe", "john@doe.com", "123456"),
      new User(2, "Jane Doe", "jane@doe.com", "123456"),
    ];
    return $users;
  }
}

