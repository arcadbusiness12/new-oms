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
class ProductSkuModel extends AbstractOpenCartModel
{
	protected $table = 'product_sku';
	protected $primaryKey = 'product_sku_id';

	const FIELD_PRODUCT_SKU_ID = 'product_sku_id';
	const FIELD_PRODUCT_ID = 'product_id';
	const FIELD_PRODUCT_OPTION = 'product_option';
	const FIELD_SKU = 'sku';
}