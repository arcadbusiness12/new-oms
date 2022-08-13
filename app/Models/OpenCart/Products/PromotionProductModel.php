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
class PromotionProductModel extends AbstractOpenCartModel
{

  protected $table = 'promotion_product';
  protected $primaryKey = 'id';
  protected $fillable = ['id','product_id','special_id','value','date_expire','quantity','type','days_into_hour','org_quantity'];
  const FIELD_PRODUCT_ID = 'product_id';

}
