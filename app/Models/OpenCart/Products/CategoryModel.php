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
class CategoryModel extends AbstractOpenCartModel
{

  protected $table = 'category';
  protected $primaryKey = 'category_id';

  const FIELD_CATEGORY_ID = 'category_id';
  const FIELD_PARENT_ID = 'parent_id';

}
