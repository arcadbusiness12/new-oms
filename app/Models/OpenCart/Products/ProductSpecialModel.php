<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

/*
 * Opencart Product table Model
 */

/**
 * Description of ProductsModel
 *
 * @author kamran
 */
class ProductSpecialModel extends AbstractOpenCartModel
{

  protected $table = 'product_special';
  protected $primaryKey = 'product_special_id';
  protected $fillable = ['product_id', 'customer_group_id', 'date_start','date_end','price','priority'];
  const FIELD_PRODUCT_ID = 'product_id';

}
