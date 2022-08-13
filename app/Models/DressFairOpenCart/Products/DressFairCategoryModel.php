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
class DressFairCategoryModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'category';
  protected $primaryKey = 'category_id';

  const FIELD_CATEGORY_ID = 'category_id';
  const FIELD_PARENT_ID = 'parent_id';

}
