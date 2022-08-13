<?php

/*
 * Admin Login 
 */

namespace App\Models\DressFairOpenCart\AdminUser;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of AdminUserModel
 *
 * @author Kamran Adil
 */
class AdminUserModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'user';

  const FIELD_USER_ID = 'user_id';
  const FIELD_USER_GROUP_ID = 'user_group_id';
  const FIELD_USER_NAME = 'username';
  const FIELD_SALT = 'salt';
  const FIELD_PASSWORD = 'password';
  const FIELD_FIRSTNAME = 'firstname';
  const FIELD_LASTNAME = 'lastname';
  const FIELD_EMAIL = 'email';
  const FIELD_IMAGE = 'image';
  const FIELD_STATUS = 'status';
  const FIELD_DATE_ADDED = 'date_added';

  public static function verifyPassword($user = null, $password = null){
    $salt = $user->{self::FIELD_SALT};
    $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
    $computedPassword = sha1($salt . sha1($salt . sha1($password)));
    if ($computedPassword == $user->{self::FIELD_PASSWORD}){
        return true;
    }
    return false;
  }
  public static function token($length = 32) {
    $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $max = strlen($string) - 1;
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $string[mt_rand(0, $max)];
    }   
    return $token;
  }
}
