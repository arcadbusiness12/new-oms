<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use DB;
class OrdersController extends Controller
{
	const VIEW_DIR = 'orders';
    function __construct(){
    }
    public function index(){
        // $ba_orders = OrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
        //         $query->whereNotIn('code',['tax']);
        //      }])
        //     ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
        //     // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
        //     ->where('order_type', 'like', 'normal')
        //     ->where('order_status_id', '>', 0)
        //     ->where('online_approved', 1)
        //     ->where('reseller_approve', 1);
        //dressfair orders
        $df_orders = DFOrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
            $query->whereNotIn('code',['tax']);
            }])
            ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 1);
        // $data = $ba_orders->union($df_orders)->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc');
        $data = $df_orders->paginate(20);
        // dd($data->toArray());
        return view(self::VIEW_DIR.".index",compact('data'));
    }
}
