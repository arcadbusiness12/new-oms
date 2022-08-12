<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsPlaceOrderModel
 *
 * @author kamran
 */
class EmployeePerformanceSaleModel extends Model
{

    protected $table = 'oms_employee_performance_sales';
    protected $primaryKey = "id";
    protected $fillable = ['conversation_opening', 'conversation_closing', 'user_id','date'];
    protected $guarded = ['id'];
  public function sale_products()
  {
    return $this->hasMany(__NAMESPACE__ . '\EmployeePerformanceSaleProductModel',"employee_performance_sales_id", "id");
  }
  public function sale_person()
  {
    return $this->hasOne(__NAMESPACE__ . '\OmsUserModel',"user_id", "user_id");
  }

}