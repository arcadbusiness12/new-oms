<?php
/*
 * Exchange Order table model
 */
namespace App\Models\OpenCart\ExchangeOrders;
use App\Models\OpenCart\AbstractOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class ApiModel extends AbstractOpenCartModel{
    protected $table = 'api';
    protected $primaryKey = 'api_id';
}