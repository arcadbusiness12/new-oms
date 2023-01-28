<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsActivityLogModel;
use Illuminate\Support\Facades\Http;
/**
 * Description of TfmExpress
 *
 * @author kamran
 */
class DeliveryPanda implements ShippingProvidersInterface
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
		// $this->accountNumber = config('services.deliveryPanda')['accountNumber'];
		$this->apiUrl = config('services.deliveryPanda')['url'];
		// $this->userName = config('services.deliveryPanda')['userName'];
		// $this->password = config('services.deliveryPanda')['password'];
		$this->apiKey = config('services.deliveryPanda')['apiKey'];
		//$this->httpClient = \App::make('httpClient'); // Http Client

		if ( null == $this->apiUrl)
		{
			throw new \Exception("ApiKey,Account Number and user Name for Tfm Express is required");
		}
	}

	public function forwardOrder($orders) {
		// die($orders);
		// dd($orders);
		$returnResponse = [];
		// dd($orders);


						// "commodity_id":"1",
		foreach ($orders as $key => $order) {

      if( trim($order->getPaymentMethod()) == 'ccavenuepay' ){
				// $payment_method = "Cash";
				$NcndAmount = "0.00";
			}else{
				// $payment_method = "0";
				$NcndAmount = $order->getOrderTotalAmount();
			}

      if($order->getStore()==2){
        $FromCompany   = "DressFair";
        $FromAddress   = "IndustrialArea 11";
        $FromLocation  = "Sharjah";
        $FromCountry   = "United Arab Emirates";
        $FromCperson   = "";
        $FromContactno = "971565651133";
        $FromMobileno  = "971565651133";
      }else{
        $FromCompany   = "BusinessArcade";
        $FromAddress   = "International City";
        $FromLocation  = "Dubai";
        $FromCountry   = "United Arab Emirates";
        $FromCperson   = "";
        $FromContactno = "971565634477";
        $FromMobileno  = "971565634477";
      }
      //check return order

      $order_id = $order->getOrderID();
      if (strpos($order_id, '-2') !== false) {
        //return airwaybill
        $ItemDescription = $order->getGoodsDescription();
        $SpecialInstruction = "Please Collect above From Customer.";
        $NcndAmount = "0.00";
      }else{
        $ItemDescription = $order->getGoodsDescription();
        $SpecialInstruction = "";
      }
			try
			{
				$bookingData  = array(
          "FromCompany" => $FromCompany,
          "FromAddress" => $FromAddress,
          "FromLocation" => $FromLocation,
          "FromCountry" =>$FromCountry,
          "FromCperson" => $FromCperson,
          "FromContactno" => $FromContactno,
          "FromMobileno" => $FromMobileno,
					"ToCompany" => "",
					"ToAddress" => trim($order->getCustomerArea()).", ".trim($order->getCustomerAddress()),
					"ToLocation" => trim($order->getCustomerCity()),  //city
					"ToCountry" =>"United Arab Emirates",
					"ToCperson" => trim($order->getCustomerName()),  //customer name
					"ToContactno" => str_replace('+', '', $order->getCustomerMobileNumber()),
					"ToMobileno" => str_replace('+', '', $order->getCustomerAlternateNumber()),
					"ReferenceNumber" => $order->getOrderID(),
					"CompanyCode" => '' , /*This will provided by courier*/
					"Weight" => "0.0",  /* Decimal Only */
					"Pieces" => $order->getTotalItemsQuantity(), /* Number Only */
					"PackageType" => "Parcel", /* (Document / Parcel ) - (International Documents / International N/Dox Up 30 KG)*/
					"CurrencyCode" => "AED",  /* Optional - ISO 4217 Currency Codes */
					"NcndAmount" => $NcndAmount, /* Decimal Only */
					"ItemDescription" => $ItemDescription,
					"SpecialInstruction" => $SpecialInstruction,
					"BranchName" => "Dubai"
					);
					$bookingData = json_encode($bookingData);
							//$dest_url = $this->apiUrl."CustomerBooking";
							$dest_url = $this->apiUrl."CustomertoCustomerBooking";
							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_URL => $dest_url,
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => "",
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 30,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => "POST",
								CURLOPT_POSTFIELDS => $bookingData,
								CURLOPT_HTTPHEADER => array(
									"Content-Type: application/json",
									"api-key: " . $this->apiKey,
								),
							));
							// curl_exec($curl);
							$response = json_decode(curl_exec($curl), true);
							// echo $this->apiKey;
							// echo "<pre>"; print_r($response); die("testing...");
							$err = curl_error($curl);
							curl_close($curl);

							if ($err) {
								throw new \Exception($err);
							} else {
								$pdf_or_print_no = 0;
								if(isset( $response['AwbPdf'] )){
									$pdf_or_print_no_array = explode("/",$response['AwbPdf']);
									$pdf_or_print_no = end($pdf_or_print_no_array);
								}
								$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $response['AwbNumber'],
								self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage',
								'pdf_or_print_no' => $pdf_or_print_no
							];
						}
					} catch (\Exception $ex) {
						continue;
					}
				}
				// echo $this->apiUrl;
				// dd($returnResponse);
				return $returnResponse;
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
			// return self::HASH_LINK;
			return route('deliverypanda.invoice', $awbNumber);
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
      // echo "<br>".$awb_type."<br>";
      // print_r($awb_data); return;
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
        "API-KEY: ".$this->apiKey,
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
        if( $returnResponse['current_status'] != "" && ($returnResponse['current_status'] == "DELIVERED" || $returnResponse['current_status'] == "Delivered") ){
          if( $awb_type == 0 ){
            AirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response]);
            $ba_class = app(\App\Http\Controllers\Orders\OrdersController::class)->deliverSingleOrder($order_id,$store_id);
          }elseif( $awb_type == 1 ){
            ExchangeAirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $response]);
            $ba_class = app(\App\Http\Controllers\Exchange\ExchangeOrdersController::class)->deliverSingleOrder($order_id,$store_id);
          }
        }else if( $returnResponse['current_status'] != "" &&  ( trim($returnResponse['current_status']) == "OUT FOR RTO" || trim($returnResponse['current_status']) == "READY FOR RTO" || trim($returnResponse['current_status']) == "RETURN IN PROGRESS" || trim($returnResponse['current_status']) == "RTO" ) ){
          $record = OmsOrdersModel::where("order_id",$order_id)->where("store",$store_id)->where("oms_order_status",3)->whereNull("ready_for_return")->update(['ready_for_return'=>1]);
          if( $record ){
            $activity = new OmsActivityLogModel();
            $activity->activity_id = 26; //Ready For Return
            $activity->ref_id  = $order_id;
            $activity->store   = $store_id;
            // $activity->comment = $comment;
            $activity->created_by_courier = 13;
            $activity->save();
          }
        }
        return $returnResponse;
      }else{
        //throw new \Exception($response_data['message']);
      }
    }
  }
