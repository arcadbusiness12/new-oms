<?php
namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Products\ProductSpecialModel;
use App\Models\OpenCart\ExchangeOrders\ApiIpModel;
use App\Models\OpenCart\ExchangeOrders\ExchangeOrderReturnProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Platform\Helpers\ToolImage;
use App\Models\Oms\ExchangeReason;
use DB;
use Session;
use Validator;
use Excel;

class ExchangeProductController extends Controller
{
    const VIEW_DIR = 'exchangeorders';
    const PER_PAGE = 20;
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $opencart_image_url = '';

    function __construct(){
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =  $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/';
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }

    public function searchProducts(Request $request){
        $allProducts = [];
        if (count(Input::all()) > 0){
            if (Input::get('product_title')){
                $product_title = htmlentities(Input::get('product_title'));
            }
            if (Input::get('product_model')){
                $model = Input::get('product_model');
            }
        }
        $products = ProductsModel::select(DB::Raw('*'))
                    ->join('product_description', 'product.product_id', '=', 'product_description.product_id');
        if (isset($product_title)){
            $products = $products->where('product_description.name', 'LIKE', "%{$product_title}%");
        }
        if (isset($model)){
            $products = $products->where('product.model', 'LIKE', "{$model}");
        }
        $products = $products->get()->toArray();
        if (count($products) > 0){
            $arrayFlat = new \App\Platform\Helpers\FlattenCollection();
            $flat = $arrayFlat->flattenWithKey($products)->getFlatten();
            $allProducts[] = $flat;
            foreach ($allProducts as $key => $product){
                $productOptions = new \App\Platform\OpenCart\ProductOptions();
                $options = $productOptions->getProductOptions($product['product_id']);
                $allProducts[$key]['options'] = $options;
                
                $specials = ProductSpecialModel::select('*')->where('product_id',$product['product_id'])->orderBy('priority','ASC')->first();
                if($specials) {
                    $allProducts[$key]['special'] = $specials->price;
                }
            }
        }
        return view(self::VIEW_DIR . '.product_search_form', ['products' => $allProducts]);
    }
    public function addToCart(Request $request){
        $cart_add_item = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        if($post_data['product']){
            $cart_add_item = array(
                'product_id' => $post_data['product']['product_id'],
                'quantity' => $post_data['product']['qty'],
            );
            if(isset($post_data['product']['option'])){
                foreach ($post_data['product']['option'] as $key => $value) {
                    $cart_add_item['option'][$key] = $value;
                }
            }
        }
        return response()->json(array('success' => true,'cart_add_item' => $cart_add_item));
    }
    public function getCart(Request $request){
        $products = array();
        $errors = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        // echo "<pre>"; print_r($post_data); die;
        if(isset($post_data['products']) && is_array($post_data['products'])){
            foreach ($post_data['products'] as $product) {
                $option = array();
                if(isset($product['option'])){
                    foreach ($product['option'] as $value) {
                        $option[] = array(
                            'product_option_id'         =>  $value['product_option_id'],
                            'product_option_value_id'   =>  $value['product_option_value_id'],
                            'name'                      =>  $value['name'],
                            'value'                     =>  $value['value'],
                        );
                    }
                }
                $products[] = array(
                    'cart_id'       =>  $product['cart_id'],
                    'product_id'    =>  $product['product_id'],
                    'image'         =>  $this->get_product_image($product['product_id'], 100, 100),
                    'name'          =>  $product['name'],
                    'model'         =>  $product['model'],
                    'quantity'      =>  $product['quantity'],
                    'options'       =>  $option,
                    'price'         =>  $product['price'],
                    'total'         =>  $product['total'],
                    'stock'         =>  $product['stock'],
                );
            }
        }
        if(isset($post_data['error'])){
            $errors = $post_data['error'];    
        }
        $totals = $post_data['totals'];
        return view(self::VIEW_DIR . '.cartview', ['products' => $products,'totals' => $totals,'errors' => $errors]);
    }
    public function getPaymentShipping(Request $request){
        $payment_method = '';
        $shipping_method = '';
        $payment_methods = array();
        $shipping_methods = array();
        $e_wallet_balance = 0;
        $totals = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        if(isset($post_data['totals'])){
            $totals = $post_data['totals'];
        }
        if(isset($post_data['e_wallet_balance'])){
            $e_wallet_balance = $post_data['e_wallet_balance'];
          }
        if(isset($post_data['payment_method'])){
            $payment_method = $post_data['payment_method'];
        }
        if(isset($post_data['shipping_method'])){
            $shipping_method = $post_data['shipping_method'];
        }
        if(isset($post_data['payment_methods']) && is_array($post_data['payment_methods'])){
            $payment_methods = $post_data['payment_methods']; 
            unset($payment_methods['free_checkout']);
        }
        if(isset($post_data['shipping_methods']) && is_array($post_data['shipping_methods'])){
            $shipping_methods = $post_data['shipping_methods']; 
        }
        $exchane_reasons = ExchangeReason::where('status',1)->get();
        // dd($payment_methods);
        return view(self::VIEW_DIR . '.paymentshippingview', ['payment_method' => $payment_method, 'payment_methods' => $payment_methods, 'shipping_method' => $shipping_method, 'shipping_methods' => $shipping_methods, 'totals' => $totals,'exchane_reasons'=>$exchane_reasons,'e_wallet_balance'=>$e_wallet_balance]);
    }
    public function addIP(Request $request){
    	$post_data = Input::all();
		$ApiIpModel = new ApiIpModel();
		$ApiIpModel->{ApiIpModel::FIELD_API_ID} = $post_data['api_id'];
        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
		$ApiIpModel->{ApiIpModel::FIELD_IP} = $ipAddress;
        $ApiIpModel->save();
		if($ApiIpModel->api_ip_id) $success = 'Success: You have modified APIs!';
		else $success = false;
		return response()->json(array('success' => $success));
    }
    public function getcartTotal(Request $request){
        $post_data = Input::all();
        $sub_total = 0;
        $total = 0;
        $exchange_item_amount = $post_data['exchange_item_amount'] ? $post_data['exchange_item_amount'] : '';
        if(isset($post_data['product'])){
            foreach ($post_data['product'] as $key => $product) {
                $sub_total = $sub_total + ($product['price'] * $product['qty']);
            }
        }
        $total = $sub_total - $post_data['exchange_item_amount'];
        $response_data = array(
            'success' => true,
            'sub_total' => $sub_total,
            'exchange_item_amount' => $exchange_item_amount,
            'total' => $total,
        );
        return response()->json($response_data);
    }
    public function update_return_product(){
        if(Input::all() > 0 && Input::get('submit') == 'update_return_product'){
            foreach (Input::get('order') as $order) {
                ExchangeOrderReturnProduct::where(ExchangeOrderReturnProduct::FIELD_ORDER_ID, Input::get('order_id'))->where(ExchangeOrderReturnProduct::FIELD_ORDER_PRODUCT_ID, $order['product_id'])->update(array(ExchangeOrderReturnProduct::FIELD_ORDER_QUANTITY => $order['quantity']));            
            }
        }
        return redirect('/exchange_orders/add/'. Input::get('order_id'));
    }
    protected function get_product_image($product_id = '', $width = 0, $height = 0){
        $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();
            
        if($product_image){
            if(file_exists($this->website_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
                $ToolImage = new ToolImage();
                return $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $product_image->image, $width, $height);
            }else{
                return $this->opencart_image_url . $product_image->image;
            }
        }else return $this->opencart_image_url . 'placeholder.png';
    }
}
