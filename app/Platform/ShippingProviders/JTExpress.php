<?php

namespace App\Platform\ShippingProviders;

use App\Platform\Golem\OrderGolem;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsActivityLogModel;
use Illuminate\Support\Facades\Request AS RequestFacad;

/**
 * Description of TfmExpress
 *
 * @author kamran
 */
class JTExpress implements ShippingProvidersInterface
{

	const DUMMY_AWB = 'TFM_';
	const API_CREATE_AWB = '/rest/addAwb';
	const API_EDIT_AWB = '/rest/editAwb';
	const API_AWB_NUMBER = 'orderID';
	const API_KEY = 'apiKey';

	protected $apiUrl;
  protected $apiAccount;
	protected $customerCode;
	protected $password;
	protected $privatekey;
  protected $body_digest;

	public function __construct()
	{
    //for testing environment
		// $this->apiUrl     = "https://demoopenapi.jtjms-sa.com/webopenplatformapi/api";
		// $this->customerCode = "J0086024016";
		// $this->apiAccount = "292508153084379141";
		// $this->password = "3B29A9C5728BF3E1DB0C4D66B79748B7";
		// $this->privatekey = "a0a1047cce70493c9d5d29704f05d0d9";
		// $this->body_digest = "mGUfPbWDv9xXncsyOZLdPg==";
    //for production envirnment

    $this->apiUrl     = "https://openapi.jtjms-sa.com/webopenplatformapi/api";
		$this->customerCode = "J0086001319";
		$this->password = "GBu0u0v9";
		$this->apiAccount = "415438963793199107";
		$this->privatekey = "0d959a7184824e62bbe08918516bcb17";
		$this->body_digest = "UfiQ6k5ud2Jv9mvtxW+iQw==";

		if ( null == $this->apiUrl)
		{
			throw new \Exception("ApiKey,Account Number and user Name for JT is required");
		}
	}

	public function forwardOrder($orders) {
		// echo "<pre>"; print_r($orders); die("test");
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
        $FromContactno = "0565651133";
        $FromMobileno  = "0565651133";
      }else{
        $FromCompany   = "BusinessArcade";
        $FromAddress   = "International City";
        $FromLocation  = "Dubai";
        $FromCountry   = "United Arab Emirates";
        $FromCperson   = "";
        $FromContactno = "0565634477";
        $FromMobileno  = "0565634477";
      }
      $sender_info   = [
        "name"=>$FromCompany ,
        "company"=>$FromCompany,
        "mobile"=>$FromMobileno,
        "phone"=>$FromMobileno,
        "countryCode"=>"UAE",
        "prov"=>$FromLocation,
        "city"=>$FromLocation,
        "address"=>$FromAddress
      ];
      $customer_mobile    = str_replace('+', '', $order->getCustomerMobileNumber());
      $customer_mobile    = str_replace('971', '0', $customer_mobile);
      $customer_alternate = str_replace('+', '', $order->getCustomerAlternateNumber());
      $customer_alternate = str_replace('971','0',$customer_alternate);
      $reciever_info = [
        "name"=>trim($order->getCustomerName()),
        "company"=>trim($order->getCustomerName()),
        "mobile"=> $customer_mobile,
        "phone"=> $customer_alternate,
        "countryCode"=>"UAE",
        "prov"=>trim($order->getCustomerCity()),
        "city"=>trim($order->getCustomerCity()),
        "address"=>trim($order->getCustomerArea()).", ".trim($order->getCustomerAddress())
      ];
      //check return order
      $order_id = $order->getOrderID();
      if (strpos($order_id, '-2') !== false) {
        //return airwaybill
        $temp_info = $sender_info;
        $sender_info   = $reciever_info;
        $reciever_info = $temp_info;
        $ItemDescription = $order->getGoodsDescription();
        $SpecialInstruction = "Please Collect above From Customer.";
        $NcndAmount = "0.00";
      }else{
        $ItemDescription = $order->getGoodsDescription();
        $SpecialInstruction = "";
      }
			// try
			// {
				$bookingData  = array(
          // "customerCode" => $this->clientCode,
          // "digest" => "mGUfPbWDv9xXncsyOZLdPg==",
          //"network" => "",
          "txlogisticId" =>$order_id,
          "expressType" => "EZ",
          "orderType" => 1,
          "serviceType" => "01",
					"deliveryType" => "04",
					"payType" => "PP_PM",
					"sender" => $sender_info,
					"receiver" =>$reciever_info,
					"goodsType" => "ITN4",  //customer name
					"weight" => 1,
					"totalQuantity" => $order->getTotalItemsQuantity(),
					"itemsValue" => $NcndAmount,
					"priceCurrency" => 'AED' , /*This will provided by courier*/
					//"remark" => "",  /* Decimal Only */
					"operateType" => 1, /* Number Only */
					"isUnpackEnabled" => 0, /* (Document / Parcel ) - (International Documents / International N/Dox Up 30 KG)*/
					//"items" => $ItemDescription,  /* Optional - ISO 4217 Currency Codes */
					//"NcndAmount" => $NcndAmount, /* Decimal Only */
					//"ItemDescription" => $ItemDescription,
					//"SpecialInstruction" => $SpecialInstruction,
					//"BranchName" => "Dubai"
					);
              $response_json = $this->createOrder($bookingData);

							$response = json_decode($response_json, true);
							// echo $this->apiKey;
							// echo "<pre>"; print_r($response); die("testing...res");

							if ( $response['code'] == 1) {
                $awb_data = $response['data'];
								$pdf_or_print_no = 0;
								// if(isset( $awb_data['billCode'] )){
								// 	$pdf_or_print_no_array = explode("/",$response['AwbPdf']);
								// 	$pdf_or_print_no = end($pdf_or_print_no_array);
								// }
								$returnResponse[$order->getOrderID()] = [ self::AIRWAYBILL_NUMBER => $awb_data['billCode'],
								self::MESSAGE_FROM_PROVIDER => 'Order Moved to Next stage',
								'pdf_or_print_no' => $pdf_or_print_no,
                'sortingCode' => $awb_data['sortingCode']
							  ];
							} else {
							  throw new \Exception($response_json);
						  }
					// } catch (\Exception $ex) {
					// 	continue;
					// }
				}
        // die("test");
				// echo $this->apiUrl;
				// dd($returnResponse);
				return $returnResponse;
	}
  private function createOrder($allParameter)
    {
        // $allParameter = $request->toArray();
        // echo "<pre>"; print_r($allParameter);
        // $privatekey = "a0a1047cce70493c9d5d29704f05d0d9";

        // initial exchange request
        $curl = curl_init($this->apiUrl."/order/addOrder");

        //$customerCode = "J0086024016";
        //$password = "3B29A9C5728BF3E1DB0C4D66B79748B7";

        // MD5 first and then uppercase
        $pwd = strtoupper(md5($this->password.'jadada236t2'));

        // md5 business data
        $digstr = md5($this->customerCode.$pwd.$this->privatekey);

        // business data signature
        $body_digest = base64_encode(pack('H*',$digstr));
        // echo $body_digest; die("digeest test");
        $allParameter['customerCode'] = $this->customerCode;
        $allParameter['digest']= $body_digest;
        // $allParameter['digest']= $this->body_digest;

        $biz = json_encode($allParameter, JSON_UNESCAPED_UNICODE);

        $post_content = array(
            'bizContent' => $biz
        );
        // echo "<pre>"; print_r($post_content);
        // Headers signature
        $header_digest = base64_encode(pack('H*',strtoupper(md5($biz.$this->privatekey))));

        $header = array(
            // "Content-type: application/x-www-form-urlencoded",
            "apiAccount:$this->apiAccount",
            "digest:".$header_digest,
            "timestamp:".time()
        );
        // echo "<pre>"; print_r($header);
        //SSL certificate verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        //whether to return data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //set to POST method
        curl_setopt($curl, CURLOPT_POST, true);
        //set POST request parameters
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
        //set http header
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        // execute request
       $result = curl_exec($curl);

        //close request
       curl_close($curl);

        return $result;
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
			return route('jtexpress.invoice', $awbNumber);
    }

    public function getOrderStatus()
    {

    }

    public function printAirwaybill()
    {
      $awb_number = RequestFacad::get('awb');
      $allParameter = array(
        "billCode" => $awb_number,
        "needLogo" => 0
      );
      $curl = curl_init($this->apiUrl."/order/printOrderUrl");

      // MD5 first and then uppercase
      $pwd = strtoupper(md5($this->password.'jadada236t2'));

      // md5 business data
      $digstr = md5($this->customerCode.$pwd.$this->privatekey);

      // business data signature
      $body_digest = base64_encode(pack('H*',$digstr));
      // echo $body_digest; die("digeest test");
      $allParameter['customerCode'] = $this->customerCode;
      $allParameter['digest']= $body_digest;
      // $allParameter['digest']= $this->body_digest;

      $biz = json_encode($allParameter, JSON_UNESCAPED_UNICODE);

      $post_content = array(
          'bizContent' => $biz
      );
      // echo "<pre>"; print_r($post_content);
      // Headers signature
      $header_digest = base64_encode(pack('H*',strtoupper(md5($biz.$this->privatekey))));
      // echo "<pre>"; print_r($post_content);
      $header = array(
          // "Content-type: application/x-www-form-urlencoded",
          "apiAccount:$this->apiAccount",
          "digest:".$header_digest,
          "timestamp:".time()
      );
      // echo "<pre>"; print_r($header);
      //SSL certificate verification
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      //whether to return data
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      //set to POST method
      curl_setopt($curl, CURLOPT_POST, true);
      //set POST request parameters
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
      //set http header
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

      // execute request
      $result = curl_exec($curl);

        //close request
      curl_close($curl);
      // header('Content-Type: application/pdf');
      $data = json_decode($result);
      // echo "<pre>"; print_r($data );
      // echo $data->data;
      // echo "<script>window.open('$data->data').focus();</script>";
      echo "<script>location.replace('$data->data');</script>";
      // echo $result;
    }

    public static function getTrackingUrl($awbNumber = null)
    {
    	return self::HASH_LINK;
    }
    public function getOrderTrackingHistory($awb_data,$awb_type=0){

      // echo "<pre>"; print_r($awb_data); die("testing..");s
      // echo $awb_data->airway_bill_number;
      $awbNumber = $awb_data->airway_bill_number;
      $order_id  = $awb_data->order_id;
      $store_id  = $awb_data->store;
      $allParameter = array(
        "billCodes" => $awbNumber
      );
      $curl = curl_init($this->apiUrl."/logistics/trace");

      // MD5 first and then uppercase
      $pwd = strtoupper(md5($this->password.'jadada236t2'));

      // md5 business data
      $digstr = md5($this->customerCode.$pwd);

      // business data signature
      $body_digest = base64_encode(pack('H*',$digstr));
      // echo $body_digest; die("digeest test");
      $allParameter['customerCode'] = $this->customerCode;
      // $allParameter['digest']= $body_digest;
      $allParameter['digest']= $this->body_digest;

      $biz = json_encode($allParameter, JSON_UNESCAPED_UNICODE);

      $post_content = array(
          'bizContent' => $biz
      );
      // echo "<pre>"; print_r($post_content);
      // Headers signature
      $header_digest = base64_encode(pack('H*',strtoupper(md5($biz.$this->privatekey))));

      $header = array(
          // "Content-type: application/x-www-form-urlencoded",
          "apiAccount:$this->apiAccount",
          "digest:".$header_digest,
          "timestamp:".time()
      );
      // echo "<pre>"; print_r($header);
      //SSL certificate verification
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      //whether to return data
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      //set to POST method
      curl_setopt($curl, CURLOPT_POST, true);
      //set POST request parameters
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
      //set http header
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

      // execute request
      $result = curl_exec($curl);

        //close request
      curl_close($curl);
      // echo "<pre>"; print_r($response_data); die;
      $response_data = json_decode($result,true);
      // echo "<pre>"; print_r($response_data); die;
      if($response_data['code'] == 1) {
        $TrackResponse = $response_data['data'][0]['details'];
        $current_status = "";
        foreach($TrackResponse as $key => $val){
          $activity[] = array(
            'datetime'=>$val['scanTime'],
            'status'=>$val['scanType'],
            'details'=>$val['desc'],
          );
          $current_status = $val['scanType'];
          //update oms table if order is deliver by courier
          if( $current_status != "" && $current_status == "Sign scan" ){
            if( $awb_type == 0 ){
              AirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $result]);
              if($store_id == 1) {
                $ba_class = app(\App\Http\Controllers\Orders\OrdersController::class)->deliverSingleOrder([$order_id]);
              }else if($store_id == 2){
                $ba_class = app(\App\Http\Controllers\DressFairOrders\DressFairOrdersController::class)->deliverSingleOrder([$order_id]);
              }
            }elseif( $awb_type == 1 ){
              ExchangeAirwayBillTrackingModel::where('airway_bill_number',$awbNumber)->where('order_id',$order_id)->where('store',$store_id)->update(['courier_delivered'=>1, 'courier_response' => $result]);
              if($store_id == 1) {
                $ba_class = app(\App\Http\Controllers\Exchange\ExchangeOrdersController::class)->deliverSingleOrder([$order_id]);
              }else if($store_id == 2){
                $ba_class = app(\App\Http\Controllers\DressFairExchange\DressFairExchangeOrdersController::class)->deliverSingleOrder([$order_id]);
              }
            }
          }else if( $current_status != "" && $current_status == "Returned parcel scan" ){
            $record = OmsOrdersModel::where("order_id",$order_id)->where("oms_order_status",3)->where("store",$store_id)->whereNull("ready_for_return")->update(['ready_for_return'=>1]);
            if( $record ){
              $activity1 = new OmsActivityLogModel();
              $activity1->activity_id = 26; //Ready For Return
              $activity1->ref_id  = $order_id;
              $activity1->store   = $store_id;
              // $activity->comment = $comment;
              $activity1->created_by_courier = 18;
              $activity1->save();
             }
          }
        }
        $returnResponse = ['awb_number' => $awbNumber,'current_status' => "",'status_datetime' => '',
        'shipment_address' => '','activity'=>@$activity];
        return $returnResponse;
      }else{
        //throw new \Exception($response_data['message']);
      }
    }
  }
