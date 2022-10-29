<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsCart extends Model
{
    // protected $table = 'oms_promotion_socials';
    protected $fillable = ['store_id','session_id','product_id','product_option_id','product_sku','product_name','product_image','product_color','product_quantity','product_price'];

    public function cartProductSize(){

        return $this->hasOne('App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel','product_option_id','product_option_id')
        ->join('oms_options_details', 'oms_options_details.id', '=', 'oms_inventory_product_option.option_value_id');
    }
}
