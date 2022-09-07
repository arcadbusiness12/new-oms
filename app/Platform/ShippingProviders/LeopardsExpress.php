<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;
use SoapClient;
use SoapVar;
/**
 * Description of LeopardsExpress
 *
 * @author Tariq
 */
class LeopardsExpress implements ShippingProvidersInterface
{ 
    const DUMMY_AWB = 'LEOPARDS_';
    const API_AWB_NUMBER = 'orderID';
    const API_KEY = 'apiKey';

    protected $accountNumber;
    protected $apiUrl;

    public function __construct(){
        $this->accountNumber = config('services.leopardsExpress')['accountNumber'];
        $this->apiUrl = config('services.leopardsExpress')['url'];
        $this->httpClient = \App::make('httpClient');

        if (null == $this->accountNumber || null == $this->apiUrl){
            throw new \Exception("ApiKey, Account Number and user Name for Leopards Express is required");
        }
    }

    public function forwardOrder($orders){
        $returnResponse = [];

        foreach ($orders as $order){
            if (!$order instanceof OrderGolem){
                throw new \Exception("Order needs to be an instance of orderGolem");
            }
            
            if(trim($order->getPaymentMethod()) == 'cod' || trim($order->getPaymentMethod()) == 'cod_order_fee' || trim($order->getPaymentMethod()) == '') {
                $amount = $order->getOrderTotalAmount();
            }else {
                $amount = '0.00';
            }

            $client = new SoapClient($this->apiUrl, array('soap_version' => SOAP_1_1,'trace' => true)); 
            $xml = '<arg0>
                <ToCompany>'.$order->getToCompany().'</ToCompany>
                <ToAddress>'.$order->getCustomerAddress().'</ToAddress>
                <ToLocation>'.$order->getCustomerArea().'</ToLocation>
                <ToCountry>'.$order->getCustomerCountry().'</ToCountry>
                <ToCperson>'.$order->getCustomerName().'</ToCperson>
                <ToContactno>'.$order->getCustomerMobileNumber().'</ToContactno>
                <ToMobileno>'.$order->getCustomerMobileNumber().'</ToMobileno>
                <ReferenceNumber>'.$order->getOrderID().'</ReferenceNumber>
                <CompanyCode>'.$this->accountNumber.'</CompanyCode>
                <Pieces>'.$order->getTotalItemsQuantity().'</Pieces>
                <Weight>0.00</Weight>
                <PackageType>Non Document</PackageType>
                <NcndAmount>'.$amount.'</NcndAmount>
                <ItemDescription>'.$order->getGoodsDescription().'</ItemDescription>
                <SpecialInstruction>'.$order->getSpecialInstructions().'</SpecialInstruction>
            </arg0>';

            $args = array(new SoapVar($xml, XSD_ANYXML));
            try{
                $result = $client->__soapCall('Booking', $args);
            }
            catch (\Exception $ex){
                continue;
            }
            if($result->responseCode == '1'){
                $awbNumber = $result->responseArray;
                $array = array(
                    'bill_id'   =>  $awbNumber[0]->AWBNumber,
                    'barcode'   =>  $awbNumber[0]->AWBNumber,
                    'awbNumber' =>  $awbNumber[0]->AWBNumber,
                    'msg'       =>  $result->responseMessage,
                );
                $returnResponse[$order->getOrderID()] = $array;
            }else{
                $returnResponse[$order->getOrderID()] = array('msg' => $result->responseMessage);
            }
        }
        return $returnResponse; // Details of order with response
    }

    public function getAirwayBillUrl($awbNumber = null){
        return self::HASH_LINK;
    }

    public function getOrderStatus(){
    }

    public function printAirwaybill(){
    }

    public static function getTrackingUrl($awbNumber = null){
        return self::HASH_LINK;
    }
}