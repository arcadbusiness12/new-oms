<?php

/*
 * Admin Login 
 */

namespace App\Models\OpenCart\AdminUser;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of AdminUserModel
 *
 * @author Kamran Adil
 */
class AdminUserGroupModel extends AbstractOpenCartModel
{

  protected $table = 'user_group';
  protected $primaryKey = 'user_group_id';

  const FIELD_USER_GROUP_ID = 'user_group_id';
  const FIELD_GROUP_NAME = 'name';
  const FIELD_PERMISSIONS = 'permission';

}
