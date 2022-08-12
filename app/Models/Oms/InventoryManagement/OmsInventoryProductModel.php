<?php



namespace App\Models\Oms\InventoryManagement;

use App\Models\Oms\OmsUserModel;
use App\Models\Reseller\PriceHistoryModel;
use App\Models\Reseller\ResellerProductModel;
use Illuminate\Database\Eloquent\Model;



class OmsInventoryProductModel extends Model

{

    protected $table = 'oms_inventory_product';

    protected $primaryKey = "product_id";



    const FIELD_PRODUCT_ID = 'product_id';

    const FIELD_SKU = 'sku';

    const FIELD_IMAGE = 'image';

    

    const FIELD_OPTION = 'option_value';



    const FIELD_ROW = 'row';

    const FIELD_PRINT_LABEL = 'print_label';

    const FIELD_RACK = 'rack';

    const FIELD_SHELF = 'shelf';

    const FIELD_STATUS = 'status';

    

    public function ProductsSizes(){

        return $this->hasMany('App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel','product_id')
        ->join('oms_options_details', 'oms_options_details.id', '=', 'oms_inventory_product_option.option_value_id')
        ->orderBy("oms_options_details.sort");
    }
    public function omsOptions(){
        return $this->hasOne('App\Models\Oms\InventoryManagement\OmsOptions','id',"option_value");
    }
    public function productGroups() {
      return $this->hasOne('App\Models\Oms\ProductGroupModel', 'id','group_id');
    }
    public function stockLevels() {
        return $this->hasMany(OmsInventoryStockModel::class, 'product_id');
      }

    public function resellers() {
      return $this->belongsToMany(OmsUserModel::class, 'reseller_products', 'product_id', 'user_id')->select('group_id','product_id','sku','price','username');
    }

    public function prices() {
      return $this->hasMany(ResellerProductModel::class, 'product_id')->take(1)->orderBy('created_at','DESC');
  }
  public function productOptions(){

    return $this->hasMany(OmsInventoryProductOptionModel::class, 'product_id');
}
    // public function omsOptionDetails()
    // {
    //     return $this->hasOneThrough(
    //         'App\Models\Oms\InventoryManagement\OmsDetails',
    //         'App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel',
    //         'product_id', // Foreign key on OmsInventoryProductOptionModel table...
    //         'options', // Foreign key on OmsDetails table...
    //         'product_id', // Local key on mechanics table...
    //         'option_value_id' // Local key on cars table...
    //     );
    // }

}



