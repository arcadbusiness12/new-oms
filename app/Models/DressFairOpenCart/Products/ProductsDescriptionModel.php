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
class ProductsDescriptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product_description';
  protected $primaryKey = 'product_id';

  const FIELD_PRODUCT_ID = 'product_id';

}
