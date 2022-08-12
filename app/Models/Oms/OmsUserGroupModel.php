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
            'Online_Orders'         =>  'orders/online',
            'Ready_For_Return'      =>  'orders/ready-for-return',
            'Cancel_Orders'         =>  'orders/cancel-order',
            'Pick_List'             =>  'orders/picking-list-awaiting',
            'Forward_Pick_List'     =>  'orders/frwd-to-q-fr-awb-generation',
            'Generate_&_Print_AWB'  =>  'orders/generate-awb',
            'Pack_Orders'           =>  'orders/pack_order',
            'Ship_Orders'           =>  'orders/ship-orders',
            'Return_Orders'         =>  'orders/return_order',
            'Reship_Orders'         =>  'orders/reship',
            'Approve_Reshipment'         =>  'orders/reship-orders',
            'Approve_Duplicate'     =>  'orders/duplicate-orders',
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

    public static function customer_return_order_request() {
        return array(
            'Customer_Return_Order_Request' => 'customer/return/request'
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
            'Online_Orders'         =>  'df/orders/online',
            'Ready_For_Return'      =>  'df/orders/ready-for-return',
            'Pick_List'             =>  'df/orders/picking-list-awaiting',
            'Generate_&_Print_AWB'  =>  'df/orders/generate-awb',
            'Pack_Orders'           =>  'df/orders/pack_order',
            'Ship_Orders'           =>  'df/orders/ship-orders',
            'Return_Orders'         =>  'df/orders/return_order',
            'Reship_Orders'         =>  'df/orders/reship',
            'Approve_Reshipment'    =>  'df/orders/reship-orders',
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

    public static function df_customer_return_order_request() {
        return array(
            'Customer_Return_Order_Request' => 'df/customer/return/request'
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
            'Dashboard'             =>  'inventory_manage/dashboard',
            
            'Stock_Level'       	=>  'inventory_manage/stock_level',
            'Add_Stock'         	=>  'inventory_manage/add_stock',
            // 'Pack_Order'        	=>  'inventory_manage/pack_order',
            // 'Return_Order'      	=>  'inventory_manage/return_order',
            'Inventory_Alarm'   	=>  'inventory_manage/inventory_alarm',
            'Reports'   			=>  'inventory_manage/reports',
            // 'Inventory_Options'     =>  'inventory_manage/inventory_options',
        );
    }
    public static function inventory_management_dashboard_option_routes() {
        return array(
                'Details'           => 'inventory_manage/dashboard/details',
                'Location'          => 'inventory_manage/dashboard/location',
                'Edit'              => 'inventory_manage/dashboard/edit',
                'Delete'            => 'inventory_manage/dashboard/delete',
                'Print'             => 'inventory_manage/dashboard/print',
        );
    }
    public static function promotion_management_routes() {
        return array(
            'Group_page' => 'promotion/product/groups',
            'Organic_post' => 'promotion/organic/post',
            'Paid_Ads' => 'promotion/paid/ads'
        );
    }
    public static function oms_group_page_options_routes(){
        return array(
            'Site_prices'              =>  'prices/update-site-prices',
            'Website_promotion'        =>  'prices/update-site-promotion-prices',
            'Assign_type'              =>  'change/group/type'
        );
    }
    public static function oms_organic_post_options_routes(){
        return array(
            'Setting_Template'              =>  'get/setting/template',
            'product_List'                  =>  'promotion/organic/organic_posts',
            'BA_Work'                       => 'promotion/ba/work',
            'DF_Work'                       =>  'promotion/dressf/work',
            'Setting'                       =>  'promotion/settings/1',
            'Organic_Setting_Actions'       =>  'promotion/organic/settings/actions'
        );
    }
    public static function oms_paid_ads_options_routes(){
        return array(
            'Paid_produt_List'              =>  'promotion/paid/ads/paid_ads',
            // 'Setting'                       =>  'promotion/paid/ad/setting'
            'Paid_Ads_Setting'              =>  'promotion/settings/2',
            'Paid_Ads_Setting_Actions'      =>  'promotion/paid/settings/actions'
        );
    }
    public static function employee_performance_management_routes() {
      return array(
              'Stock'             => 'employee-performance/stock',
              'Sales'             => 'employee-performance/sale/team',
              'Conversation'      => 'employee-performance/sale/conversation',
              'Designer'          => 'employee-performance/designer',
              'IT_Team'           => 'employee-performance/it/team',
              'Marketing'         => 'employee-performance/marketing',
              'Photography'       => 'employee-performance/photography',
              'Model'             => 'employee-performance/model',
              'Duties_Setting'    => 'employee-performance/duties/setting',
              'Work_Report'       => 'employee-performance/work/report',
              'Irregular_Custom_Duties' => 'employee-performance/irregular/custom/duties',
        );
      //   return array_merge(
      //     self::employeePerformanceSaleRoutes(),
      //     self::employeePerformanceOperationRoutes(),
      //     self::employeePerformanceDesignerRoutes(),
      //     self::employeePerformanceItTeamRoutes(),
      //     self::employeePerformanceMarketingRoutes()
      // );
    }
    // public static function employeePerformanceRoutes(){
    //   return array(
    //             'Stock'             => 'employee-performance/stock',
    //             'Save_Conversation' => 'employee-performance/custom/duties'
    //   );
    // }
    public static function employeePerformanceSaleRoutes(){
      return array(
        'Today_Sale'       =>  'employee-performance/sale/all-daily-progress', 
        'Update_Progress'  =>  'employee-performance/sale/save-daily-progress',
        'Report'           =>  'employee-performance/sale',
        'Commission_Report'=>  'commission/sale/on-total-delivered-amount',
      );
    }
    public static function employeePerformanceOperationRoutes(){
      return array(
        'Save_Conversation' =>  'employee-performance/operation/save-conversation',
      );
    }
    public static function employeePerformanceDesignerRoutes(){
      return array(
        'Daily_Work'        =>  'employee-performance/designer/save-daily-work',
        'Assigned_Custom_Duties'        =>  'employee-performance/designer/custom/duties',
        'Custom_Duties_Report'        =>  'employee-performance/designer/custom/duty/report',
        'New_Product_Image'        =>  'employee-performance/designer/new/product/image',
      );
    }

    public static function employeePerformanceItTeamRoutes(){
        return array(
          'Web_Developer_Custom_Duties'        =>  'employee-performance/web/developer/custom/duties',
          'Smart_Look'                         =>  'employee-performance/web/developer/smart/look',
          'Web R&D'                            =>  'employee-performance/webdeveloper/R&D',
          'App_Developer_Custom_Duties'        =>  'employee-performance/app/developer/custom/duties',
          'Smart_look'                         =>  'employee-performance/app/developer/smart/look',
          'App R&D'                            =>  'employee-performance/app/developer/R&D',
        );
      }

    public static function employeePerformanceMarketingRoutes(){
      return array(
                'Save_Ad_Chat'             => 'employee-performance/marketing/save-add-chat',
                'Updates_&_Assign_Regular_Duties'           => 'marketer/custom/duties/marketer',
                'Smart_Look'            => 'employee-performance/smart/look',
                'Custom_Duties_Assigning'            => 'irregular/custom/duties',
                'Product_Listing'            => 'employee-performance/marketer/product/listing',
                'Ads_Updates'            => 'employee-performance/marketer/ba/paid/ads/work'
                
      );
    }
    public static function employeePerformancePhotographyRoutes(){
      return array(
          'Products_Shoot'         => 'employee-performance/photography/product-shoot',  
          'Save_Shoot'             => 'employee-performance/photography/save-shoot-data',  
          'Post_Shoot'             => 'employee-performance/photography/save-shoot-posting',  
      );
    }
    public static function employeePerformanceModelRoutes(){
      return array(
          'Products_Shoot'         => 'employee-performance/model/product-shoot',  
          'Save_Shoot'             => 'employee-performance/model/save-shoot-data',  
          'Post_Shoot'             => 'employee-performance/model/save-shoot-posting',  
      );
    }
    public static function employeePerformanceDuties_setting(){
        return array(
            'Duties'             => 'performance/duties',
            'Activities'         => 'performance/duty/activities'
                  
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
    public static function oms_vouchers_routes(){
      return array(
          'Receipts'                 =>  'accounts/receipts',
          'Pending'                  =>  'accounts/pending-receipts',
          'Payments'                 =>  'accounts/payments'
      );
   }

   public static function oms_requests_routes() {
    return array(
        'Requests' => 'view/request/notifications'
    );
   }

   public static function oms_manage_custom_duties() {
       return array(
           'Manage_Custom_Duty' => 'marketer/custom/duties/marketer'
       );
   }

   public static function oms_manage_reseller_routes() {
    return array(
        'Reseller_Users'               => 'rusers/1',
        'Product_List'                 => 'product/list',
        'Assigned_Product'             => 'assigned/products',
        'Place_Order'                  => 'place/order',
        'Orders'                       => 'all/orders',
        'Return_Orders'                => 'reseller/orders/return/order',
        'Account'                      => 'transaction',
        'Account_Summary'              => 'reseller/account/summary/report',
        'Credit_Note'                  => 'reseller/credit/note',
        'Withdraw_Request'             => 'reseller/withdraw/request',
        'Customer_E_Wallet'            => 'customer/e_wallet'
    );
  }

  public static function ResellerUsersRouts() {
    return array(
        'Add_Users'           => 'new/ruser',
        'Users_Action_Option' => 'ruser/action/option',
    );
  }
  public static function ResellerProductListRoutes() {
    return array(
        'Assign_Product_To_Reseller'   => 'assign/product/to/reseller',
    );
  }
  public static function ResellerAssignedProductRoutes() {
    return array(
        'Update_Product_Price'         => 'update/products/price',
    );
  }

  public static function ResellerWithdrawRequestRoutes() {
    return array(
        'Approve_Reject_Options'         => 'withdraw/approve/reject/request',
        'Payment_Option'                 => 'withdraw/payment/option',
    );
  }
  public static function ResellerCustomerEWalletRoutes() {
    return array(
        'E_Wallet_Approve_Reject_Options'         => 'e_wallet/approve/reject',
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
            array_values(self::oms_setting_routes()),
            array_values(self::oms_requests_routes()),
            array_values(self::oms_manage_custom_duties()),
            array_values(self::oms_manage_reseller_routes())
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
    public static function inventory_management_dashboard_option_routes_for_lable() {
        $inventory_management_dashboard_option_routes = self::inventory_management_dashboard_option_routes();
        return array_map(function($value) { return 0;}, array_flip($inventory_management_dashboard_option_routes));
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
        $customer_return_order_request_route = self::customer_return_order_request();
        return array_map(function($value){ return 0; }, array_merge(array_flip($place_order_routes), array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes), array_flip($customer_return_order_request_route)));
    }
    public static function dressfair_routes(){
        $place_order_routes = self::df_place_order_routes();
        $normal_order_routes = self::df_normal_order_routes();
        $exchange_order_routes = self::df_exchange_order_routes();
        $return_order_routes = self::df_return_order_routes();
        $customer_return_order_request_route = self::df_customer_return_order_request();
        return array_map(function($value){ return 0; }, array_merge(array_flip($place_order_routes), array_flip($normal_order_routes), array_flip($exchange_order_routes), array_flip($return_order_routes), array_flip($customer_return_order_request_route)));
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

    public function duty() {
        return $this->belongsTo(DutyModel::class, 'duty_id');
    }
}