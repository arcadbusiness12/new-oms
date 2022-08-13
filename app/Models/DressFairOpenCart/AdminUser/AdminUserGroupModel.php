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
class AdminUserGroupModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'user_group';
  protected $primaryKey = 'user_group_id';

  const FIELD_USER_GROUP_ID = 'user_group_id';
  const FIELD_GROUP_NAME = 'name';
  const FIELD_PERMISSIONS = 'permission';

}
