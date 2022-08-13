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
class PromotionProductModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'promotion_product';
  protected $primaryKey = 'id';
  protected $fillable = ['id','product_id','special_id','value','date_expire','quantity','type','days_into_hour','org_quantity'];
  
  const FIELD_PRODUCT_ID = 'product_id';

}
