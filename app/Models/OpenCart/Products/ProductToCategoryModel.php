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
class ProductToCategoryModel extends AbstractOpenCartModel
{

  protected $table = 'product_to_category';
  protected $primaryKey = 'product_id';

  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_CATEGORY_ID = 'category_id';

}
