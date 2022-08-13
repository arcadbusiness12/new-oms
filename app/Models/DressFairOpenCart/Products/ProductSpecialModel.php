<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/*
 * Opencart Product table Model
 */

/**
 * Description of ProductsModel
 *
 * @author kamran
 */
class ProductSpecialModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product_special';
  protected $primaryKey = 'product_special_id';
  protected $fillable = ['product_id', 'customer_group_id', 'date_start','date_end','price','priority'];
  const FIELD_PRODUCT_ID = 'product_id';

}
