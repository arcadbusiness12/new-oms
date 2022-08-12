<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class DutyModel extends Model
{
  public $timestamps = false;
    protected $table = 'duties';
    protected $primaryKey = "id";
    protected $fillable = ['name'];
  public function dutyLists()
  {
    return $this->hasMany(DutyListsModel::class,"duty_id", "id");
  }

  public function userGroups() {
    return $this->hasMany(OmsUserGroupModel::class, 'duty_id');
  }
 
}
