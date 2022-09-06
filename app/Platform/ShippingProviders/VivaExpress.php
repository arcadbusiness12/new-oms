<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use GuzzleHttp\Client;
use SoapClient;
use SoapVar;
// use App\Models\Oms\ShippingProvidersModel;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;

/**
 * Description of VivaExpress
 *
 * @author kamran
 */
class VivaExpress implements ShippingProvidersInterface
{

	const DUMMY_AWB = 'VIVA_';
	const API_CREATE_AWB = '/rest/addAwb';
	const API_EDIT_AWB = '/rest/editAwb';
	const API_AWB_NUMBER = 'orderID';
	const API_KEY = 'apiKey';

	protected $accountNumber;
	protected $apiUrl;

	public function __construct()
	{
		$this->accountNumber = config('services.vivaExpress')['apiKey'];
		$this->apiUrl = config('services.vivaExpress')['url'];
		$this->httpClient = \App::make('httpClient'); // Http Client

		if (null == $this->accountNumber || null == $this->apiUrl)
		{
		  throw new \Exception("ApiKey, URL is required for Viva Express");
		}
	}

	public function forwardOrder($orders)
  	{   

        $returnResponse = [];

        foreach ($orders as $order){
            
            if (!$order instanceof OrderGolem){
                throw new \Exception("Order needs to be an instance of orderGolem");
            }
            
            if($order->getPaymentMethod() == 'cod' || $order->getPaymentMethod() == 'cod_order_fee' || $order->getPaymentMethod() == '') {
                $amount = $order->getOrderTotalAmount();
            }else {
                $amount = '0.00';
            }
            if($order->getStore()==2){
              $FromCompany   = "DressFair";
              $origin_address_line1 = "IndustrialArea 11";
              $origin_address_line2  = "Sharjah";
              $FromContactno = "971565651133";
              $origin_postal_code = "";
              $FromCperson = "Dressfair";
            }else{
              $FromCompany   = "BusinessArcade";
              $origin_address_line1 = "International City";
              $origin_address_line2  = "Dubai";
              $FromContactno = "971565634477";
              $origin_postal_code = "DXB";
              $FromCperson = "BusinessArcade";
            }
            $order_id = $order->getOrderID();
            if (strpos($order_id, '-2') !== false) {
              $type_of_order = "Reverse";
            }else{
              $type_of_order = "Forward";
            }

            try{
                
                $in = array(
                    "FromCompany" => $FromCompany,
                    "FromAddress" => $origin_address_line1,
                    "FromLocation" => $origin_address_line2,
                    "FromCountry" =>"United Arab Emirates",
                    "FromCperson" => "John Roy",
                    "FromContactno" => $FromContactno,
                    "FromMobileno" => $FromContactno,
                    "ToCompany" => $order->getCustomerName(),
                    "ToAddress" => $order->getCustomerAddress(),
                    "ToLocation" => $order->getCustomerArea(),
                    "ToCountry" => "United Arab Emirates",
                    "ToCperson" => $order->getCustomerName(),
                    "ToContactno" => $order->getCustomerMobileNumber(),
                    "ToMobileno" => $order->getCustomerMobileNumber(),
                    "ReferenceNumber" => $order->getOrderID(),
                    "CompanyCode" => '', 
                    "Weight" => "0.5",
                    "Pieces" => $order->getTotalItemsQuantity(),
                    "PackageType" => "Document",
                    "CurrencyCode" => "AED",
                    "NcndAmount" => $amount,
                    "ItemDescription" => $order->getGoodsDescription(),
                    "SpecialInstruction" => ''  
                );
                
                $post_data = json_encode($in);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $this->apiUrl."/CustomertoCustomerBooking",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $post_data,
                  CURLOPT_HTTPHEADER => array(
                	"Content-Type: application/json",
                	"API-KEY: ".$this->accountNumber,
                  ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                
                $response_data = json_decode($response, true);
                
                if($response_data['success'] == 1) {
                    $returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $response_data['AwbNumber'],
					    self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage','pdf_print_link' => $response_data['AwbPdf']
					      ];
                }else{
                	throw new \Exception($response_data['message']);	
                }
                
            }
            catch (\Exception $ex){
                throw new \Exception($ex->getMessage());
            }
        }
        return $returnResponse; // Details of order with response
    }
  	public function cleanText($text = ''){
  		if($text){
	  		$text = str_replace("'", "", $text);
	  		$text = str_replace('"', '', $text);
	  		$text = str_replace('"', '', $text);
	  		$text = str_replace('<', '', $text);
	  		$text = str_replace('>', '', $text);
	  		$text = str_replace("\n", ' ', $text);
	  		$text = str_replace("\r", ' ', $text);
	  		$text = str_replace("\r\n", ' ', $text);
	  		return $text;
  		}else{
	  		return $text;
  		}
  	}
	public function getAirwayBillUrl($awbNumber = null)
	{
		return self::HASH_LINK;
	}

	public function getOrderStatus()
	{

	}

	public function printAirwaybill()
	{

	}

	public static function getTrackingUrl($awbNumber = null)
	{
		return self::HASH_LINK;
	}
  public function getOrderTrackingHistory($awb_data,$awb_type=0){
    $awbNumber = $awb_data->airway_bill_number;
    $order_id  = $awb_data->order_id;
    $store_id  = $awb_data->store;
    $in = array("AwbNumber" =>array($awbNumber));
    $post_data = json_encode($in);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->apiUrl."/GetTracking",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $post_data,
      CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json",
      "API-KEY: ".$this->accountNumber,
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    $response_data = json_decode($response, true);
    

    // echo "<pre>"; print_r($response_data); die;
    if($response_data['success'] == 1) {
      $TrackResponse = @$response_data['TrackResponse'][0]['Shipment'];
      $shipment_address = $TrackResponse['ShipmentAddress']['address'].','.$TrackResponse['ShipmentAddress']['city'];
      $returnResponse = ['awb_number' => $TrackResponse['awb_number'],'current_status' => $TrackResponse['current_status'],'status_datetime' => $TrackResponse['status_datetime'],
      'shipment_address' => $shipment_address,'activity'=>$TrackResponse['Activity']];
      //update oms table if order is deliver by courier
      if( $returnResponse['current_status'] != "" && $returnResponse['current_status'] == "Delivered" ){
        if( $awb_type == 0 ){
          AirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1]);
        }elseif( $awb_type == 1 ){
          ExchangeAirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1]);
        }

      }
      return $returnResponse;
    }else{
      throw new \Exception($response_data['message']);	
    }
  }
}