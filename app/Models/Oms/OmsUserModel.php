<?php
namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Reseller\ResellerBankDetailModel;
use App\Models\Reseller\ResellerProductModel;
use App\Models\Reseller\ResellerUserDetailModel;
use App\Models\Reseller\WithdrawRequestModel;
use Illuminate\Database\Eloquent\Model;

class OmsUserModel extends Model
{
    protected $table = 'oms_user';
    protected $primaryKey = "user_id";

    const FIELD_USER_ID = 'user_id';
    const FIELD_USER_GROUP_ID = 'user_group_id';
    const FIELD_USER_NAME = 'username';
    const FIELD_FIRSTNAME = 'firstname';
    const FIELD_LASTNAME = 'lastname';
    const FIELD_EMAIL = 'email';
    const FIELD_COMMISSION = 'commission';
    const FIELD_COMMISSION_ON = 'commission_on';
    const FIELD_PASSWORD = 'password';
    const FIELD_SALT = 'salt';
    const FIELD_ROLE = 'role';
    const FIELD_STATUS = 'status';
    
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

    public function userGroupName() {
       return $this->belongsTo('App\Models\Oms\OmsUserGroupModel', 'user_group_id')->select(array('id', 'name'));
    }
    public function userGroup() {
        return $this->belongsTo('App\Models\Oms\OmsUserGroupModel', 'user_group_id');
     }
 
    public function activities() {
        return $this->belongsToMany(DutyListsModel::class,'duty_assigned_users', 'user_id', 'activity_id')->select('user_id','activity_id','point','quantity','duration','name','per_quantity_point', 'monthly_tasks','daily_compulsory');
    }

    public function performance_sales() {
        return $this->hasMany(EmployeePerformanceModel::class, 'user_id');
    }

    public function customDuties() {
        return $this->hasMany(EmployeeCustomeDutiesModel::class, 'user_id');
    }

    public function dutyStatusHistories() {
        return $this->hasMany(EmployeeDutyStatusHistoryModel::class, 'user_id');
    }

    public function notifacations() {
        return $this->hasMany(OmsNotificationModel::class, 'user_id');
    }

    public function assignedCustomeDuties() {
        return $this->hasMany(DutyAssignedUserModel::class, 'user_id');
    }

    public function payments() {
        return $this->hasMany(UserPaymentModel::class, 'user_id')->orderBy('month', 'DESC');
    }

     public function todayTime() {
        return $this->hasMany(UserStartEndTimeModel::class, 'user_id')->where('date', date('Y-m-d'));
    }

    public function smart_look() {
        return $this->hasMany(SmartLookModel::class, 'user_id');
    }

    public function employeeLeaves() {
        return $this->hasMany(EmployeeLeaveModel::class, 'user_id');
    }

    public function doneDutyHistories() {
        return $this->hasMany(DoneDutyHistroryModel::class, 'user_id');
    }

    public function products() {
        return $this->belongsToMany(OmsInventoryProductModel::class, 'reseller_products', 'user_id', 'product_id');
    }

    public function detail() {
        return $this->hasOne(ResellerUserDetailModel::class, 'user_id');
    }

    public function withdrawRequests() {
        return $this->hasMany(WithdrawRequestModel::class, 'reseller_id');
    }
    public function bankDetails() {
        return $this->hasMany(ResellerBankDetailModel::class, 'reseller_id');
    }

    public function campaigns() {
        return $this->belongsToMany(PaidAdsCampaign::class, 'campaign_users', 'user_id', 'campaign_id');
    } 
}