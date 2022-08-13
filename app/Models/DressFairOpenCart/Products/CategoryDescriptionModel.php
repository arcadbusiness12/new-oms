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
class CategoryDescriptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'category_description';
  protected $primaryKey = 'category_id';

  const FIELD_CATEGORY_ID = 'category_id';
  const FIELD_NAME = 'name';

}
