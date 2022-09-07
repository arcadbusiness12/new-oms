<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use App\Platform\ShippingProviders\ShippingProvidersInterface;

/**
 * Description of GetGive
 *
 * @author Kamran Adil
 */
class GetGive implements ShippingProvidersInterface
{

    protected $apiUrl;
    protected $apiKey;

    /**
    * @var \GuzzleHttp\Client $httpClient Current Client is guzzle
    */
    protected $httpClient;

    const API_ORDER_END_POINT = '/AWBCreation';
    const API_ORDER_STATUS_END_POINT = '/GetStatus';
    const API_AWB_END_POINT = 'http://getgive.dynalias.com:880/Onlinebooking/Report.aspx?FrmType=AWB&AwbNo=';
    const API_TRACKING_URL = 'http://getgive.dynalias.com:880/tracking/TrackDetails.Aspx?AWB_ID=';
    const API_KEY = 'get_give_key';
    const API_URL = 'get_give_url';
    const API_FORWARD_ORDER = 'forward';
    const API_ORDER_ID = 'shipperRef';
    const API_CUSTOMER_NAME = 'consigneeName';
    const API_CUSTOMER_CONTACT = 'consigneeMob';
    const API_CUSTOMER_ADDRESS = 'consigneeAddress1';
    const API_CUSTOMER_CITY = 'consigneeCity';
    const API_CUSTOMER_EMAIL = 'consigneePhone';
    const API_ORDER_AMOUNT = 'codAmount';
    const API_PRODUCT_DESCRIPTION = 'goodDescription';
    const API_COMMENTS = 'specialInstruction';
    const API_ACCOUNT_NUMBER = 'accounNo';
    const API_AREA = 'area';
    const API_ORDER_TYPE = 'orderType';
    const API_AWB_NUMBER = 'awbNo';
    const API_PAYMENT_METHOD = 'paymentMethod';
    const API_SERVICE_TYPE = 'serviceType';
    const CURRENT_ORDER_STATUS = 'orderStatus';
    const API_RESPONSE_MESSAGE = 'description';
    const GETGIVE_COD_VAL = 'CD';
    const GETGIVE_SERVICE_TYPE_VAL = 'COD';

    public function __construct() // Laravel Container injection
    {
        $this->apiKey = config('services.getgive')['apiKey'];
        $this->apiUrl = config('services.getgive')['url'];
        $this->httpClient = \App::make('httpClient'); // Http Client 

        if (null == $this->apiKey || null == $this->apiUrl)
        {
          throw new \Exception("ApiKey and url for GetGive is required");
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

            $paymentMehotd = $order->getPaymentMethod();
            if ($paymentMehotd == "cod" || $paymentMehotd == 'cod_order_fee' || $paymentMehotd == '')
            {
                $paymentMehotd = self::GETGIVE_COD_VAL;
            }

            $dataToPots = [
                self::API_COMMENTS => $order->getSpecialInstructions(),
                self::API_CUSTOMER_ADDRESS => $order->getCustomerAddress(),
                self::API_CUSTOMER_CITY => $order->getCustomerCity(),
                self::API_CUSTOMER_CONTACT => $order->getCustomerMobileNumber(),
                self::API_CUSTOMER_EMAIL => $order->getCustomerEmail(),
                self::API_CUSTOMER_NAME => $order->getCustomerName(),
                self::API_ORDER_AMOUNT => $order->getOrderTotalAmount(),
                self::API_ORDER_ID => $order->getOrderID(),
                self::API_ORDER_TYPE => self::API_FORWARD_ORDER,
                self::API_PAYMENT_METHOD => $paymentMehotd,
                self::API_PRODUCT_DESCRIPTION => $order->getGoodsDescription(),
                self::API_AREA => $order->getCustomerArea(),
                self::API_ACCOUNT_NUMBER => $this->apiKey,
                self::API_SERVICE_TYPE => self::GETGIVE_SERVICE_TYPE_VAL,
                //self::CURRENT_ORDER_STATUS => $results['order_status_id'],	
            ];

            $data = ['json' => $dataToPots];

            try
            {
                $response = $this->httpClient->post($this->apiUrl . self::API_ORDER_END_POINT, $data);
                $content = \json_decode($response->getBody()->getContents());

                $returnResponse[$order->getOrderID()] = [self::AIRWAYBILL_NUMBER => $content->{self::API_AWB_NUMBER},
                    self::MESSAGE_FROM_PROVIDER => $content->{self::API_RESPONSE_MESSAGE}
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

    public function getOrderStatus()
    {

    }

    public function printAirwaybill()
    {

    }

    public function getAirwayBillUrl($awbNumber)
    {
        if (null == $awbNumber)
        {
          return;
        }
        return self::API_AWB_END_POINT . $awbNumber;
    }

    public static function getTrackingUrl($awbNumber)
    {
        if (null == $awbNumber)
        {
          return;
        }

        return self::API_TRACKING_URL . $awbNumber;
    }
}