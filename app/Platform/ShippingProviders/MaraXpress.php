<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;

/**
 * Description of MaraXpress
 *
 * @author kamran
 */
class MaraXpress implements ShippingProvidersInterface
{

    protected $apiUrl;
    protected $apiKey;

    const API_FORWARD_ORDER = 'forward';
    const API_ORDER_END_POINT = '/orders';
    const API_AWB_NUMBER = 'orderID';
    const API_KEY = 'apiKey';
    const API_TRACKING_URL = "http://maraxpress.com/track.aspx?TrackId=";
    const API_RESPONSE_MESSAGE = 'message';
    const MARA_API_ORDER_ID = 'orderID';
    const MARA_API_CUSTOMER_NAME = 'customerName';
    const MARA_API_CUSTOMER_CONTACT = 'customerContact';
    const MARA_API_CUSTOMER_ADDRESS = 'customerAddress';
    const MARA_API_CUSTOMER_CITY = 'customerCity';
    const MARA_API_CUSTOMER_EMAIL = 'customerEmail';
    const MARA_API_ORDER_AMOUNT = 'orderAmount';
    const MARA_API_ORDER_TYPE = 'orderType';
    const MARA_API_PAYMENT_METHOD = 'paymentMethod';
    const MARA_API_PRODUCT_DESCRIPTION = 'productDescription';
    const MARA_API_COMMENTS = 'comments';
    const MARA_API_AREA = 'area';

    /**
     * @var Client $httpClient Current Client is guzzle
     */
    protected $httpClient;

    public function __construct()
    {
        $this->apiKey = config('services.maraxpress')['apiKey'];
        $this->apiUrl = config('services.maraxpress')['url'];
        $this->httpClient = \App::make('httpClient'); // Http Client

        if (null == $this->apiKey || null == $this->apiUrl)
        {
            throw new \Exception("ApiKey and url for MaraXpress is required");
        }
    }

    public function forwardOrder($orders)
    {
        $returnResponse = [];
        // Expected array of OrderGOlem objects
        foreach ($orders as $order)
        {
            if (!$order instanceof OrderGolem)
            {
                throw new \Exception("Order needs to be an instance of orderGolem");
            }

            $dataToPots = [
                self::MARA_API_COMMENTS => $order->getSpecialInstructions(),
                self::MARA_API_CUSTOMER_ADDRESS => $order->getCustomerAddress(),
                self::MARA_API_CUSTOMER_CITY => $order->getCustomerCity(),
                self::MARA_API_CUSTOMER_CONTACT => $order->getCustomerMobileNumber(),
                self::MARA_API_CUSTOMER_EMAIL => $order->getCustomerEmail(),
                self::MARA_API_CUSTOMER_NAME => $order->getCustomerName(),
                self::MARA_API_ORDER_AMOUNT => $order->getOrderTotalAmount(),
                self::MARA_API_ORDER_ID => (string) $order->getOrderID(),
                self::MARA_API_ORDER_TYPE => self::API_FORWARD_ORDER,
                self::MARA_API_PAYMENT_METHOD => $order->getPaymentMethod(),
                self::MARA_API_PRODUCT_DESCRIPTION => $order->getGoodsDescription(),
                self::MARA_API_AREA => (string) $order->getCustomerArea()
            ];

            $data = ['json' => $dataToPots, 'headers' => [self::API_KEY => $this->apiKey]];
            // dd($this->apiKey);
            try
            {
                $response = $this->httpClient->post($this->apiUrl . self::API_ORDER_END_POINT, $data);
                $content = \json_decode($response->getBody()->getContents());

                $returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $content->success->{self::API_AWB_NUMBER},
                    self::MESSAGE_FROM_PROVIDER => $content->success->{self::API_RESPONSE_MESSAGE}
                ];
            }
            catch (\Exception $ex)
            {
                $returnResponse[$order->getOrderID()] = [ "",
                    self::MESSAGE_FROM_PROVIDER => $ex->getMessage()
                ];
                continue; // Skip orders failed to respond
            }
        }
        return $returnResponse; // Details of order with response 
    }

    public function getAirwayBillUrl($awbNumber)
    {
        return $this->apiUrl . "/orders/" . $awbNumber . "/pdf?apiKey=" . $this->apiKey;
    }

    public function getOrderStatus()
    {

    }

    public function printAirwaybill()
    {

    }

    public static function getTrackingUrl($awbNumber)
    {
        if (null == $awbNumber)
        {
            return;
        }

        return self::API_TRACKING_URL . $awbNumber;
    }

    //put your code here
}