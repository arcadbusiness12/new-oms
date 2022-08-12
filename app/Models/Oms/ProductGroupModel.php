<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\ProductPhotographyModel;
use DB;
class ProductGroupModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_inventory_product_groups';
    protected $fillable = ['name'];

    public function products() {
        return $this->hasMany(OmsInventoryProductModel::class, 'group_id');
    }
    public function photographyProducts() {
      return $this->hasOne(ProductPhotographyModel::class, 'product_group_id');
  }
  public function producType() {
    return $this->belongsTo(PromotionTypeModel::class, 'product_type_id');
  } 
  public function category() {
      return $this->belongsTo(GroupCategoryModel::class, 'category_id');
  }
  public function productsQuantity() {
    return $this->hasMany(OmsInventoryProductModel::class, 'group_id')
            ->join('oms_inventory_product_option','oms_inventory_product_option.product_id','=','oms_inventory_product.product_id')
            ->selectRaw('*,SUM(available_quantity) AS total_available_quantity')
            ->groupBy('oms_inventory_product_option.product_id')
            ->having(DB::raw('SUM(available_quantity)'),'>',0);
  }

}
