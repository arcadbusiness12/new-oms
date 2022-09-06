<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;
use SoapClient;
use SoapVar;
/**
 * Description of FetchrExpress
 *
 * @author Tariq
 */
class FetchrExpress implements ShippingProvidersInterface
{ 
    const DUMMY_AWB = 'FETCHR_';
    const API_AWB_NUMBER = 'orderID';
    const API_KEY = 'apiKey';

    protected $accountNumber;
    protected $apiUrl;
    protected $apiToken;
    protected $client_address_id;

    public function __construct(){
        $this->accountNumber = config('services.fetchrExpress')['accountNumber'];
        $this->apiUrl = config('services.fetchrExpress')['url'];
        $this->apiToken = config('services.fetchrExpress')['apiToken'];
        $this->client_address_id = config('services.fetchrExpress')['client_address_id'];
        $this->httpClient = \App::make('httpClient');

        if (null == $this->accountNumber || null == $this->apiUrl){
            throw new \Exception("ApiKey, Account Number and user Name for Fetchr Express is required");
        }
    }

    public function forwardOrder($orders){
        $returnResponse = [];

        foreach ($orders as $order){
            if (!$order instanceof OrderGolem){
                throw new \Exception("Order needs to be an instance of orderGolem");
            }

            $data_object = "{
                \"client_address_id\": \"".$this->client_address_id."\",
                \"data\": [
                    {
                    \"order_reference\": \"".substr($order->getOrderID(), 0, 40)."\",
                    \"name\": \"".substr($order->getCustomerName(), 0, 100)."\",
                    \"email\": \"".substr($order->getCustomerEmail(), 0, 70)."\",
                    \"phone_number\": \"".substr($order->getCustomerMobileNumber(), 0, 64)."\",
                    \"address\": \"".substr($order->getCustomerAddress(), 0, 600)."\",
                    \"receiver_country\": \"".$order->getCustomerCountry()."\",
                    \"receiver_city\": \"".$order->getCustomerCity()."\",
                    \"area\": \"".substr($order->getCustomerArea(), 0, 128)."\",
                    \"payment_type\": \"".$order->getPaymentMethod()."\",
                    \"bag_count\": 1,
                    \"description\": \"".substr($order->getGoodsDescription(), 0, 2000)."\",
                    \"comments\": \"".substr($order->getSpecialInstructions(), 0, 500)."\",
                    \"order_package_type\": \"nondocument\",
                    \"total_amount\": ".$order->getOrderTotalAmount().",
                    \"sms_company_name\": \"".$order->getCompanyName()."\",
                    \"items\": [";
                    $tmp = array();
                        foreach ($order->getOrderItems() as $value) {
                            $tmp[] = "{
                                \"description\": \"".$value['description']."\",
                                \"sku\": \"".$value['sku']."\",
                                \"quantity\": ".$value['quantity'].",
                                \"order_value_per_unit\": ".$value['order_value_per_unit']."
                            }";
                        }
                    $data_object .= implode(',', $tmp);
            $data_object .= "]
                    }
                ]
            }";

            try{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl . "order");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_object);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  "Content-Type: application/json",
                  "Authorization: Bearer ".$this->apiToken
                ));

                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
            }
            catch (\Exception $ex){
                continue;
            }
            if($response['status'] == 'success'){
                $responseData = $response['data'];
                $array = array(
                    'bill_id'   =>  $responseData[0]['so_no'],
                    'barcode'   =>  $responseData[0]['tracking_no'],
                    'awbNumber' =>  $responseData[0]['tracking_no'],
                    'awbLink'   =>  $responseData[0]['awb_link'],
                    'msg'       =>  $responseData[0]['message'],
                );
                $returnResponse[$order->getOrderID()] = $array;
            }else{
                $returnResponse[$order->getOrderID()] = array('msg' => $response['message']);
            }
        }
        return $returnResponse; // Details of order with response
    }

    public function getAirwayBillUrl($awbNumber = null){
        return "https://s3-eu-west-1.amazonaws.com/cms-dhl-pdf-stage-1/standard_".$awbNumber.".pdf";
    }

    public function getOrderStatus(){
    }

    public function printAirwaybill(){
    }

    public static function getTrackingUrl($awbNumber = null){
        return self::HASH_LINK;
    }
}