<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Http\Request;
use App\Platform\Helpers\ToolImage;
use Illuminate\Support\Facades\Input;
use Session;
use Validator;
use Illuminate\Support\Facades\Storage;
use URL;

class OrdersAjaxController extends Controller {

	const VIEW_DIR = 'orders';
	function __construct(){
	}
    public function index(){

    }
}
