<?php

namespace App\Http\Controllers\ShippingProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Session;
use Excel;

/**
* Description of PurchaseController
*
* @author kamran
*/
class JTCourier extends Controller
{
	public function __construct() {
	}
	public function index(){
		return redirect('/');
	}
	public function invoice(Request $request){
    $shippingCompanyClass = "\\App\\Platform\\ShippingProviders\\JTExpress";
    if (!class_exists($shippingCompanyClass)) {
      throw new \Exception("Shipping Provider Class {$shippingCompanyClass} does not exist");
    }
    

    $shipping = new $shippingCompanyClass();
    $shipping->printAirwaybill();
	}

}