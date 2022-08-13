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
class CategoryDescriptionModel extends AbstractOpenCartModel
{

  protected $table = 'category_description';
  protected $primaryKey = 'category_id';

  const FIELD_CATEGORY_ID = 'category_id';
  const FIELD_NAME = 'name';

}
