<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;
use SoapClient;
use SoapVar;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsActivityLogModel;
use Illuminate\Support\Facades\Http;
/**
 * Description of TcsCourier
 *
 * @author Tariq
 */
class TcsCourier implements ShippingProvidersInterface
{
    const DUMMY_AWB = 'TCS_';
    const API_AWB_NUMBER = 'orderID';
    const API_KEY = 'apiKey';

    protected $accountNumber;
    protected $apiUrl;
    protected $apiKey;
    protected $username;
    protected $password;
    protected $cities = array();
    protected $countries = array();
    protected $origins = array();

    public function __construct(){
        $this->accountNumber = config('services.tcsCourier')['accountNumber'];
        $this->apiUrl = config('services.tcsCourier')['url'];
        $this->apiKey = config('services.tcsCourier')['apiKey'];
        $this->username = config('services.tcsCourier')['username'];
        $this->password = config('services.tcsCourier')['password'];
        // $this->httpClient = \App::make('httpClient');

        if (null == $this->apiKey || null == $this->apiUrl || null == $this->accountNumber){
            throw new \Exception("ApiKey, Account Number and user Name for TCS Courier is required");
        }

        // $this->getCountries();
        // $this->getOrigins();
        // $this->getCities();
    }

    public function forwardOrder($orders){
        $returnResponse = [];

        foreach ($orders as $order){
            if (!$order instanceof OrderGolem){
                throw new \Exception("Order needs to be an instance of orderGolem");
            }

            try{
              // dd(trim($order->getPaymentMethod()));
              if(trim($order->getPaymentMethod()) == 'cod' || trim($order->getPaymentMethod()) == 'cod_order_fee' || trim($order->getPaymentMethod()) == ''){
                // $payment_method = "Cash";
                $COD_AMOUNT = $order->getOrderTotalAmount();
              }else if( trim($order->getPaymentMethod()) == 'ccavenuepay' ){
                // $payment_method = "0";
                $COD_AMOUNT = "0.00";
              }else{
                $COD_AMOUNT = $order->getOrderTotalAmount();
              }
              // dd($COD_AMOUNT);
              if( $order->getCustomerAlternateNumber() != "" ){
                $alternat_number = $order->getCustomerAlternateNumber();
              }else{
                $alternat_number = $order->getCustomerMobileNumber();
              }
            	$data_object = "USER=".$this->username."&APIKEY=".rawurlencode($this->password)."&TCS_ACCOUNT_NO=".$this->accountNumber."&CNSGEE_NAME=".rawurlencode($order->getCustomerName())."&CNSGEE_ADDRESS1=".rawurlencode($order->getCustomerAddress())."&CNSGEE_ADDRESS2=".rawurlencode($order->getCustomerArea())."&CNSGEE_ADDRESS3=".rawurlencode($order->getCustomerArea())."&CNSGEE_PHONE=".rawurlencode($order->getCustomerMobileNumber())."&CNSGEE_CELL=".rawurlencode($alternat_number)."&NO_PCS=".$order->getTotalItemsQuantity()."&COUNTRY=DXB&WEIGHT=1&COD_AMOUNT=".$COD_AMOUNT."&ORDER_ID=".$order->getOrderID()."&PARAMETER=1&P_CNNO=&P_ERROR=&ITEM=".rawurlencode($order->getGoodsDescription());
            	// echo "<pre>";print_r($data_object);echo "</pre>";die;

            	$curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => $this->apiUrl . "Ship4Me_Booking_INTL",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data_object,
                CURLOPT_HTTPHEADER => array("Content-Type: application/x-www-form-urlencoded"),
              ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $response_data = json_decode($response, true);
                // echo "<pre>";print_r($response_data);echo "</pre>";die;

                if(empty($response_data['ERROR'])){
                	$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $response_data['CN_NO'],
					    self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage'
					];
                }else{
                	throw new \Exception($response_data['ERROR']);
                }
            }
            catch (\Exception $ex){
                throw new \Exception($ex->getMessage());
            }
        }
        return $returnResponse; // Details of order with response
    }

    protected function getCountries(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . "countries",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "x-ibm-client-id: " . $this->apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);

        if($response_data['returnStatus']['code'] == '0200'){
            $this->countries = $response_data['allCountries'];
        }
    }

    protected function getOrigins(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . "origins",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "x-ibm-client-id: " . $this->apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);

        if($response_data['returnStatus']['code'] == '0200'){
            $this->cities = $response_data['allOrigins'];
        }
    }

    protected function getCities(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . "cities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "x-ibm-client-id: " . $this->apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);

        if($response_data['returnStatus']['code'] == '0200'){
            $this->cities = $response_data['allCities'];
        }
    }

    public function getAirwayBillUrl($awbNumber = null){
        return "#";
    }

    public function getOrderStatus(){
    }

    public function printAirwaybill(){
    }

    public static function getTrackingUrl($awbNumber = null){
        return self::HASH_LINK;
    }
    public function getOrderTrackingHistory($awb_data,$awb_type=0){
      // echo "<pre>"; print_r($awb_data); die;
      $awbNumber = $awb_data->airway_bill_number;
      $order_id  = $awb_data->order_id;
      $store_id  = $awb_data->store;
      $returnResponse = [];
        $track_url = "https://api.tcscourier.com/production/track/v1/shipments/detail?consignmentNo=$awbNumber";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $track_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "x-ibm-client-id: " . $this->apiKey
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response_data = json_decode($response, true);
        // echo "<pre>"; print_r($response_data);
        if( $response_data['returnStatus']['status'] == 'SUCCESS' && $response_data['returnStatus']['code'] == '0200'  ){
          $DeliveryInfo = @$response_data['TrackDetailReply']['DeliveryInfo'];
          $shipment_address = '';
          $activity =[];
          if( is_array($DeliveryInfo) && count($DeliveryInfo) > 0 ){
            foreach($DeliveryInfo as $key => $val){
              if( $val['status'] == "DELIVERED" ){
                if( $awb_type == 0 ){
                  AirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response]);
                  $ba_class = app(\App\Http\Controllers\Orders\OrdersController::class)->deliverSingleOrder($order_id,$store_id);
                }elseif( $awb_type == 1 ){
                  ExchangeAirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response]);
                  $ba_class = app(\App\Http\Controllers\Exchange\ExchangeOrdersController::class)->deliverSingleOrder($order_id,$store_id);
                }
              }else if( trim($val['status']) == "Ready for Return" OR trim($val['status']) == "RETURN TO SHIPPER" ){
               $record = OmsOrdersModel::where("order_id",$order_id)->where("store",$store_id)->where("oms_order_status",3)->whereNull("ready_for_return")->update(['ready_for_return'=>1]);
               if( $record ){
                $activity = new OmsActivityLogModel();
                $activity->activity_id = 26; //Ready For Return
                $activity->ref_id  = $order_id;
                $activity->store   = $store_id;
                // $activity->comment = $comment;
                $activity->created_by_courier = 10;
                $activity->save();
               }
              }
              $activity[] = array(
                'datetime'=>$val['dateTime'],
                'status'=>$val['status'],
                'details'=>$val['recievedBy']."-".$val['station'],
              );
            }
          }
          $returnResponse = ['awb_number' => '','current_status' => '','status_datetime' => '',
          'shipment_address' => $shipment_address,'activity'=>$activity];
          return $returnResponse;
        }
  }
}
