<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;

/**
 * Description of TfmExpress
 *
 * @author kamran
 */
class ShamilExpress implements ShippingProvidersInterface
{
    const DUMMY_AWB = 'SHAMIL_';
    const API_ADD_AWB_BILL = '/Api/apiAddAirwaybill';
    const API_CREATE_AWB = '/Api/apiAddInvoice';
    const API_EDIT_AWB = '/Api/apiEditInvoice';
    const API_CLOSE_AWB = '/Api/closeAirwayBill';
    const API_AWB_NUMBER = 'orderID';
    const API_KEY = 'apiKey';

    protected $accountNumber;
    protected $apiUrl;

    public function __construct(){
        $this->accountNumber = config('services.shamilExpress')['accountNumber'];
        $this->apiUrl = config('services.shamilExpress')['url'];
        $this->httpClient = \App::make('httpClient');

        if (null == $this->accountNumber || null == $this->apiUrl){
            throw new \Exception("ApiKey,Account Number and user Name for Shamil Express is required");
        }
    }

    public function forwardOrder($orders){
        $returnResponse = [];

        // Create AWB Bill ID
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . self::API_ADD_AWB_BILL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->accountNumber);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $booking = curl_exec ($ch);
            curl_close($ch);
            $booking = json_decode($booking, true);
            $bookingNumber = $booking['data']['billid'];
        }
        catch (\Exception $ex){
            return [];
        }

        foreach ($orders as $order){
            if (!$order instanceof OrderGolem){
                throw new \Exception("Order needs to be an instance of orderGolem");
            }
            $data = "token=".$this->accountNumber.
                    "&bill_id=".$bookingNumber.
                    "&date=".$order->getOrderDate().
                    "&invoice_number=".$order->getInvoiceNumber().
                    "&items=".$order->getGoodsDescription().
                    "&price=".$order->getOrderTotalAmount().
                    "&delivery_charges=0".
                    "&receiver_name=".$order->getCustomerName().
                    "&receiver_address=".$order->getCustomerAddress().
                    "&receiver_city=".$order->getCustomerCity().
                    "&receiver_contact=".$order->getCustomerMobileNumber();
            try{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->apiUrl . self::API_CREATE_AWB);
                curl_setopt($ch, CURLOPT_POST, 11);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec ($ch);
                curl_close($ch);
            }
            catch (\Exception $ex){
                continue;
            }
            $close_data = "token=".$this->accountNumber.
                    "&bill_id=".$bookingNumber;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl . self::API_CLOSE_AWB);
            curl_setopt($ch, CURLOPT_POST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $close_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $close = curl_exec ($ch);
            curl_close($ch);
            
            $awbNumber = json_decode($response, true);
            if($awbNumber['response'] == 'success'){
                $array = array(
                    'bill_id'   =>  $awbNumber['data']['bill_id'],
                    'barcode'   =>  $awbNumber['data']['barcode'],
                    'awbNumber' =>  $awbNumber['data']['barcode'],
                    'msg'       =>  $awbNumber['message'],
                );
                $returnResponse[$order->getOrderID()] = $array;
            }else{
                $returnResponse[$order->getOrderID()] = array('msg' => $awbNumber['message']);
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