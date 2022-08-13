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
class ProductsDescriptionModel extends AbstractOpenCartModel
{

  protected $table = 'product_description';
  protected $primaryKey = 'product_id';

  const FIELD_PRODUCT_ID = 'product_id';

}
