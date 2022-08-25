<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
	const VIEW_DIR = 'orders';
    function __construct(){
    }
    public function index(){
        return view(self::VIEW_DIR.".index");
    }
}
