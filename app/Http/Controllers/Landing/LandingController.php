<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\DressFairOms\OmsUserModel;
use App\Models\Oms\DoneDutyHistroryModel;
use App\Models\Oms\DutyAssignedUserModel;
use App\Models\Oms\EmployeeCustomeDutiesModel;
use App\Models\Oms\EmployeeLeaveModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Products\ProductOptionValueModel;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\OpenCart\Products\OptionValueDescriptionModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersHistoryModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\OmsNotificationModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\OmsUserModel as OmsOmsUserModel;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersStatusInterface;
use App\Models\Oms\UserStartEndTimeModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use DateTimeZone;
use DB;
use Illuminate\Support\Facades\Date;
use Session;

use function dd;
use function view;

/**
 * Description of LandingController
 *
 * @author Kamran Adil
 */
class LandingController extends Controller
{
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    private $opencart_image_url = '';

    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
    	$this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =  $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/';
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }

    public function home() {
        $ba_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $df_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 2)->where('posting_type', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::whereIn('store_id', [1,2])->where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $whereClause = [];
        if(session('role') == 'ADMIN') {
            $whereClause[] = array('is_admin_viewed', 0);
        }else {
            array_push($whereClause, ['is_viewed', 0]);
            array_push($whereClause, ['user_id', session('user_id')]);
        }
        // dd(json_decode(session('access'), true));
        
        // $notifications = OmsNotificationModel::where('user_id', session('user_id'))->where('is_viewed', 0)->orderBy('id', 'DESC')->get();
        // dd($whereClause);
        $notifications = OmsNotificationModel::where($whereClause)->orderBy('id', 'DESC')->get();
        // dd($notifications);
        $requestNotifications = OmsNotificationModel::where('is_approve', 0)->where('entity_type', 'work_ending_request')->orWhere('entity_type', 'work_start_request')->count();
        
        $pending_resellers = OmsOmsUserModel::where('user_group_id', OmsUserGroupInterface::OMS_USER_GROUP_RESELLER)->where('status', 0)->get()->toArray();
        $pending_resellers = count($pending_resellers);
        Session::put('pending_resellers', $pending_resellers);
        Session::put('request_notifications', $requestNotifications);
        Session::put('ba_main_setting_list', json_encode($ba_promotion_main_setting));
        Session::put('df_main_setting_list', json_encode($df_promotion_main_setting));
        Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
        Session::put('user_notifactions', json_encode($notifications->toArray() ? $notifications->toArray() : []));
        // dd(session('user_notifactions'));
        // User custom duties
        $custom_duties = EmployeeCustomeDutiesModel::with('files')->where('user_id', session('user_id'))->count();
        Session::put('custom_duties', $custom_duties);
        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);
        $orders_data = collect(DB::select(DB::Raw("SELECT 
            (SELECT COUNT(order_id) FROM ".env('DB_BAOPENCART_DATABASE').".oc_order WHERE date_added <= '".$today."' AND date_added >= '".$date."' AND order_status_id = '".OrdersModel::OPEN_CART_STATUS_PENDING."') AS pending,
            (SELECT COUNT(order_id) FROM ".env('DB_BAOPENCART_DATABASE').".oc_order WHERE date_added <= '".$today."' AND date_added >= '".$date."' AND order_status_id = '".OrdersModel::OPEN_CART_STATUS_SHIPPED."') AS shipped,
            (SELECT COUNT(order_id) FROM ".env('DB_BAOPENCART_DATABASE').".oc_order WHERE date_added <= '".$today."' AND date_added >= '".$date."' AND order_status_id = '".OrdersModel::OPEN_CART_STATUS_DELIVERED."') AS delivered, 
            (SELECT COUNT(order_id) FROM ".env('DB_BAOPENCART_DATABASE').".oc_order WHERE date_added <= '".$today."' AND date_added >= '".$date."' AND order_status_id = '".OrdersModel::OPEN_CART_STATUS_RETURNED."') AS returned 
            FROM ".env('DB_BAOPENCART_DATABASE').".oc_order LIMIT 1") ) )->first();

        $data['order_section'] = array(
            array(
                'name'      =>  'Pending',
                'href'      =>  route('orders') . "?order_status_id=" . OrdersModel::OPEN_CART_STATUS_PENDING,
                'counter'   =>  $orders_data->pending
            ),
            array(
                'name'      =>  'Shipped',
                'href'      =>  route('orders') . "?order_status_id=" . OrdersModel::OPEN_CART_STATUS_SHIPPED,
                'counter'   =>  $orders_data->shipped
            ),
            array(
                'name'      =>  'Delivered',
                'href'      =>  route('orders') . "?order_status_id=" . OrdersModel::OPEN_CART_STATUS_DELIVERED,
                'counter'   =>  $orders_data->delivered
            ),
            array(
                'name'      =>  'Returned',
                'href'      =>  route('orders') . "?order_status_id=" . OrdersModel::OPEN_CART_STATUS_RETURNED,
                'counter'   =>  $orders_data->returned
            )
        );

        $purchase_data = collect(DB::select(DB::Raw("SELECT 
            (SELECT COUNT(order_id) FROM oms_purchase_order WHERE created_at <= '".$today."' AND created_at >= '".$date."' AND order_status_id = '".OmsPurchaseOrdersStatusInterface::PURCHASE_STATUS_PENDING."') AS new,
            (SELECT COUNT(order_id) FROM oms_purchase_order WHERE created_at <= '".$today."' AND created_at >= '".$date."' AND order_status_id = '".OmsPurchaseOrdersStatusInterface::PURCHASE_STATUS_AWAIT_APPROVAL."') AS awaiting_approval,
            (SELECT COUNT(order_id) FROM oms_purchase_order WHERE created_at <= '".$today."' AND created_at >= '".$date."' AND order_status_id = '".OmsPurchaseOrdersStatusInterface::PURCHASE_STATUS_SHIPPED."') AS shipped, 
            (SELECT COUNT(order_id) FROM oms_purchase_order WHERE created_at <= '".$today."' AND created_at >= '".$date."' AND order_status_id = '".OmsPurchaseOrdersStatusInterface::PURCHASE_STATUS_DELIVERED."') AS delivered, 
            (SELECT COUNT(order_id) FROM oms_purchase_order WHERE created_at <= '".$today."' AND created_at >= '".$date."' AND order_status_id = '".OmsPurchaseOrdersStatusInterface::PURCHASE_STATUS_CANCELLED."') AS cancelled 
            FROM oms_purchase_order LIMIT 1") ) )->first();

        $data['purchase_section'] = array(
            array(
                'name'      =>  'New',
                'href'      =>  route('purchase_manage.awaiting_action'),
                'counter'   =>  $purchase_data->new
            ),
            array(
                'name'      =>  'Awaiting Approval',
                'href'      =>  route('purchase_manage.awaiting_approval'),
                'counter'   =>  $purchase_data->awaiting_approval
            ),
            array(
                'name'      =>  'Shipped',
                'href'      =>  route('purchase_manage.shipped'),
                'counter'   =>  $purchase_data->shipped
            ),
            array(
                'name'      =>  'Delivered',
                'href'      =>  route('purchase_manage.delivered'),
                'counter'   =>  $purchase_data->delivered
            ),
            array(
                'name'      =>  'Cancelled',
                'href'      =>  route('purchase_manage.cancelled'),
                'counter'   =>  $purchase_data->cancelled
            )
        );

        $lowstock_data = collect(DB::select(DB::Raw("SELECT p.product_id, pov.quantity, ois.minimum_quantity FROM oms_inventory_product ip 
            LEFT JOIN oms_inventory_stock ois ON (ois.product_id = ip.product_id)
            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product p ON (p.sku = ip.sku) 
            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
            WHERE DATE(p.date_modified) <= '" . $today . "' AND DATE(p.date_modified) >= '" . $date . "' 
            GROUP BY p.product_id 
            HAVING pov.quantity <= ois.minimum_quantity")))->count();
        $seller_data = collect(DB::select(DB::Raw("SELECT op.product_id FROM ".env('DB_BAOPENCART_DATABASE').".oc_order_product op LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_order_option oo ON (oo.order_product_id = op.order_product_id) LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_order o ON (oo.order_id = o.order_id) WHERE o.order_status_id > '0' AND DATE(o.date_added) <= '" . $today . "' AND DATE(o.date_added) >= '" . $date . "' GROUP BY op.name")))->count();
        $surplusstock_data = collect(DB::select(DB::Raw("SELECT p.product_id, pov.quantity, ois.average_quantity FROM oms_inventory_product ip 
            LEFT JOIN oms_inventory_stock ois ON (ois.product_id = ip.product_id)
            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product p ON (p.sku = ip.sku) 
            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
            WHERE DATE(p.date_modified) <= '" . $today . "' AND DATE(p.date_modified) >= '" . $date . "' 
            GROUP BY p.product_id 
            HAVING pov.quantity >= ois.average_quantity")))->count();
        $outofstock_data = collect(DB::select(DB::Raw("SELECT p.product_id, p.quantity as product_qty, pov.quantity as option_qty 
            FROM ".env('DB_BAOPENCART_DATABASE').".oc_product p 
            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
            WHERE (DATE(p.low_quantity_date) <= '" . $today . "' AND DATE(p.low_quantity_date) >= '" . $date . "') 
            OR (DATE(pov.low_quantity_date) <= '" . $today . "' AND DATE(pov.low_quantity_date) >= '" . $date . "') 
            GROUP BY p.product_id 
            HAVING p.quantity <= 0 
            OR pov.quantity <= 0")))->count();
        $finished_data = ProductsModel::where('status', 2)->where('status_finished_date', '<=', $today)->where('status_finished_date', '=>', $date)->count();

        $data['stock_section'] = array(
            array(
                'name'      =>  'Low Stock',
                'href'      =>  route('home.report.lowstock', 'filter=' . $filter),
                'counter'   =>  $lowstock_data
            ),
            array(
                'name'      =>  'Best Seller',
                'href'      =>  route('home.report.bestseller', 'filter=' . $filter),
                'counter'   =>  $seller_data
            ),
            array(
                'name'      =>  'Surplus Stock',
                'href'      =>  route('home.report.surplus_stock', 'filter=' . $filter),
                'counter'   =>  $surplusstock_data
            ),
            array(
                'name'      =>  'Out of Stock',
                'href'      =>  route('home.report.outofstock', 'filter=' . $filter),
                'counter'   =>  $outofstock_data
            ),
            array(
                'name'      =>  'Finished',
                'href'      =>  route('home.report.finished', 'filter=' . $filter),
                'counter'   =>  $finished_data
            )
        );
        $order_data = DB::table("oms_place_order AS opo")
            ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
            ->select("opo.order_id")
            ->where('opo.user_id',session('user_id'))
            // ->where('ord.oms_order_status','!=',5)
            ->where(function ($query) {
              $query->where('ord.oms_order_status','!=',5)
                  ->orWhereNull('ord.oms_order_status');
            })
            ->whereDate('opo.created_at',date('Y-m-d'))
            ->get();
        $totalOrders = $order_data->count();
        $duties = [];
        if(session('role') == 'ADMIN') {
            // dd(session('user_id'));
            $page = '.landing';
        }else {
        // dd(Input::all());
        
            // $duties = json_decode(session('duties'), true);
            $user = OmsOmsUserModel::with(['activities' => function($q) {
                $q->where('duration', '!=', 0);
            }])->where('user_id', session('user_id'))->first();
            $dateWhereClause = $this->checkFilterUser(Input::all(), $user);
            
            // dd($user->activities->toArray());
            // dd(session()->all());
            foreach($user->activities as $activity) {
                $target = 0;
                $achieved = 0;
                if($user->userGroupName['name'] == 'Sales Team') {
                    $performance_sales = EmployeePerformanceModel::select('achieved','target')->where('duty_list_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                    ->get();
                    foreach($performance_sales as $ac) {
                        $achieved += $ac['achieved'];
                        $target += $ac['target'];
                    }
                    $activity->quantity = (int)$target;
                }
                if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
                $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
                $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
                    // dd($activity);
                    // dd(session('user_id'));
                    $performance_sales = DoneDutyHistroryModel::where('duty_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                    ->count();
                    $achieved = $performance_sales;
                    if($activity->daily_compulsory == 1) {
                        $total_q = DoneDutyHistroryModel::where('duty_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                                                        ->groupBy('done_date')->get();
                        foreach($total_q as $ac) {
                            $target += $activity->quantity;
                       }
                    }else {
                        $target += $activity->monthly_tasks;
                    }
                    $activity->quantity = (int)$target;
                }
                    
                
                    
                // $performance_sales = EmployeePerformanceModel::where('duty_list_id', $activity->activity_id)->where($dateWhereClause)->sum('achieved');
                $activity->achieved = ($activity->activity_id == 2) ? $totalOrders + (int)$achieved : (int)$achieved;
                
                
            }
            // dd($user->activities->toArray());
            $duties = $user->activities->toArray();
            $page = '.staff_landing';
        }
        $notifications_from = date("Y-m-d");
        // $notifications_from = date("Y-m-d", strtotime("first day of previous month"));
        
        $data['duties'] = $duties;
        $data['notifications'] = OmsPurchaseOrdersHistoryModel::where('name', 'Supplier')->where(DB::Raw('DATE(created_at)'), '=', $notifications_from)->orderBy('order_history_id', 'DESC')->get()->toArray();
        
        $data['old_input'] = Input::all();
        // dd($data);
        // dd(session(session('access')));
        return view("landing".$page, $data);
    }

    public function resellerLanding() {
        return view("uac.reseller_landing");   
    }
    public function resellerLanding1() {
        return view("uac.reseller_landing2");   
    }
    public function privacyPolicy() {
        return view('uac.privacy_policy');
    }
    function checkFilterUser($filter, $user) {
        $dateWhereClause = [];
        if(count($filter) > 0) {
        if($filter['filter'] == 'week') {
            if($user->userGroupName['name'] == 'Sales Team') {
                array_push($dateWhereClause, ['created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d')]);
                array_push($dateWhereClause, ['created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d')]);
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
            $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
            $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
                // dd(Carbon::now()->endOfWeek()->format('Y-m-d'));
                array_push($dateWhereClause, ['done_date', '>=', Carbon::now()->startOfWeek()->format('Y-m-d')]);
                array_push($dateWhereClause, ['done_date', '<=', Carbon::now()->endOfWeek()->format('Y-m-d')]);
            }
            
        }else if($filter['filter'] == 'month') {
            if($user->userGroupName['name'] == 'Sales Team') {
                array_push($dateWhereClause, ['created_at', '>=', Carbon::now()->year.'-'.Carbon::now()->month.'-01']);
                array_push($dateWhereClause, ['created_at', '<=', Carbon::now()->endOfMonth()->format('Y-m-d')]);
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
            $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
            $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
                array_push($dateWhereClause, ['done_date', '>=', Carbon::now()->year.'-'.Carbon::now()->month.'-01']);
                array_push($dateWhereClause, ['done_date', '<=', Carbon::now()->endOfMonth()->format('Y-m-d')]);
            }
            
        }else {
            if($user->userGroupName['name'] == 'Sales Team') {
                $dateWhereClause[] = array('created_at', date('Y-m-d'));
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
            $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
            $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
                $dateWhereClause[] = array('done_date', date('Y-m-d'));
            }
        }
     }else {
        if($user->userGroupName['name'] == 'Sales Team') {
            $dateWhereClause[] = array('created_at', date('Y-m-d'));
        }
        if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
        $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
        $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
            $dateWhereClause[] = array('done_date', date('Y-m-d'));
        }
     }
        return $dateWhereClause;
    }

    public function report_lowstock(Request $request){
    	$whereClause = array();

        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);

        if(count(Input::all()) > 0){
            if (Input::get('product_title')){
                $name = Input::get('product_title');
                $whereClause[] = array('pd.name', 'LIKE', "{$name}%");
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
                $whereClause[] = array('p.sku', 'LIKE', "{$sku}%");
            }
        }

        $data['products'] = array();

        $sql = "SELECT p.product_id, p.sku, p.image, pd.name, pov.quantity, ois.minimum_quantity FROM oms_inventory_product ip 
	            LEFT JOIN oms_inventory_stock ois ON (ois.product_id = ip.product_id)
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product p ON (p.sku = ip.sku) 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_description pd ON (pd.product_id = p.product_id) 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
	            WHERE DATE(p.date_modified) <= '" . $today . "' AND DATE(p.date_modified) >= '" . $date . "'"; 
	    if (Input::get('product_title')){
            $name = Input::get('product_title');
	    	$sql .= " AND pd.name LIKE '".$name."'";
        }
        if (Input::get('product_sku')){
            $sku = Input::get('product_sku');
	    	$sql .= " AND p.sku LIKE '".$sku."'";
        }
	    $sql .= " GROUP BY p.product_id 
	            HAVING pov.quantity <= ois.minimum_quantity";

        $lowstock_data = DB::select(DB::Raw($sql));

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $lowstock_data = new \Illuminate\Pagination\LengthAwarePaginator(array_slice($lowstock_data, $offset, $perPage, true), count($lowstock_data), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
            
        $data['products'] = array();
        foreach ($lowstock_data as $key => $value) {
            $options = array();

            $product_options = ProductOptionValueModel::where('product_id', $value->product_id)->orderBy('product_option_value_id', 'ASC')->get();
            foreach ($product_options as $key => $product_option) {
                $inventory_stock = OmsInventoryStockModel::select('minimum_quantity')->where('option_id', $product_option->option_id)->where('option_value_id', $product_option->option_value_id)->first();
                
                $option_description = OptionDescriptionModel::select('name')->where('option_id', $product_option->option_id)->first();
                $option_value_description = OptionValueDescriptionModel::select('name')->where('option_value_id', $product_option->option_value_id)->first();
                if((int)$product_option->quantity <= (int)$inventory_stock['minimum_quantity']){
                    $options[] = array(
                        'name' =>  $option_description->name,
                        'value' =>  $option_value_description->name,
                        'quantity' =>  $product_option->quantity,
                        'minimum' =>  $inventory_stock['minimum_quantity'],
                    );
                }
            }

            $data['products'][] = array(
                'product_id'        =>  $value->product_id,
                'image'             =>  $this->get_product_image($value->image, 100, 100),
                'name'              =>  $value->name,
                'sku'               =>  $value->sku,
                'options'           =>  $options,
            );
        }
        $data['pagination'] = $lowstock_data->render();
        $data['page'] = $lowstock_data;
        $data['old_input'] = Input::all();

        return view("landing.report_lowstock", $data);
    }

    public function report_bestseller(Request $request){
    	$whereClause = array();

        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);

        if(count(Input::all()) > 0){
            if (Input::get('product_title')){
                $name = Input::get('product_title');
                $whereClause[] = array('pd.name', 'LIKE', "{$name}%");
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
                $whereClause[] = array('p.sku', 'LIKE', "{$sku}%");
            }
        }

        $data['products'] = array();

        $page = Input::get('page', 1);
        $perPage = 50;
        $offset = ($page * $perPage) - $perPage;

        $sql = "SELECT op.product_id,op.order_product_id, CONCAT(oo.name,'-',oo.value) as onv, op.name, op.model, SUM(op.quantity) as quantity, SUM(op.price) as price ,op.tax 
        		FROM ".env('DB_BAOPENCART_DATABASE').".oc_order_product op 
        		LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_order_option oo ON (oo.order_product_id = op.order_product_id) 
        		LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_order o ON (oo.order_id = o.order_id) 
        		WHERE o.order_status_id > '0' AND DATE(o.date_added) <= '" . $today . "' AND DATE(o.date_added) >= '" . $date . "'";

	    if (Input::get('product_title')){
            $name = Input::get('product_title');
	    	$sql .= " AND op.name LIKE '".$name."'";
        }
        if (Input::get('product_model')){
            $model = Input::get('product_model');
	    	$sql .= " AND op.model LIKE '".$model."'";
        }
	    $sql .= " GROUP BY onv, op.product_id";
	    $sql .= " ORDER BY op.product_id ASC, onv ASC";

        $bestseller_data = DB::select(DB::Raw($sql));
        $bestseller_data = new \Illuminate\Pagination\LengthAwarePaginator(array_slice($bestseller_data, $offset, $perPage, true), count($bestseller_data), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
        
        $data['products'] = $bestseller_data->items();

        /*$data['products'] = array();
        foreach ($bestseller_data as $key => $value) {
			$q = isset($options[$value->product_id][$value->onv]['quantity']) ? $options[$value->product_id][$value->onv]['quantity'] + $value->quantity : $value->quantity;

			$options[$value->product_id][$value->onv] = array(
				'option'	=>	$value->onv,
				'quantity'	=>	$q,
				'total'	=>	isset($options[$value->product_id][$value->onv]['total']) ? $options[$value->product_id][$value->onv]['total'] + $value->price + ($value->tax * $value->quantity) : $value->price + ($value->tax * $q),
			);

			$data['products'][$value->product_id] = array(
				'name'		=>	$value->name,
				'model'		=>	$value->model,
				'options'	=>	$options[$value->product_id],
			);
        }*/

        $data['pagination'] = $bestseller_data->render();
        $data['page'] = $bestseller_data;
        $data['old_input'] = Input::all();

        return view("landing.report_bestseller", $data);
    }

    public function report_surplus_stock(Request $request){
    	$whereClause = array();

        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);

        if(count(Input::all()) > 0){
            if (Input::get('product_title')){
                $name = Input::get('product_title');
                $whereClause[] = array('pd.name', 'LIKE', "{$name}%");
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
                $whereClause[] = array('p.sku', 'LIKE', "{$sku}%");
            }
        }

        $data['products'] = array();

        $sql = "SELECT p.product_id, p.image, p.sku, pd.name, pov.quantity, ois.average_quantity FROM oms_inventory_product ip 
	            LEFT JOIN oms_inventory_stock ois ON (ois.product_id = ip.product_id)
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product p ON (p.sku = ip.sku) 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_description pd ON (pd.product_id = p.product_id) 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
	            WHERE DATE(p.date_modified) <= '" . $today . "' AND DATE(p.date_modified) >= '" . $date . "'";

	    if (Input::get('product_title')){
            $name = Input::get('product_title');
	    	$sql .= " AND pd.name LIKE '".$name."'";
        }
        if (Input::get('product_sku')){
            $sku = Input::get('product_sku');
	    	$sql .= " AND p.sku LIKE '".$sku."'";
        }
	    $sql .= " GROUP BY p.product_id 
	            HAVING pov.quantity >= ois.average_quantity";

        $surplusstock_data = DB::select(DB::Raw($sql));

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $surplusstock_data = new \Illuminate\Pagination\LengthAwarePaginator(array_slice($surplusstock_data, $offset, $perPage, true), count($surplusstock_data), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
            
        $data['products'] = array();
        foreach ($surplusstock_data as $key => $value) {
            $options = array();

            $product_options = ProductOptionValueModel::where('product_id', $value->product_id)->orderBy('product_option_value_id', 'ASC')->get();
            foreach ($product_options as $key => $product_option) {
                $inventory_stock = OmsInventoryStockModel::select('minimum_quantity')->where('option_id', $product_option->option_id)->where('option_value_id', $product_option->option_value_id)->first();
                
                $option_description = OptionDescriptionModel::select('name')->where('option_id', $product_option->option_id)->first();
                $option_value_description = OptionValueDescriptionModel::select('name')->where('option_value_id', $product_option->option_value_id)->first();
                if((int)$product_option->quantity <= (int)$inventory_stock['minimum_quantity']){
                    $options[] = array(
                        'name' =>  $option_description->name,
                        'value' =>  $option_value_description->name,
                        'quantity' =>  $product_option->quantity,
                        'average' =>  $inventory_stock['average_quantity'],
                    );
                }
            }

            $data['products'][] = array(
                'product_id'        =>  $value->product_id,
                'image'             =>  $this->get_product_image($value->image, 100, 100),
                'sku'               =>  $value->sku,
                'name'              =>  $value->name,
                'options'           =>  $options,
            );
        }
        $data['pagination'] = $surplusstock_data->render();
        $data['page'] = $surplusstock_data;
        $data['old_input'] = Input::all();

        return view("landing.report_surplus_stock", $data);
    }

    public function report_outofstock(Request $request){
    	$whereClause = array();

        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);

        if(count(Input::all()) > 0){
            if (Input::get('product_title')){
                $name = Input::get('product_title');
                $whereClause[] = array('pd.name', 'LIKE', "{$name}%");
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
                $whereClause[] = array('p.sku', 'LIKE', "{$sku}%");
            }
        }

        $data['products'] = array();

        $sql = "SELECT p.product_id, pd.name, p.sku, p.image, p.quantity as product_qty, pov.quantity as option_qty 
	            FROM ".env('DB_BAOPENCART_DATABASE').".oc_product p 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_description pd ON (pd.product_id = p.product_id) 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_option_value pov ON (pov.product_id = p.product_id) 
	            WHERE (DATE(p.low_quantity_date) <= '" . $today . "' AND DATE(p.low_quantity_date) >= '" . $date . "') 
	            OR (DATE(pov.low_quantity_date) <= '" . $today . "' AND DATE(pov.low_quantity_date) >= '" . $date . "')";

	    if (Input::get('product_title')){
            $name = Input::get('product_title');
	    	$sql .= " AND pd.name LIKE '".$name."'";
        }
        if (Input::get('product_sku')){
            $sku = Input::get('product_sku');
	    	$sql .= " AND p.sku LIKE '".$sku."'";
        }
	    $sql .= " GROUP BY p.product_id HAVING p.quantity <= 0 OR pov.quantity <= 0";

        $finished_data = DB::select(DB::Raw($sql));

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $finished_data = new \Illuminate\Pagination\LengthAwarePaginator(array_slice($finished_data, $offset, $perPage, true), count($finished_data), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
            
        $data['products'] = array();
        foreach ($finished_data as $key => $value) {
        	$options = array();

            $product_options = ProductOptionValueModel::where('product_id', $value->product_id)->where('quantity', '<=', 0)->orderBy('product_option_value_id', 'ASC')->get();
            foreach ($product_options as $key => $product_option) {               
                $option_description = OptionDescriptionModel::select('name')->where('option_id', $product_option->option_id)->first();
                $option_value_description = OptionValueDescriptionModel::select('name')->where('option_value_id', $product_option->option_value_id)->first();

                $options[] = array(
                    'name' =>  $option_description->name,
                    'value' =>  $option_value_description->name,
                    'quantity' =>  $product_option->quantity,
                );
            }

            $data['products'][] = array(
                'product_id'        =>  $value->product_id,
                'image'             =>  $this->get_product_image($value->image, 100, 100),
                'name'             	=>  $value->name,
                'sku'               =>  $value->sku,
                'quantity'          =>  $value->product_qty,
                'options'           =>  $options,
            );
        }

        $data['pagination'] = $finished_data->render();
        $data['page'] = $finished_data;
        $data['old_input'] = Input::all();

        return view("landing.report_outofstock", $data);
    }

    public function report_finished(Request $request){
    	$whereClause = array();

        $filter = Input::get('filter', 'today');
        $today = date('Y-m-d');
        $date = $this->getDate($filter);

        if(count(Input::all()) > 0){
            if (Input::get('product_title')){
                $name = Input::get('product_title');
                $whereClause[] = array('pd.name', 'LIKE', "{$name}%");
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
                $whereClause[] = array('p.sku', 'LIKE', "{$sku}%");
            }
        }

        $data['products'] = array();

        $sql = "SELECT p.product_id, pd.name, p.sku, p.image FROM ".env('DB_BAOPENCART_DATABASE').".oc_product p 
	            LEFT JOIN ".env('DB_BAOPENCART_DATABASE').".oc_product_description pd ON (pd.product_id = p.product_id) 
	            WHERE DATE(p.status_finished_date) <= '" . $today . "' AND DATE(p.status_finished_date) >= '" . $date . "' AND status = '2'"; 

	    if (Input::get('product_title')){
            $name = Input::get('product_title');
	    	$sql .= " AND pd.name LIKE '".$name."'";
        }
        if (Input::get('product_sku')){
            $sku = Input::get('product_sku');
	    	$sql .= " AND p.sku LIKE '".$sku."'";
        }
	    $sql .= " GROUP BY p.product_id";

        $finished_data = DB::select(DB::Raw($sql));

        $page = Input::get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $finished_data = new \Illuminate\Pagination\LengthAwarePaginator(array_slice($finished_data, $offset, $perPage, true), count($finished_data), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
            
        $data['products'] = array();
        foreach ($finished_data as $key => $value) {
            $data['products'][] = array(
                'product_id'        =>  $value->product_id,
                'image'             =>  $this->get_product_image($value->image, 100, 100),
                'sku'               =>  $value->sku,
            );
        }
        $data['pagination'] = $finished_data->render();
        $data['page'] = $finished_data;
        $data['old_input'] = Input::all();

        return view("landing.report_finished", $data);
    }

    protected function getDate($filter){
    	if($filter == 'today'){
            $date = date('Y-m-d');
        }else if($filter == 'week'){
            $previous_week = strtotime("-1 week");
            $date = date("Y-m-d", $previous_week);
        }else if($filter == 'month'){
            $previous_month = strtotime("-1 month");
            $date = date("Y-m-d", $previous_month);
        }

        return $date;
    }
    protected function get_product_image($product_image = '', $width = 0, $height = 0){
        if($product_image){
            if(file_exists($this->website_image_source_path . $product_image) && !empty($width) && !empty($height)){
                $ToolImage = new ToolImage();
                return $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $product_image, $width, $height);
            }else{
                return $this->opencart_image_url . $product_image;
            }
        }else return $this->opencart_image_url . 'placeholder.png';
    }

    public function userViewedNotification($user) {
        // $notifications = OmsNotificationModel::where('user_id', $user)->update(['is_viewed' => 1]);
        $notifications = OmsNotificationModel::where('user_id', $user)->where('is_viewed', 0)->orderBy('id', 'DESC')->get();
        Session::forget('user_notifactions');
        Session::put('user_notifactions', json_encode($notifications->toArray() ? $notifications->toArray() : []));
        return response()->json([
            'status' => true
        ]);
    }

    public function getUserNotifications($user = null) {
        $whereClause = [];
        $orWhereClause = [];
        $orWhereClause1 = [];
        // for marketer if have access 
        if(session('user_group_id') == 8 && (array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true)))) {
            $this->notifications();
        }
        if(session('role') == 'ADMIN') {
            // $whereClause[] = array('entity_type', '!=', 'custom_duty');
            array_push($whereClause, ['entity_type', '!=', 'custom_duty']);
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
            array_push($whereClause, ['is_approve', 0]);
            array_push($whereClause, ['is_admin_viewed', 0]);
            // array_push($whereClause, ['user_id', session('user_id')]);
            // $orWhereClause1[] = array('is_viewed', 1);
        }elseif( is_array(json_decode(session('access'),true)) && array_key_exists('view/request/notifications', json_decode(session('access'),true)) && array_key_exists('view/request/notifications', json_decode(session('access'),true)) && !array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) && !array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true))) {
            array_push($whereClause, ['entity_type', 'work_ending_request']);
            array_push($whereClause, ['is_approve', 0]);
            array_push($whereClause, ['is_admin_viewed', 0]);
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
        }elseif( is_array(json_decode(session('access'),true)) && (array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true))) ) {
            array_push($whereClause, ['entity_type', '!=', 'custom_duty']);
            array_push($whereClause, ['entity_type', '!=', 'work_ending_request']);
            array_push($whereClause, ['entity_type', '!=', 'request_response']);
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
            $orWhereClause[] = array('assigned_by_viewed', 0);
        }else {
            array_push($whereClause, ['user_id', session('user_id')]);
            array_push($whereClause, ['entity_type', '!=', 'work_ending_request']);
            $orWhereClause[] = array('is_viewed', 0);
            // $orWhereClause1[] = array('is_viewed', 2);
        }
        
        $date = date("Y-m-d", strtotime("-30 days"));
        $notifications = OmsNotificationModel::where($whereClause)->where(function($q) use($orWhereClause, $orWhereClause1) {
            $q->where($orWhereClause)
            ->orWhere($orWhereClause1);
        })->where('created_at', '>=', $date)->orderBy('id', 'DESC')->get();
        Session::forget('user_notifactions');
        // dd($notifications);
        Session::put('user_notifactions', json_encode($notifications->toArray() ? $notifications->toArray() : []));

        return view('layout.notifications');
    }

    public function notifications() {
        $orWhereClause = [];
        $orWhereClause1 = [];
            $orWhereClause[] = array('assigned_by_viewed', 0);
            // $orWhereClause1[] = array('is_admin_viewed', 0);
            $notifications = OmsNotificationModel::whereIn('entity_type', ['work_ending_request','comment','comment_reply'])->where(function($q) use($orWhereClause, $orWhereClause1) {
                $q->where($orWhereClause)
                ->orWhere($orWhereClause1);
            })->orderBy('id', 'DESC')->get();
            Session::forget('user_notifactions');
            // dd($notifications);
            Session::put('user_notifactions', json_encode($notifications->toArray() ? $notifications->toArray() : []));
    
            return view('layout.notifications');
    }
    public function getUserNewNotificationCount($user = null) {
        $whereClause = [];
        $orWhereClause = [];
        $orWhereClause1 = [];
        if(session('role') == 'ADMIN') {
            // $whereClause[] = array('entity_type', '!=', 'custom_duty');
            array_push($whereClause, ['entity_type', '!=', 'custom_duty']);
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
            array_push($whereClause, ['is_approve', 0]);
            array_push($whereClause, ['is_admin_clear', 0]);
            $orWhereClause[] = array('is_admin_viewed', 0);
            
            // array_push($whereClause, ['user_id', session('user_id')]);
            // $orWhereClause1[] = array('is_viewed', 1);
            // $notifications = OmsNotificationModel::where($whereClause)->where(function($q) {
            //     $q->where('is_viewed', 1)
            //       ->orWhere('is_viewed', 0);
            // })->orderBy('id', 'DESC')->toSql();
        }elseif(is_array(json_decode(session('access'),true)) && array_key_exists('view/request/notifications', json_decode(session('access'),true))) {
            array_push($whereClause, ['entity_type', 'work_ending_request']);
            array_push($whereClause, ['is_approve', 0]);
            array_push($whereClause, ['is_admin_viewed', 0]);
            $orWhereClause1[] = array('entity_type', 'work_start_request1');
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
            array_push($whereClause, ['is_clear', 0]);
            array_push($whereClause, ['is_admin_clear', 0]);
        }elseif(is_array(json_decode(session('access'),true)) && (array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true)))) {
            array_push($whereClause, ['entity_type', '!=', 'custom_duty']);
            array_push($whereClause, ['entity_type', '!=', 'work_ending_request']);
            array_push($whereClause, ['entity_type', '!=', 'request_response']);
            array_push($whereClause, ['entity_type', '!=', 'assgined_custom_duty']);
            array_push($whereClause, ['entity_type', '=', 'comment']);
            $orWhereClause[] = array('assigned_by_viewed', 0);
            array_push($whereClause, ['is_assigned_by_clear', 0]);
        }else {
            array_push($whereClause, ['user_id', session('user_id')]);
            array_push($whereClause, ['entity_type', '!=', 'work_ending_request']);
            $orWhereClause[] = array('is_viewed', 0);
            array_push($whereClause, ['is_clear', 0]);
            // $orWhereClause1[] = array('is_viewed', 2);
        }
        // dd($whereClause);
        
        // for marketer if have access 
        // dd($whereClause);
        $date = date("Y-m-d", strtotime("-30 days"));
        if(session('user_group_id') == 8 && (array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true)))) {
            $orWhereClause[] = array('assigned_by_viewed', 0);
            // $orWhereClause1[] = array('is_admin_viewed', 0);
            $notifications = OmsNotificationModel::whereIn('entity_type', ['work_ending_request','comment','comment_reply'])->where(function($q) use($orWhereClause, $orWhereClause1) {
                $q->where($orWhereClause)
                ->orWhere($orWhereClause1);
            })->where('created_at', '>=', $date)->orderBy('id', 'DESC')->count();
        }else {
            $notifications = OmsNotificationModel::where($whereClause)->where(function($q) use($orWhereClause, $orWhereClause1) {
                $q->where($orWhereClause)
                ->orWhere($orWhereClause1);
            })->where('created_at', '>=', $date)->orderBy('id', 'DESC')->count();
        }
        
        // dd($notifications);
        $user_break_time = OmsOmsUserModel::select('break_time','office_location')->find(session('user_id'));
        // $zone = new DateTimeZone();
        $timezone_offset_minutes = 330;  // $_GET['timezone_offset_minutes']

        // Convert minutes to seconds
        $timezone_name = timezone_name_from_abbr(date('H:i:s'));
        
        // Asia/Kolkata
        // echo $timezone_name;
        
        $breaktime = 0;
        $break = '';
        if($user_break_time) {
            $time_stage = UserStartEndTimeModel::where('user_id', session('user_id'))->where('date', date('Y-m-d'))
                                               ->where('today_time', 0)->where('btn_action', 1)->first();
            if($time_stage) {
                if($user_break_time->office_location == 1) {
                    // dd($user_break_time->break_time);
                    $t = $user_break_time->break_time;
                    $t = explode(':', $t);
                    $h = $t[0] - 1;
                    $h = ($h < 9) ? '0'.$h : $h;
                    $bt = $h.':'.$t[1].':'.$t[2];
                }else {
                    $bt = $user_break_time->break_time;
                }
                $break = date('H:i', strtotime($bt));
                // $d = $break. '=='. date('H:i');
                // dd($d);
                if($break == date('H:i')) {
                    $breaktime = 1;
                }else {
                    $breaktime = 0;
                }
            }else {
                $time_stage = UserStartEndTimeModel::where('user_id', session('user_id'))->where('date', date('Y-m-d'))
                                               ->where('today_time', 0)->where('btn_action', 5)->first();
                if($time_stage) {
                    $breaktime = 1; 
                    $break = date('H:i');
                }
            }
        }else {
            $breaktime = 0;
        }
        $requestNotifications = OmsNotificationModel::where('is_approve', 0)->where('entity_type', 'work_ending_request')->orWhere('entity_type', 'work_start_request')->count();
        Session::put('request_notifications', $requestNotifications);
        $pending_resellers = OmsOmsUserModel::where('user_group_id', OmsUserGroupInterface::OMS_USER_GROUP_RESELLER)->where('status', 0)->get()->toArray();
        $pending_resellers = count($pending_resellers);
        Session::put('pending_resellers', $pending_resellers);
        $urequest =  UserStartEndTimeModel::where('user_id', $user)->where('today_time', 0)->where('date', date('Y-m-d'))->whereNotNull('request_approval')->first();
        $todaytime = UserStartEndTimeModel::where('user_id', session('user_id'))->where('date', Carbon::yesterday()->format('Y-m-d'))->where('today_time', 1)->whereNotNull('request_approval')->first();
        $todayNotifications = OmsNotificationModel::where('user_id', session('user_id'))->where('created_at', date('Y-m-d'))->where(function($query) {
            $query->where('entity_type', 'work_start_request')->orWhere('entity_type', 'request_response');
        })->first();
        $customDuties = 0;
        if(session('user_group_id') == 8 || session('user_group_id') == 1) {
            $customDuties = EmployeeCustomeDutiesModel::with('attachmentFiles')->where('is_close', 1)->where('end_date', '>=', date('Y-m-d'))->count();
        }
        Session::put('new_custom_duties', $customDuties);
        return response()->json([
            'count' => $notifications,
            'break' => $breaktime,
            'break_time' => $break,
            'curent' =>date('H:i'),
            'request' => $urequest,
            'start_work_request' => $todaytime,
            'today_notif' => $todayNotifications,
            'request_notifications_count' => $requestNotifications,
            'pending_resellers' => $pending_resellers,
            'new_custom_duties' => $customDuties
        ]);
        
    }

    public function startUserBreakTime($user) {
        if($user) {
            DB::table('oms_user_start_end_times')->where('user_id', $user)->where('date', date('Y-m-d'))
            ->where('today_time', 0)->update(['btn_action'=> 5]);

            return response()->json([
                'status' => true
            ]);
        }
    }

    public function leaveRequestNotifications() {
        if(session('role') == 'ADMIN') {
            $leaveNotifics = EmployeeLeaveModel::where('approved', 0)->whereDate('request_date', '>=', date('Y-m-d'))->where('status', 1)->count();
        }else {
            $leaveNotifics = OmsNotificationModel::where('entity_type', 'leave_request_response')->where('user_id', session('user_id'))->where('is_viewed', 0)->count();
        }
        
        
        $role = session('role');
        return response()->json([
            'request_count' => $leaveNotifics,
            'role' => $role
        ]);
    }

    public function clearLeaveRequestNotifications($user) {
        if($user) {
            OmsNotificationModel::where('entity_type', 'leave_request_response')->where('user_id', $user)->where('is_viewed', 0)->update(['is_viewed' => 1]);
            return response()->json([
                'status' => true,
                'role'  => session('role')
            ]);
        }
    }

    public function changebreakTime(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'break_time' => 'required'
        ]);
        $user_break_time = OmsOmsUserModel::find(session('user_id'));
        $user_break_time->break_time = $request->break_time;
        $user_break_time->update();
        DB::table('oms_user_start_end_times')->where('user_id', session('user_id'))->where('date', date('Y-m-d'))
        ->where('today_time', 0)->update(['btn_action'=> 1]);

        return response()->json([
            'status' => true
        ]);
    }
    public function storeUserStartTime($time, $user) {
        $todayTime = UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->where('today_time', 1)->first();
        if($todayTime) {
            return response()->json([
                'status' => false,
                'start_time' => $todayTime->start_time
            ]);
        }else {
            $existTodayTime = UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->where('today_time', 0)->whereNull('end_time')->first();
            if(!$existTodayTime) {
                $userTime = new UserStartEndTimeModel();
                $userTime->user_id = $user;
                $userTime->start_time = $time;
                $userTime->btn_action = 1;
                $userTime->date = date('Y-m-d');
                if($userTime->save()) {
                    return response()->json([
                        'status' => true,
                        'time' => $userTime->id,
                        'start_time' => date('h:i:s a', strtotime($userTime->start_time))
                    ]);
                }
            }
            
        }
        
    }

    public function storeUserBreakTime($time, $user) {
        
        $userTime =  UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->first();
        // dd("ok");
        // dd(($userTime->break_time) ? date('H:i:s', $userTime->break_time) : date('H:i:s', '00:00:00') + strtotime($time));
        // dd($userTime);
        $complete_per = calculateCompleteTasks($user);
        if(!$userTime) {
            $userTime =  UserStartEndTimeModel::where('user_id', $user)->where('date', '<', date('Y-m-d'))
                                                ->where('today_time', 0)->whereNull('end_time')->first();
        }
        $userTime->break_start = $time;
        $userTime->btn_action = 2;
        if($userTime->update()) {
            return response()->json([
                'status' => true,
                'work_percentage' => $complete_per['complete_per']
            ]);
        }
    }

    
    public function stopUserBreakTime($time, $user) {
        $userTime =  UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->first();
        if(!$userTime) {
            $userTime =  UserStartEndTimeModel::where('user_id', $user)->where('date', '<', date('Y-m-d'))
                                                ->where('today_time', 0)->whereNull('end_time')->first();
        }
        // dd($userTime);
        $break_start = new DateTime(@$userTime->break_start);
        $break_end = new DateTime($time);
        $interval = $break_start->diff($break_end);
        $d = $interval->format('%h')." Hours ".$interval->format('%i')." Minutes".$interval->format('%s')." Seconds";
        $timeInterval = $interval->format('%h').":".$interval->format('%i').":".$interval->format('%s');

        $timeInterval = (@$userTime->break_interval) ? strtotime(@$userTime->break_interval) + strtotime($timeInterval) - strtotime('00:00:00') : strtotime($timeInterval);
        $timeInterval = date('H:i:s', $timeInterval);
        // dd(($userTime->break_time) ? date('H:i:s', $userTime->break_time) : date('H:i:s', '00:00:00') + strtotime($time));
        // dd($timeInterval);
        $userTime->break_end = $time;
        $userTime->break_interval = $timeInterval;
        $userTime->btn_action = 1;
        if($userTime->update()) {
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function stopUserTime($time, $user, $action, $id = null) {
        $todayTime = UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->where('today_time', 1)->first();
        $whereClause = [];
        if(!$todayTime) {
            if($id && $id != null) {
                $whereClause[] = array('id', $id);
            }else {
                $whereClause[] = array('date', date('Y-m-d'));
            }
            $userTime =  UserStartEndTimeModel::where('user_id', $user)->where($whereClause)->first();
            // dd($userTime);
            $start = new DateTime($userTime->date. " ". $userTime->start_time);
            // dd($start);
            $end = new DateTime($time);
            $interval = $start->diff($end);
            // dd($interval->format('%a'));
            $hour1 = 0;
            $hour2 = 0;
            $minut2 = 0;
            $second = 0;
            if($interval->format('%a') > 0){
                $hour1 = $interval->format('%a')*24;
                
                $minut1 = $interval->format('%i')*24;
                }
                if($interval->format('%h') > 0){
                $hour2 = $interval->format('%h');
                $minut2 = $interval->format('%i');
                $second = $interval->format('%s');
                }
            $totalH = $hour1 + $hour2. ":" . $minut2 . ":" . $second;
            $d = $interval->format('%h')." Hours ".$interval->format('%i')." Minutes".$interval->format('%s')." Seconds";
            $timeInterval = $interval->format('%h').":".$interval->format('%i').":".$interval->format('%s');
            $userTime->end_time = $time;
            $userTime->total_worked = $totalH;
            $userTime->today_time = 1;
            $userTime->on_time = ($action == 1) ? 1 : 0;
            if($userTime->update()) {
                return response()->json([
                    'status' => true
                ]);
            }
        }else {
            return response()->json([
                'status' => false
            ]);
        }
        
    }

    public function checkUserTodayDuties($user) {
        // dd("OK");
        // $done_flag = true;
        // $pending_duties = [];
        // $customduties = EmployeeCustomeDutiesModel::where('user_id', $user)->where('end_date', date('Y-m-d'))->where('progress', "!=", 5)->where('is_close', 0)->get();
        // $duties = DutyAssignedUserModel::where('user_id', $user)->where('daily_compulsory', 1)->get();
        // // dd($customduties->toArray());
        // if(count($duties) > 0) {
        //     foreach($duties as $duty) 
        //     {
        //         $done = DoneDutyHistroryModel::where('user_id', $user)->where('duty_id', $duty->activity_id)->where('done_date', date('Y-m-d'))->count();
                
        //         if($done < $duty->quantity) {
        //             $done_flag = false;
        //             array_push($pending_duties, $duty);
        //         }
        //     }
        // }
        // if(count($customduties) > 0) {
        //     array_push($pending_duties, $customduties);
        // }
        $pending_duties = calculateCompleteTasks($user);
        return response()->json([
            'status' => true,
            'tasks' => $pending_duties['pending'],
            'end_time' => $pending_duties['end_time'],
            'duty_ending' => $pending_duties['duty_ending'] ? date("g:i:s a", strtotime($pending_duties['duty_ending'])) : '',
            'user_request' => $pending_duties['user_request']
        ]);  
    }
    public function checkUserTodayEnd($user) {
        $userTime =  UserStartEndTimeModel::where('user_id', $user)->where('date', date('Y-m-d'))->where('today_time', 1)->first();
        $userStartedTime =  UserStartEndTimeModel::where('user_id', $user)->where('today_time', 0)->orderBy('id', "DESC")->first();
        // dd($userStartedTime);
        if($userStartedTime) {
            $userStartedTime->start_time = $userStartedTime->start_time ? date('h:i:s a', strtotime($userStartedTime->start_time)) : $userStartedTime->start_time;
        $userStartedTime->end_time = $userStartedTime->end_time ? date('h:i:s a', strtotime($userStartedTime->end_time)) : null;
        }
        $complete_per = calculateCompleteTasks($user);
        if($userTime) {
            return response()->json([
                'status' => true,
                'start_time' => date('h:i:s a', strtotime($userTime->start_time)),
                'end_time' => date('h:i:s a', strtotime($userTime->end_time)),
                'btn_action' => $userStartedTime,
                'logedIn_user' =>  session('role'),
                'work_percentage' => $complete_per['complete_per']
            ]);
        }else {
            // $yesterday =  UserStartEndTimeModel::where('user_id', $user)->where('date', Carbon::yesterday()->format('Y-m-d'))->where('today_time', 1)->where('on_time', 1)->orderBy('id', "DESC")->first();
            // if($yesterday) {
            //     return response()->json([
            //         'status' => false,
            //         'btn_action' => 6,
            //         'logedIn_user' =>  session('role'),
            //         'work_percentage' => $complete_per['complete_per']
            //     ]);
            // }else {
                return response()->json([
                    'status' => false,
                    'btn_action' => $userStartedTime,
                    'logedIn_user' =>  session('role'),
                    'work_percentage' => $complete_per['complete_per']
                ]);
            // }
            
        }
    }

    public function requestForEndDuty(Request $request) {
        $this->validate($request, [
            'reason' => 'required'
        ]);
        $noti = OmsNotificationModel::where('user_id', session('user_id'))->where('created_at', date('Y-m-d'))
                                    ->where('entity_type','work_ending_request')->first();
        $todaytime = UserStartEndTimeModel::where('user_id', session('user_id'))->where('date', date('Y-m-d'))->where('today_time', 0)->first();
        // dd($todaytime);
        if($noti > 0) {
            return response()->json([
                'status' => false,
                'request' => 'exist'
            ]);
        }else {
            $enttity = [
                'id' => $todaytime->id,
                'title' => 'Approval request from '.session('firstname').' '.session('lastname'),
                'title1' => '',
                'user' => session('user_id'),
                'reason' => $request->reason,
                'date' => date("Y-m-d"),
                'addmin_comments' => [],
            ];
            // createNotification('work_ending_request', $enttity, session('user_id'));
            $todaytime->request_approval = 0;
            $todaytime->update();
            // $new_request = new OmsNotificationModel();
            // $new_request->user_id = session('user_id');
            // $new_request->entity_type = 'work_ending_request';
            // $new_request->entity = $request->reason;
            // $new_request->is_approve = 0;
            // $new_request->created_at = date("Y-d-m");
            // $new_request->save();
            return response()->json([
                'status' => true,
                'request' => 'pendding'
            ]);
        }
    }

    public function requestForStartDuty(Request $request) {
        // dd($request->all());
        // $this->validate($request, [
        //     'reason' => 'required'
        // ]);
        $noti = OmsNotificationModel::where('user_id', session('user_id'))->where('created_at', date('Y-m-d'))
                                    ->where('entity_type','work_start_request')->first();
        $todaytime = UserStartEndTimeModel::where('user_id', session('user_id'))->where('date', Carbon::yesterday()->format('Y-m-d'))->where('today_time', 1)->where('on_time', 1)->orderBy('id', "DESC")->first();
        
        if($noti) {
            return response()->json([
                'status' => false,
                'request' => 'exist'
            ]);
        }else {
            $enttity = [
                'id' => $todaytime->id,
                'title' => 'Start work request from '.session('firstname').' '.session('lastname'),
                'title1' => '',
                'user' => session('user_id'),
                'reason' => $request->reason,
                'date' => date("Y-m-d"),
                'addmin_comments' => [],
            ];
            // createNotification('work_start_request', $enttity, session('user_id'));
            $todaytime->request_approval = 0;
            $todaytime->update();
            return response()->json([
                'status' => true,
                'request' => 'pendding'
            ]);
        }
    }

    public function checkAdminRequestApproval() {
            
    }

    public function adminRequestResponse($notif, $time, $action, $request) {
        $noti = OmsNotificationModel::find($notif);
        $todaytime = UserStartEndTimeModel::find($time);
        $entity = json_decode($noti->entity, true);
        $ac = ($action == 1) ? 'Approved' : 'Rejected';
        $entity['title1'] = "Your request is ".$ac ;
        $noti->entity = json_encode($entity);
        $noti->entity_type = 'request_response';
        $noti->is_admin_viewed = 2;
        $noti->is_approve = $action;
        $noti->created_at = date('Y-m-d');
        if($noti->update()) {
            $todaytime->request_approval = $action;
            $todaytime->on_time = ($action == 1) ? 0 : 1;
            $todaytime->update();

            return response()->json([
                'status' => true
            ]);
        }
        
    }

    public function viewAllRequestNotifications($status = null) {
        $whereClause = [];
       if($status) {
        // $whereClause[] = array('is_approve', $status);
        $requests = OmsNotificationModel::with('user')->where('is_approve', 0)->where('entity_type', 'work_ending_request')->orWhere('entity_type', 'work_start_request')->orderBy('id', 'DESC')->orderBy('created_at', 'DESC')->paginate(5);
       }else {
        $requests = OmsNotificationModel::with('user')->where('entity_type', 'work_ending_request')->orWhere('entity_type', 'request_response')->orWhere('entity_type', 'work_start_request')->orderBy('id', 'DESC')->orderBy('created_at', 'DESC')->paginate(5);
       }
       OmsNotificationModel::whereIn('id', $requests->pluck('id')->toArray())->update(['is_admin_viewed' => 1]);
    
    //    $requests = [];
    
       return view('employee_performance.request_lists')->with(compact('requests'));
    }

    // Staff Requests 
    public function staffAllRequestNotifications() {
        $whereClause = [];
        // $whereClause[] = array('is_approve', $status);
        $requests = OmsNotificationModel::with('user')->where('user_id', session('user_id'))->where('entity_type', 'request_response')->orWhere('entity_type', 'work_ending_request')->orderBy('id', 'DESC')->orderBy('created_at', 'DESC')->paginate(5);
       OmsNotificationModel::whereIn('id', $requests->pluck('id')->toArray())->where('entity_type', 'request_response')->update(['is_viewed' => 1]);

       return view('employee_performance.staff_request_list')->with(compact('requests'));
    }

    public function adminCommentOnrequest(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'response_comment_text' => 'required'
        ]);
        $requests = OmsNotificationModel::select('id','entity')->find($request->notification_id);
        $entity = json_decode($requests->entity, true);
        array_push($entity['addmin_comments'], $request->response_comment_text);
        $newentity = json_encode($entity);
        $requests['entity'] = $newentity;
        if($requests->update()) {
            return response()->json([
                'status' => true,
                'comment' => $request->response_comment_text
            ]);
        }
        // dd($requests);
    }

    public function checkPendingCustomDuties() {
        $user = OmsOmsUserModel::find(session('user_id'));
        $inActives = [];
        $inTesting = [];
        $pendingChats = [];
        
        if(count(json_decode(session('access'), true)) > 0 && array_key_exists('marketer/custom/duties/marketer',json_decode(session('access'), true)) && in_array($user->user_group_id, [1,2,6,8]) == true) {
            $inActives = EmployeeCustomeDutiesModel::where('is_close', 1)->where('end_date', '>=', date('Y-m-d'))->get();
            // dd($inActives);
            $inTesting = EmployeeCustomeDutiesModel::where('progress', 2)->get();
            
            // $paid_ads_chat = PaidAdsCampaign::with('chatResults')->where('status', 1)->where('start_date', '<', date('Y-m-d'))->get();
            // $yesterday = date('Y-m-d', strtotime("-1 days"));
            // if(count($paid_ads_chat) > 0) {
            //     foreach($paid_ads_chat as $chat) {
            //         $exist = false;
            //         foreach($chat->chatResults as $result) {
            //             if($result->date == $yesterday) {
            //                 $exist = true;
            //             }
            //         }

            //         if(!$exist) {
            //             $ch = [
            //                 'campaign' => $chat->campaign_name,
            //                 'date'     => $yesterday 
            //             ];
            //             array_push($pendingChats, $ch);
            //         }
            //         // PromotionProductPostModel::where('main_setting_id', $chat->main_setting_id)->where('') 
            //     }
            // }
            // if(count($pendingChats) > 0) {
            //     return response()->json([
            //         'status' => true,
            //         'pendingChat' => 1,
            //         'chats' => $pendingChats
            //     ]);
            // }else
            if(count($inActives) > 0 || count($inTesting) > 0) {
                return response()->json([
                    'status' => true,
                    'activity' => 1,
                    'pending_activities' => count($inActives),
                    'testing_activities' => count($inTesting),
                    'in_a_duties' => $inActives,
                    'testing_duties' => $inTesting
                ]);
            } else {
                return response()->json([
                    'status' => true,
                    'activity' => 0
                ]);
            }
        }else {
            return response()->json([
                'status' => false,
                'activity' => 0,
            ]);
        }
        // dd($inActives->toArray());
        // foreach($users as $user) {
        //     $u_access = json_decode($user->user_access, true);
        //     if(count($u_access) > 0) {
        //         if(in_array('marketer/custom/duties/marketer', $u_access)) {
        //             echo "Yes";
        //         }else {
        //             echo "No";
        //         }
        //         // dd($u_access);
        //     }else {

        //     }
    }
 
    public function checkPendingPaidAdschat() {
        $pendingChats = [];
        $paid_ads_chat = PaidAdsCampaign::with('chatResults')->where('status', 1)->where('start_date', '<', date('Y-m-d'))->get();
        
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            if(count($paid_ads_chat) > 0) {
                foreach($paid_ads_chat as $chat) {
                    $exist = false;
                    foreach($chat->chatResults as $result) {
                        if($result->date == $yesterday) {
                            $exist = true;
                        }
                    }

                    if(!$exist) {
                        $ch = [
                            'campaign' => $chat->campaign_name,
                            'date'     => $yesterday 
                        ];
                        array_push($pendingChats, $ch);
                    }
                    // PromotionProductPostModel::where('main_setting_id', $chat->main_setting_id)->where('') 
                }
            }
            if(count($pendingChats) > 0) {
                return response()->json([
                    'status' => true,
                    'pendingChat' => 1,
                    'chats' => $pendingChats
                ]);
            }else {
                return response()->json([
                    'status' => true,
                    'pendingChat' => 0
                ]);
            }
    }

    public function checkDesgnerPendingDuties() {
        $flag = false;
        $pending_duties = [];
        $designer_duties = EmployeeCustomeDutiesModel::where('user_id', session('user_id'))->whereIn('duty_list_id', [1,3])
                         ->whereIn('progress', [0,1])->where('end_date', '<=', date('Y-m-d'))->where('is_close', 0)->get();
           
            if(count($designer_duties) > 0) {
                foreach($designer_duties as $duty) {
                    $difference = Carbon::parse(Carbon::parse(date('Y-m-d')))->diffInDays(date('Y-m-d', strtotime($duty->end_date)));
                    // if($difference == 0) {
                        $flag = true;
                        array_push($pending_duties, $duty->title);
                    // }
                }
                
            }

            if($flag) {
                return response()->json([
                    'status' => true,
                    'pendingDuties' => 1,
                    'duties' => $pending_duties
                ]);
            }else {
                return response()->json([
                    'status' => true,
                    'pendingDuties' => 0
                ]);
            }
    }

    function clearUserAllNotifications() {
        $whereClause = [];
        if(session('role') == 'ADMIN') {
            $viewer = ['is_admin_clear' => 1];
         }elseif(array_key_exists('employee-performance/designer/custom/duties', json_decode(session('access'),true)) || array_key_exists('employee-performance/custom/duties', json_decode(session('access'),true))){
            // dd("Yse");
            $viewer = ['is_assigned_by_clear' => 1];
         }else {
            $whereClause[] = array('user_id', session('user_id'));
            $viewer = ['is_clear' => 1];
         }
          OmsNotificationModel::where($whereClause)->update($viewer);
          return response()->json([
              'status' => true
          ]);
          
    }






























































































































































































































































































































































    




































































































































    



    

    public function checkDesgnerPendingDutiesEndDate() {
        DB::table('oms_inventory_product')->delete();
         DB::table('oms_orders')->delete();
        DB::table('oms_purchase_order')->delete();
        DB::table('oms_purchase_order_product')->delete();
        DB::table('oms_purchase_product')->delete();
        DB::table('oms_purchase_shipped_order_product')->delete();
        DB::table('oms_options')->delete();
        DB::table('airwaybill_tracking')->delete();
        DB::table('oms_options_details')->delete();
        DB::table('reseller_products')->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_product'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_product'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_product_description'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_product_description'))->delete();

        DB::table('oms_inventory_product_option')->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option_value_description'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option_value_description'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option_value'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option_value'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_order'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_order'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_order_product'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_order_product'))->delete();
       
    }
}
