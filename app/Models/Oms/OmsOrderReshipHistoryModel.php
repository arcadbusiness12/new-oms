<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class OmsOrderReshipHistoryModel extends Model
{

  protected $table = 'oms_order_reship_history';
  protected $primaryKey = "id";
  protected $fillable = ['order_id', 'comment'];


}
