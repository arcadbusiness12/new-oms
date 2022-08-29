<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\DressFairOpenCart\Orders\OrderStatusModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

class OrdersController extends Controller
{
	const VIEW_DIR = 'orders';
    function __construct(){
    }
    public function index(){
        $old_input = Request::all();
        // dd($old_input);
        $ba_orders = OrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
                $query->whereNotIn('code',['tax']);
             }])
            ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,
            shipping_city,shipping_zone,date_added,date_modified,
            1 AS store"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 1)
            ->where('reseller_approve', 1)
            ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                return $query->where('order_id',$old_input['order_id']);
            })
            ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                return $query->where('order_status_id',$old_input['order_status_id']);
            })
            ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','>=',$old_input['date_from']);
            })
            ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','<=',$old_input['date_to']);
            });
        //dressfair orders
        $df_orders = DFOrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
            $query->whereNotIn('code',['tax']);
            }])
            ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,
            shipping_city,shipping_zone,date_added,date_modified,
            2 AS store"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 1)
            ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                return $query->where('order_id',$old_input['order_id']);
            })
            ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                return $query->where('order_status_id',$old_input['order_status_id']);
            })
            ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','>=',$old_input['date_from']);
            })
            ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','<=',$old_input['date_to']);
            });
        if( @$old_input['by_store'] == 1 ){
            $data = $ba_orders->paginate(20);
        }else if( @$old_input['by_store'] == 2 ){
            $data = $df_orders->paginate(20);
        }else{
            $data = $ba_orders->union($df_orders)->where("order_id",1)->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc');
            $data = $data->paginate(20);
        }
        // dd($data->toArray());
        $searchFormAction = URL::to('orders');
        $orderStatus = OrderStatusModel::all();
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input'));
    }
    public function online(){
        $old_input = Request::all();

        $ba_orders = OrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
                $query->whereNotIn('code',['tax']);
             }])
            ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 0)
            ->where('reseller_approve', 1)
            ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                return $query->where('order_id',$old_input['order_id']);
            })
            ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                return $query->where('order_status_id',$old_input['order_status_id']);
            })
            ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','>=',$old_input['date_from']);
            })
            ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','<=',$old_input['date_to']);
            });

        //dressfair orders
        $df_orders = DFOrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
            $query->whereNotIn('code',['tax']);
            }])
            ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 0)
            ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                return $query->where('order_id',$old_input['order_id']);
            })
            ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                return $query->where('order_status_id',$old_input['order_status_id']);
            })
            ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','>=',$old_input['date_from']);
            })
            ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
                return $query->whereDate('date_added','<=',$old_input['date_to']);
            });
        // fileter by store
        if( @$old_input['by_store'] == 1 ){
            $data = $ba_orders->paginate(20);
        }else if( @$old_input['by_store'] == 2 ){
            $data = $df_orders->paginate(20);
        }else{
            // die("elase");
            $data = $ba_orders->union($df_orders)
            ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc');
            $data = $data->paginate(20);
        }
        ///extra variable
        $searchFormAction = URL::to('orders/online');
        $orderStatus = OrderStatusModel::all();
        return view(self::VIEW_DIR.".online",compact('data','searchFormAction','orderStatus','old_input','old_input'));
    }
}
