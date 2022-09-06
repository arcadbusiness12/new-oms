<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsActivityLogModel;
/**
 * Description of TfmExpress
 *
 * @author kamran
 */
class TfmExpress implements ShippingProvidersInterface
{

	const DUMMY_AWB = 'TFM_';
	const API_CREATE_AWB = '/rest/addAwb';
	const API_EDIT_AWB = '/rest/editAwb';
	const API_AWB_NUMBER = 'orderID';
	const API_KEY = 'apiKey';

	protected $accountNumber;
	protected $apiUrl;
	protected $userName;

	public function __construct()
	{
		$this->accountNumber = config('services.tfmExpress')['accountNumber'];
		$this->apiUrl = config('services.tfmExpress')['url'];
		$this->userName = config('services.tfmExpress')['userName'];
		$this->password = config('services.tfmExpress')['password'];
		$this->httpClient = \App::make('httpClient'); // Http Client

		if (null == $this->accountNumber || null == $this->apiUrl || $this->userName == null)
		{
			throw new \Exception("ApiKey,Account Number and user Name for Tfm Express is required");
		}
	}

	public function forwardOrder($orders)
	{
		$returnResponse = [];
		try
		{
			$bookingData = ["json" => [
				"company" => "",
				"cid" => $this->accountNumber,
				"accountno"=> $this->accountNumber,
				"country" => 'UAE',
				"city" => 'Sharjah',
				"address" => '',
				"phone" => '',
				"fax" => '',
				"pickup_loc" => '',
				"consignee_loc" => '',
			]];

			$response = $this->httpClient->post($this->apiUrl . "/rest/savenewbooking", $bookingData);
			$booking = json_decode($response->getBody()->getContents(), true);
			$bookingNumber = $booking["message"];

			$datas = "\n\nsavenewbooking\n" . json_encode($booking);
			file_put_contents(public_path('assets/TFM_request_response.php'), $datas, FILE_APPEND);
		}
		catch (\Exception $ex)
		{
			throw new \Exception($ex->getMessage());
		}
		if($booking['status'] == 1){
	    	// Expected array of OrderGOlem objects
			foreach ($orders as $order)
			{
				if (!$order instanceof OrderGolem)
				{
					throw new \Exception("Order needs to be an instance of orderGolem");
				}

				$is_exchange_return_order = 0;
				if (strpos($order->getOrderID(), '-2') !== false) {
					$is_exchange_return_order = 1;
				}

				if(trim($order->getPaymentMethod()) == 'cod' || trim($order->getPaymentMethod()) == 'cod_order_fee' || trim($order->getPaymentMethod()) == '') {
					$amount = $order->getOrderTotalAmount();
					$service_method = "COD";
				}else if( trim($order->getPaymentMethod()) == 'ccavenuepay' ) {
					$amount = '0.00';
					$service_method = "Courier";
				}else{
          $amount = $order->getOrderTotalAmount();
					$service_method = "COD";
        }
				// Create Awb First
				$data = ['json' => [
					"accountno" => (string) $this->accountNumber,
					"destination" => 'DXB',
					"originStationId" => "1",
					"pieces" => $order->getTotalItemsQuantity(),
					"refno" => $bookingNumber,
					"service" => $service_method,
					"username" => $this->userName,
					"weight" => 1,
				]];
				try
				{
					$response = $this->httpClient->post($this->apiUrl . "/" . self::API_CREATE_AWB, $data);
				}
				catch (\Exception $ex)
				{
					throw new \Exception($ex->getMessage());
				}

				$awbNumber = json_decode($response->getBody()->getContents(), true);
				$status = $awbNumber['status'];
				$prefix = substr($awbNumber['message'], 0, 4);
				$actualAwbNumber = substr($awbNumber['message'], 4, strlen($awbNumber['message']));

				$datas = "\n\nawbenterdata\n" . json_encode($awbNumber);
				file_put_contents(public_path('assets/TFM_request_response.php'), $datas, FILE_APPEND);

				if ($status == 1 && !empty($prefix) && !empty($actualAwbNumber))
				{
				// edit airway bill in second call
					if($is_exchange_return_order){
						$awb = ['json' => [
							"ACCOUNTNO" => $this->accountNumber,
							"BILLPREFIX" => $prefix,
							"BILLNUMBER" => $actualAwbNumber,
							"CONSIGNORREF" => $order->getOrderID(),
							"CONSIGNEE" => $this->cleanText($order->getCompanyName()),
							"CONSIGNEEMOBILE" => $order->getCompanyMobileNumber(),
							"CONSIGNEEADDRESS" => $this->cleanText($order->getCompanyAddress()),
							"CONSIGNEETOWN" => $this->cleanText($order->getCompanyCity()),
							"CONSIGNEEZIPCODE" => 'N/A',
							"CONSIGNEECOUNTRY" => 'UAE',
							"CONSIGNEETELEPHONE" => $order->getCompanyMobileNumber(),
							"CONSIGNEEFAX" => 'N/A',
							"CONSIGNEEEMAILADDRESS" => "N/A",
							"CONSIGNEEATTENTION" => "N/A",
							"DESTINATIONCODE" => "DXB",
							"PIECES" => $order->getTotalItemsQuantity(),
							"TOTALWEIGHT" => "1",
							"TOTALVOLWEIGHT" => "1",
							"CURRENCY" => "AED",
							"VALUEAMT" => 0,
							"CODAMT" => 0,
							"TYPEOFSHIPMENT" => "Non Docs & Docs",
							"CONTENTS" => $this->cleanText($order->getGoodsDescription()),
							"CONSIGNORNOTE" => "Exchange Return Shipment",
						]];
					}else{
						$awb = ['json' => [
							"ACCOUNTNO" => $this->accountNumber,
							"BILLPREFIX" => $prefix,
							"BILLNUMBER" => $actualAwbNumber,
							"CONSIGNORREF" => $order->getOrderID(),
							"CONSIGNEE" => $this->cleanText($order->getCustomerName()),
							"CONSIGNEEMOBILE" => $order->getCustomerMobileNumber(),
							"CONSIGNEEADDRESS" => $this->cleanText($order->getCustomerAddress()),
							"CONSIGNEETOWN" => $this->cleanText($order->getCustomerCity()),
							"CONSIGNEEZIPCODE" => 'N/A',
							"CONSIGNEECOUNTRY" => 'UAE',
							"CONSIGNEETELEPHONE" => $order->getCustomerAlternateNumber(),
							"CONSIGNEEFAX" => 'N/A',
							"CONSIGNEEEMAILADDRESS" => "N/A",
							"CONSIGNEEATTENTION" => "N/A",
							"DESTINATIONCODE" => "DXB",
							"PIECES" => $order->getTotalItemsQuantity(),
							"TOTALWEIGHT" => "1",
							"TOTALVOLWEIGHT" => "1",
							"CURRENCY" => "AED",
						  "VALUEAMT" => $amount, //$order->getOrderTotalAmount(),
						  "CODAMT" => $amount, //$order->getOrderTotalAmount(),
						  "TYPEOFSHIPMENT" => "Non Docs & Docs",
						  "CONTENTS" => $this->cleanText($order->getGoodsDescription()),
						  "CONSIGNORNOTE" => "",
						  ]];
						}
						try
						{
							$response = $this->httpClient->post($this->apiUrl . "/" . self::API_EDIT_AWB, $awb);
						}
						catch (\Exception $ex)
						{
							throw new \Exception($ex->getMessage());
						}

						$response = json_decode($response->getBody()->getContents(), true);

						$datas = "\n\nDate - ". date('Y-m-d h:i:s') . "\n\n";
						$datas .= "Request\n";
						$datas .= json_encode($awb);
						$datas .= "\n\nResponse\n";
						$datas .= json_encode($response);
						file_put_contents(public_path('assets/TFM_request_response.php'), $datas, FILE_APPEND);

						if($response['status'] == 1){
							$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $prefix . $actualAwbNumber,
								self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage'
							];
						}else{
							throw new \Exception($response['message']);
						}

		      	} // IF prefix and awb number is set
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
      try
				{
					$response = $this->httpClient->get($this->apiUrl . "/rest/trackingbyawb/".$awbNumber."/$this->accountNumber",[]);
				}
				catch (\Exception $ex)
				{
					throw new \Exception($ex->getMessage());
				}

      $response_data = json_decode($response->getBody()->getContents(), true);
      if( is_array($response_data) && count($response_data) > 0 ){
        $response_data = $response_data['trackingbyawbResult'];
      }
      
  
      // echo "<pre>"; print_r($response_data); die("test");
      if($response_data['status'] == 1) {
        $TrackResponse = $response_data['response'];
        $shipment_address = '';
        $activity =[];
        foreach($TrackResponse as $key => $val){
          $activity[] = array(
            'datetime'=>$val['colDate'],
            'status'=>$val['Status'],
            'details'=>$val['colStatus'],
          );
          if( $val['Status'] == "POD" || $val['Status'] == 'EXCPOD01'){
            if( $awb_type == 0 ){
              AirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response->getBody()->getContents()]);
              if($store_id == 1) {
                $ba_class = app(\App\Http\Controllers\Orders\OrdersController::class)->deliverSingleOrder([$order_id]);
              }else if($store_id == 2){
                $ba_class = app(\App\Http\Controllers\DressFairOrders\DressFairOrdersController::class)->deliverSingleOrder([$order_id]);
              }
            }elseif( $awb_type == 1 ){
              ExchangeAirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response->getBody()->getContents()]);
              if($store_id == 1) {
                $ba_class = app(\App\Http\Controllers\Exchange\ExchangeOrdersController::class)->deliverSingleOrder([$order_id]);
              }else if($store_id == 2){
                $ba_class = app(\App\Http\Controllers\DressFairExchange\DressFairExchangeOrdersController::class)->deliverSingleOrder([$order_id]);
              }
            }
          }else if( $val['Status'] == 'EXC21R' ){

            $record = OmsOrdersModel::where("order_id",$order_id)->where("oms_order_status",3)->where("store",$store_id)->whereNull("ready_for_return")->update(['ready_for_return'=>1]);
            if( $record ){
              $activity = new OmsActivityLogModel();
              $activity->activity_id = 26; //Ready For Return
              $activity->ref_id  = $order_id;
              $activity->store   = $store_id;
              // $activity->comment = $comment;
              $activity->created_by_courier = 3;
              $activity->save();
             }
          }
        }
        $returnResponse = ['awb_number' => '','current_status' => '','status_datetime' => '',
        'shipment_address' => $shipment_address,'activity'=>$activity];
        return $returnResponse;
      }else{
        // throw new \Exception($response_data['message']);	
      }
    }
  }