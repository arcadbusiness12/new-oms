<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'oms_user';
    
    protected $primaryKey = "user_id";
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_group_id',
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
        'commission',
        'commission_on',
        'salt',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userGroupName() {
        return $this->belongsTo('App\Models\Oms\OmsUserGroupModel', 'user_group_id')->select(array('id', 'name'));
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
}
