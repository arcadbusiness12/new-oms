<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class StockStatusModel extends AbstractDressFairOpenCartModel
{
    protected $table = 'stock_status';
    protected $primaryKey = 'stock_status_id';

    const FIELD_STOCK_STATUS_ID = 'stock_status_id';
    const FIELD_NAME = 'name';
}
