<?php
/*
 * Exchange Order table model
 */
namespace App\Models\DressFairOpenCart\ExchangeOrders;
use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class ApiModel extends AbstractDressFairOpenCartModel{
    protected $table = 'api';
    protected $primaryKey = 'api_id';
}