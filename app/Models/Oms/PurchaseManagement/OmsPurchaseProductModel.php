<?php
namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseProductModel extends Model
{
    protected $table = 'oms_purchase_product';
    protected $primaryKey = "product_id";

    const FIELD_ORDER_ID = 'product_id';
    const FIELD_NAME = 'name';
    const FIELD_IMAGE = 'image';
    const FIELD_DATE_ADDED = 'created_at';
    const FIELD_DATE_MODIFIED = 'updated_at';
}
