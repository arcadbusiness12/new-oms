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
class ApiIpModel extends AbstractDressFairOpenCartModel{
    protected $table = 'api_ip';
    protected $primaryKey = 'api_ip_id';

    const FIELD_API_IP_ID = 'api_ip_id';
    const FIELD_API_ID = 'api_id';
    const FIELD_IP = 'ip';
}