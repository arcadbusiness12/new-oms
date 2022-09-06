<?php
namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;
/**
 * Description of ShafiExpress
 *
 * @author Tariq
 */
class AkbarExpress implements ShippingProvidersInterface
{ 
  /**
   * @var \GuzzleHttp\Client $httpClient Current Client is guzzle
   */ 
    public function __construct() // Laravel Container injection
    {

    }
    public function forwardOrder($orders)
    {
        $returnResponse = [];
        foreach ($orders as $order)
        {
            if (!$order instanceof OrderGolem)
            {
                throw new \Exception("Order needs to be an instance of orderGolem");
            }
            try
            {
                $returnResponse[$order->getOrderID()] = [self::AIRWAYBILL_NUMBER => $order->getOrderID(),
                    self::MESSAGE_FROM_PROVIDER => ''
                ];
            }
            catch (\Exception $ex)
            {
                $returnResponse[$order->getOrderID()] = ["",
                    self::MESSAGE_FROM_PROVIDER => $ex->getMessage()
                ];
                continue; // Skip orders failed to respond
            }
        }

        return $returnResponse; // Details of order with response 
    }
    public function getOrderTrackingHistory(){
      
    }
    public function getOrderStatus()
    {

    }

    public function printAirwaybill()
    {

    }

    public function getAirwayBillUrl($awbNumber)
    {

    }

    public static function getTrackingUrl($awbNumber)
    {

    }
}