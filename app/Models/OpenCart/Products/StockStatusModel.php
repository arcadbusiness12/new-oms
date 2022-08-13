<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

class StockStatusModel extends AbstractOpenCartModel
{
    protected $table = 'stock_status';
    protected $primaryKey = 'stock_status_id';

    const FIELD_STOCK_STATUS_ID = 'stock_status_id';
    const FIELD_NAME = 'name';
}
