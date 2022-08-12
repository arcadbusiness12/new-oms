<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseTabsModel extends Model
{
    protected $table = 'oms_purchase_tabs';
    protected $primaryKey = "tab_id";

    const FIELD_TAB_ID = 'tab_id';
    const FIELD_NAME = 'name';
    const FIELD_URL = 'url';
    const FIELD_SORT_ORDER = 'sort_order';
}
