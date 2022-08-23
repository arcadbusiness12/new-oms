<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryProductOptionModel extends Model
{
    protected $table = 'oms_inventory_product_option';
    protected $primaryKey = "product_option_id";

    const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
    const FIELD_PRODUCT_ID 	= 'product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_AVAILABLE_QUANTITY = 'available_quantity';
    const FIELD_ONHOLD_QUANTITY = 'onhold_quantity';
    const FIELD_PACK_QUANTITY = 'pack_quantity';
    const FIELD_SHIPPED_QUANTITY = 'shipped_quantity';
    const FIELD_DELIVERED_QUANTITY = 'delivered_quantity';
    const FIELD_RETURN_QUANTITY = 'return_quantity';
    const FIELD_UPDATED_QUANTITY = 'updated_quantity';
    const FIELD_RACK = 'rack';
    const FIELD_SHELF = 'shelf';
    public function omsOptionDetails(){
     return $this->belongsTo('App\Models\Oms\InventoryManagement\OmsDetails', 'option_value_id', 'id')->orderBy('sort');
 }

    public function optionValue()
    {
        return $this->belongsTo(OmsOptionValueDescriptionModel::class, 'option_value_id');
    }

    public function option()
    {
        return $this->belongsTo(OmsOptions::class, 'option_id');
    }

    public function product() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
}
