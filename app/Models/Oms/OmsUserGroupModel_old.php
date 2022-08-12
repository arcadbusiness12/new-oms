<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;

class OmsUserGroupModel extends Model
{
    protected $table = 'oms_user_group';
    protected $primaryKey = "id";

    const FIELD_ID = 'id';
    const FIELD_NAME = 'name';
    const FIELD_ACCESS = 'access';
    const FIELD_UPDATED_AT = 'updated_at';

    public static function place_order_routes(){
        return array(
            'Place_Order'           =>  'place_order',
            'Reports'               =>  'place_order/reports',
        );
    }
    public static function normal_order_routes(){
        
        return array(
            'All_Orders'            =>  'orders',
            'Campaign_Orders'       =>  'orders/campaign',
            'Pick_List'             =>  'orders/picking-list-awaiting',
            'Generate_&_Print_AWB'  =>  'orders/generate-awb',
            'Pack_Orders'           =>  'orders/pack_order',
            'Ship_Orders'           =>  'orders/ship-orders',
            'Return_Orders'         =>  'orders/return_order',
            'Deliver_Orders'        =>  'orders/deliver-orders',
            'Airway_Bill_History'   =>  'orders/awb-generated',
        );
    }
    public static function exchange_order_routes(){
        return array(
            'All_Orders'            =>  'exchange_orders',
            'Approve_Exchange'      =>  'exchange_orders/pending',
            'Pick_List'  			=>  'exchange_orders/picking-list-awaiting',
            'Generate_&_Print_AWB'  =>  'exchange_orders/generate-awb',
            'Pack_Orders'         	=>  'exchange_orders/pack_order',
            'Ship_Orders'           =>  'exchange_orders/ship-orders',
            'Deliver_Orders'        =>  'exchange_orders/deliver-orders',
            'Return_Orders'   	    =>  'exchange_orders/return_order',
            // 'Un-Deliver_Orders'     =>  'exchange_orders/undeliver-orders',
            'Airway_Bill_History'   =>  'exchange_orders/awb-generated',
        );
    }
    public static function return_order_routes(){
        return array(
            'All_Orders'            =>  'exchange_returns',
            'Returns_Deliver_Orders'=>  'exchange_returns/return_order',
            'Airway_Bill_History'	=>  'exchange_returns/awb-generated',
        );
    }
    public static function df_place_order_routes(){
        return array(
            'Place_Order'           =>  'df/place_order',
            'Reports'               =>  'df/place_order/reports',
        );
    }
    public static function df_normal_order_routes(){
        return array(
            'All_Orders'            =>  'df/orders',
            'Pick_List'             =>  'df/orders/picking-list-awaiting',
            'Generate_&_Print_AWB'  =>  'df/orders/generate-awb',
            'Pack_Orders'           =>  'df/orders/pack_order',
            'Ship_Orders'           =>  'df/orders/ship-orders',
            'Return_Orders'         =>  'df/orders/return_order',
            'Deliver_Orders'        =>  'df/orders/deliver-orders',
            'Airway_Bill_History'   =>  'df/orders/awb-generated',
        );
    }
    public static function df_exchange_order_routes(){
        return array(
            'All_Orders'            =>  'df/exchange_orders',
            'Approve_Exchange'      =>  'df/exchange_orders/pending',
            'Pick_List'             =>  'df/exchange_orders/picking-list-awaiting',
            'Generate_&_Print_AWB'  =>  'df/exchange_orders/generate-awb',
            'Pack_Orders'           =>  'df/exchange_orders/pack_order',
            'Ship_Orders'           =>  'df/exchange_orders/ship-orders',
            'Deliver_Orders'        =>  'df/exchange_orders/deliver-orders',
            'Return_Orders'         =>  'df/exchange_orders/return_order',
            // 'Un-Deliver_Orders'     =>  'df/exchange_orders/undeliver-orders',
            'Airway_Bill_History'   =>  'df/exchange_orders/awb-generated',
        );
    }
    public static function df_return_order_routes(){
        return array(
            'All_Orders'            =>  'df/exchange_returns',
            'Returns_Deliver_Orders'=>  'df/exchange_returns/return_order',
            'Airway_Bill_History'   =>  'df/exchange_returns/awb-generated',
        );
    }
    public static function purchase_management_routes(){
        return array(
            'Add_Product (Admin)'               =>  'purchase_manage/add_product',
            'All_Orders (Admin)'                =>  'purchase_manage/purchase_orders',
            'All_Orders (Supplier)'             =>  'purchase_manage/supplier_orders',
            'New_Order (Both)'                  =>  'purchase_manage/awaiting_action',
            'Awaiting_Approval (Admin)'         =>  'purchase_manage/awaiting_approval',
            'Confirm (Both)'                    =>  'purchase_manage/confirmed',
            'To_Be_Shipped (Both)'              =>  'purchase_manage/to_be_shipped',
            'Shipped (Both)'                    =>  'purchase_manage/shipped',
            'Deliver (Admin)'                   =>  'purchase_manage/deliver',
            'Delivered (Both)'                  =>  'purchase_manage/delivered',
            'Cancelled (Admin)'                 =>  'purchase_manage/cancelled',
            'Stock_Cancelled (Admin)'           =>  'purchase_manage/stock_cancel',
            'Add_Complaint (Admin)'             =>  'purchase_manage/add_complaint',
            'Accounts (Admin)'                  =>  'purchase_manage/accounts',
            'Complaint (Supplier)'              =>  'purchase_manage/supplier_complaint_order',
            'Accounts (Supplier)'               =>  'purchase_manage/supplier_accounts',
            'Account_Summary_Report (Admin)'    =>  'purchase_manage/account_summary_report',
            'Withdraw_Request (Admin)'          => 	'withdraw_request',
            'Withdraw_Money (Supplier)'         =>  'withdraw_money',
            'Account_Summary (Supplier)'        => 	'account_summary',
        );
    }
    public static function inventory_management_routes(){
        return array(
            'Add Inventory'         =>  'inventory_manage/add_inventory',
            'Add Options'           =>  'inventory_manage/add_options',
            'Dashboard'             =>  'inventory_manage/dashboard',
            'Stock_Level'       	=>  'inventory_manage/stock_level',
            'Add_Stock'         	=>  'inventory_manage/add_stock',
            // 'Pack_Order'        	=>  'inventory_manage/pack_order',
            // 'Return_Order'      	=>  'inventory_manage/return_order',
            'Inventory_Alarm'   	=>  'inventory_manage/inventory_alarm',
            'Reports'   			=>  'inventory_manage/reports',
            'Inventory_Options'     =>  'inventory_manage/inventory_options',
        );
    }
    public static function oms_setting_routes(){
        return array(
            'Supplier'              =>  'supplier',
            'Staff'                 =>  'staff',
            'User_Groups'           =>  'supplier_groups',
            'Shipping_Provider'     =>  'shipping_providers',
        );
    }
    public static function all_routes_for_blade(){
        return array_merge(
            array_values(self::place_order_routes()),
            array_values(self::normal_order_routes()),
            array_values(self::exchange_order_routes()),
            array_values(self::return_order_routes()),
            array_values(self::df_place_order_routes()),
            array_values(self::df_normal_order_routes()),
            array_values(self::df_exchange_order_routes()),
            array_values(self::df_return_order_routes()),
            array_values(self::purchase_management_routes()),
            array_values(self::inventory_management_routes()),
            array_values(self::oms_setting_routes())
        );
    }

    public static function place_order_routes_for_blade(){
        $place_order_routes = self::place_order_routes();
        return array_map(function($value){ return 0; }, array_flip($place_order_routes));
    }
    public static function orders_routes_for_blade(){
    	$normal_order_routes = self::normal_order_routes();
    	$exchange_order_routes = self::exchange_order_routes();
    	$return_order_routes = self::return_order_routes();
        return array_map(function($value){ return 0; }, array_merge(array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes)));
    }
    public static function normal_order_routes_for_blade(){
    	$normal_order_routes = self::normal_order_routes();
        return array_map(function($value){ return 0; }, array_flip($normal_order_routes));
    }
    public static function exchange_order_routes_for_blade(){
    	$exchange_order_routes = self::exchange_order_routes();
        return array_map(function($value){ return 0; }, array_flip($exchange_order_routes));
    }
    public static function return_order_routes_for_blade(){
    	$return_order_routes = self::return_order_routes();
        return array_map(function($value){ return 0; }, array_flip($return_order_routes));
    }

    public static function df_place_order_routes_for_blade(){
        $df_place_order_routes = self::df_place_order_routes();
        return array_map(function($value){ return 0; }, array_flip($df_place_order_routes));
    }
    public static function df_orders_routes_for_blade(){
        $normal_order_routes = self::df_normal_order_routes();
        $exchange_order_routes = self::df_exchange_order_routes();
        $return_order_routes = self::df_return_order_routes();
        return array_map(function($value){ return 0; }, array_merge(array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes)));
    }
    public static function df_normal_order_routes_for_blade(){
        $df_normal_order_routes = self::df_normal_order_routes();
        return array_map(function($value){ return 0; }, array_flip($df_normal_order_routes));
    }
    public static function df_exchange_order_routes_for_blade(){
        $df_exchange_order_routes = self::df_exchange_order_routes();
        return array_map(function($value){ return 0; }, array_flip($df_exchange_order_routes));
    }
    public static function df_return_order_routes_for_blade(){
        $df_return_order_routes = self::df_return_order_routes();
        return array_map(function($value){ return 0; }, array_flip($df_return_order_routes));
    }

    public static function purchase_management_routes_for_blade(){
    	$purchase_management_routes = self::purchase_management_routes();
        return array_map(function($value){ return 0; }, array_flip($purchase_management_routes));
    }
    public static function inventory_management_routes_for_blade(){
    	$inventory_management_routes = self::inventory_management_routes();
        return array_map(function($value){ return 0; }, array_flip($inventory_management_routes));
    }
    public static function oms_setting_routes_for_blade(){
        $oms_setting_routes = self::oms_setting_routes();
        return array_map(function($value){ return 0; }, array_flip($oms_setting_routes));
    }

    public static function businessarcade_routes(){
        $place_order_routes = self::place_order_routes();
        $normal_order_routes = self::normal_order_routes();
        $exchange_order_routes = self::exchange_order_routes();
        $return_order_routes = self::return_order_routes();
        return array_map(function($value){ return 0; }, array_merge(array_flip($place_order_routes), array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes)));
    }
    public static function dressfair_routes(){
        $place_order_routes = self::df_place_order_routes();
        $normal_order_routes = self::df_normal_order_routes();
        $exchange_order_routes = self::df_exchange_order_routes();
        $return_order_routes = self::df_return_order_routes();
        return array_map(function($value){ return 0; }, array_merge(array_flip($place_order_routes), array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes)));
    }

    public static function escape_routes_middleware(){
        $routes = collect(\Route::getRoutes())->map(function ($route) {
            if(in_array("GET", $route->methods())){
                return $route->uri();
            }
        });
        $get_routes = array_values(array_filter($routes->toArray()));
        $access_routes = array_values(self::all_routes_for_blade());
        $new = array();
        foreach ($get_routes as $key => $value) {
            if(!in_array($value, $access_routes)){
                $new[] = $value;
            }
        }
        $clean_array = preg_grep("/\/[(\[{].*[\]})](.*)/", $new, PREG_GREP_INVERT);
        return $clean_array;
    }
}