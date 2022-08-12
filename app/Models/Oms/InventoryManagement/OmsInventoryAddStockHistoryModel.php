<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\OmsUserModel;
class OmsInventoryAddStockHistoryModel extends Model
{
    protected $table = 'oms_inventory_add_stock_history';
    protected $primaryKey = "history_id";

    const FIELD_HISTORY_ID = 'history_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_USER_ID = 'user_id';
    const FIELD_COMMENT = 'comment';

    public function user() {
        return $this->hasOne(OmsUserModel::class, 'user_id','user_id');
    }
}