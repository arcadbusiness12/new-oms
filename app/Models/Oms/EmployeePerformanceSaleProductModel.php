<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsPlaceOrderModel
 *
 * @author kamran
 */
class EmployeePerformanceSaleProductModel extends Model
{

    protected $table = 'oms_employee_performance_sales_product';
    protected $primaryKey = "id";
    protected $fillable = ['employee_performance_sales_id','product_group_id','product_group_name','posting_type'];
    protected $guarded = ['id'];

}