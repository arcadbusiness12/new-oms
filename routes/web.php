<?php

use App\Http\Controllers\PlaceOrder\DressFairPlaceOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\omsSetting\categorySettingController;
use App\Http\Controllers\inventoryManagement\InventoryManagementController;
use App\Http\Controllers\Orders\OrdersAjaxController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\PlaceOrder\PlaceOrderController;
use App\Http\Controllers\PurchaseManagement\PurchaseManagementAjaxController;
use App\Http\Controllers\PurchaseManagement\PurchaseManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::prefix('placeOrder')->middleware('auth')->group(function(){
    Route::controller(PlaceOrderController::class)->group(function() {
        Route::get('/', 'view')->name('place.order');
        Route::post('/ajax/search', 'searchProducts')->name('place.order.ajax.search');
        Route::post('/ajax/getCustomerDetails', 'getCustomerDetails')->name('place.order.ajax.getCustomerDetails');
        Route::post('/ajax/searchCustomer', 'searchCustomer')->name('place.order.ajax.search.Customer');
        Route::post('/ajax/addToCart', 'addToCart')->name('place.order.ajax.addToCart');
        Route::post('/ajax/getCart', 'getCart')->name('place.order.ajax.getCart');
        Route::post('/ajax/getAddress', 'getAddress')->name('place.order.ajax.getAddress');
        Route::post('/ajax/getPaymentAddress', 'getPaymentAddress')->name('place.order.ajax.getPaymentAddress');
        Route::post('/ajax/getShippingAddress', 'getShippingAddress')->name('place.order.ajax.getShippingAddress');
        Route::post('/ajax/getPaymentShipping', 'getPaymentShipping')->name('place.order.ajax.getPaymentShipping');
        Route::post('/ajax/addIP', 'addIP')->name('place.order.ajax.addIP');
        Route::post('/ajax/cart_total', 'getcartTotal')->name('place.order.ajax.cart_total');
        Route::post('/ajax/update_return_product', 'update_return_product')->name('place.order.ajax.update_return_product');
        Route::post('/ajax/addUserOrder', 'addUserOrder')->name('place.order.ajax.addUserOrder');
        Route::post('/ajax/save_customer', 'save_customer')->name('place.order.ajax.save_customer');
        Route::post('/ajax/get_customer', 'get_customer')->name('place.order.ajax.get_customer');
        Route::post('/ajax/get_product_name', 'get_product_name')->name('place.order.ajax.get_product_name');
        Route::post('/ajax/get_product_model', 'get_product_model')->name('place.order.ajax.get_product_model');
        Route::post('/ajax/get_zone', 'get_zone')->name('place.order.ajax.get_zone');
        Route::post('/ajax/get_area', 'get_area')->name('place.order.ajax.get_area');
        Route::post('/ajax/set_payment_address', 'set_payment_address')->name('place.order.ajax.set_payment_address');
    });
    //for df
    Route::controller(DressFairPlaceOrderController::class)->group(function() {
        Route::get('/df', 'view')->name('df.place.order');
        Route::post('/df/ajax/search', 'searchProducts')->name('df.place.order.ajax.search');
        Route::post('/df/ajax/getCustomerDetails', 'getCustomerDetails')->name('df.place.order.ajax.getCustomerDetails');
        Route::post('/df/ajax/searchCustomer', 'searchCustomer')->name('df.place.order.ajax.searchCustomer');
        Route::post('/df/ajax/addToCart', 'addToCart')->name('df.place.order.ajax.addToCart');
        Route::post('/df/ajax/getCart', 'getCart')->name('df.place.order.ajax.getCart');
        Route::post('/df/ajax/getAddress', 'getAddress')->name('df.place.order.ajax.getAddress');
        Route::post('/df/ajax/getPaymentAddress', 'getPaymentAddress')->name('df.place.order.ajax.getPaymentAddress');
        Route::post('/df/ajax/getShippingAddress', 'getShippingAddress')->name('df.place.order.ajax.getShippingAddress');
        Route::post('/df/ajax/getPaymentShipping', 'getPaymentShipping')->name('df.place.order.ajax.getPaymentShipping');
        Route::post('/df/ajax/addIP', 'addIP')->name('df.place.order.ajax.addIP');
        Route::post('/df/ajax/cart_total', 'getcartTotal')->name('df.place.order.ajax.cart_total');
        Route::post('/df/ajax/update_return_product', 'update_return_product')->name('df.place.order.ajax.update_return_product');
        Route::post('/df/ajax/addUserOrder', 'addUserOrder')->name('df.place.order.ajax.addUserOrder');
        Route::post('/df/ajax/save_customer', 'save_customer')->name('df.place.order.ajax.save_customer');
        Route::post('/df/ajax/get_customer', 'get_customer')->name('df.place.order.ajax.get_customer');
        Route::post('/df/ajax/get_product_name', 'get_product_name')->name('df.place.order.ajax.get_product_name');
        Route::post('/df/ajax/get_product_model', 'get_product_model')->name('df.place.order.ajax.get_product_model');
        Route::post('/df/ajax/get_zone', 'get_zone')->name('df.place.order.ajax.get_zone');
        Route::post('/df/ajax/get_area', 'get_area')->name('df.place.order.ajax.get_area');
        Route::post('/df/ajax/set_payment_address', 'set_payment_address')->name('df.place.order.ajax.set_payment_address');
    });
});
Route::prefix('orders')->middleware('auth')->group(function(){
    Route::controller(OrdersController::class)->group(function() {
        Route::get("/","index")->name('orders');
        Route::get("/online","online")->name('orders.online');
        Route::any("/update-customer-details","updateCustomerDetails")->name('orders.update-customer-details');
        Route::any('/reship-orders', 'approveReshipment')->name('orders.reship-orders');
        Route::get('/picking-list-awaiting', 'pickingListAwaiting')->name('orders.picking-list-awaiting');
        Route::get('/pack/order', 'packOrder')->name('orders.pack.order');
        Route::post('/get/pack/order', 'getPackOrder')->name('orders.get.pack.order');
        Route::post('/update/pack/order', 'updatePackOrder')->name('orders.update.pack.order');
        Route::get('/generate/awb', 'generateAwb')->name('orders.generate.awb');
        Route::get('/awb', 'awb')->name('orders.awb');
    });
    Route::controller(OrdersAjaxController::class)->group(function() {
        Route::post('/cancel-order','cancelOrder')->name('orders.cancel-order');
        Route::post('/reship', 'reship')->name('orders.reship');
        Route::any('/activity-details', 'activityDetails')->name('orders.activity-details');
        Route::post('/get/order/detail', 'getOrderDetail')->name('orders.get.order.detail');
        Route::post('/forward/for/shipping', 'forwardForShipping')->name('orders.forward.for.shipping');
    });
});
Route::prefix('omsSetting')->middleware('auth')->group(function () {
    Route::controller(categorySettingController::class)->group(function() {
        route::get('/category/setting', 'categorySetting')->name('category.name');
        route::post('/save/group/main/category', 'saveMainCategory')->name('save.main.category');
        Route::get('/get/sub/cates/{cate}', 'getSubCategories')->name('get.sub.cates');
        route::post('/save/sub/category', 'saveSubCategory')->name('save.sub.category');
        Route::post('/destroy/group/sub/cate/setting', 'destroySubCategory')->name('destroy.sub.category');
    });

});
Route::prefix('inventoryManagement')->middleware('auth')->group(function() {
    Route::controller(InventoryManagementController::class)->group(function() {
        route::get('/add/inventory', 'addInventory')->name('add.inventory');
        Route::get('/checking/for/group/code/{cate}', 'getLatestGroup')->name('cheking.for.group.code');
        Route::any('/inventory_manage/add_inventory_prod/{id?}', 'add_inventory_product_add')->name('inventory_manage.add_inventory_prod');
        Route::post('/add/inventory/product', 'addInventoryProduct')->name('add.inventory.product');
        Route::get('/inventory/dashboard', 'inventoryDashboard')->name('inventory.dashboard');
        Route::post('/inventory/change/product/status', 'changeProductStatus')->name('change.product.status');
        Route::get('/view/inventory/product/detail/{id}', 'viewInventory')->name('view.inventory.product.details');
        Route::any('/add/stock/{id?}', 'addStock')->name('inventory.add.stock');
        Route::get('/inventory/product/history/{id}', 'inventoryProductHistory')->name('inventory.product.history');
        Route::any('/inventory/edit/location/{id}', 'inventoryEditProductLocation')->name('inventory.edit.product.location');
        Route::any('/inventory/edit/product/{id}', 'EditInventoryProduct')->name('edit.inventory.product');
        Route::post('/inventory/edit/product/options/details', 'EditInventoryProductOptionDetails')->name('edit.inventory.product.option.details');
        Route::get('/inventory/destroy/product/{id}', 'destoryInventoryProduct')->name('inventory.destroy.product');
        Route::post('/inventory/print/pending/stock/label/{id?}', 'printPendingStockLabel')->name('inventory.print.pending.stock.label');
        Route::get('/inventory/stock/level', 'stockLevel')->name('inventory.stock.level');
        Route::post('/inventory/get/product/sku', 'getProductSku')->name('inventory.get.product.sku');
        Route::post('/get/inventory/stock/level/product', 'getInventoryStockLevelProduct')->name('get.inventory.stock.level.product');
        Route::post('/check/stock/level/duration/quantity', 'checkStockLevelDurationQuantity')->name('check.stock.level.duration.quantity');
        Route::post('/update/stock/level', 'updateStockLevel')->name('update.stock.level');
        Route::get('/stock/report', 'stockReport')->name('stock.report');
        Route::get('/inventory/alarm', 'inventoryAlarm')->name('inventory.alarm');
        Route::any('/inventory/options', 'inventoryOptions')->name('inventory.options');
        Route::post('/add/option/name', 'addOptionName')->name('add.option.name');
        Route::get('/edit/option/detail/{id}', 'editOptionDetails')->name('edit.option.details');
        Route::post('/add/option/details/{id}', 'addOptionDetails')->name('add.option.details');
        Route::get('/destroy/option/{id}', 'destroyOption')->name('destroy.option');
        Route::get('/destroy/option/value/{id}', 'destroyOptionValue')->name('destroy.option.value');
        Route::any('/option/connection', 'optionConnection')->name('option.connection');
    });
});
Route::prefix('PurchaseManagement')->middleware('auth')->group(function() {
    Route::controller(PurchaseManagementController::class)->group(function() {
        Route::any('/purchase/orders', 'purchaseOrders')->name('purchase.orders');
        Route::post('/order/out/stock/product', 'orderOutStockProduct')->name('order.out.stock.product');
        Route::post('/add/purchase/order', 'addOrder')->name('add.purchase.order');
        Route::any('/place/purchase/order', 'placePurchaseOrder')->name('place.purchase.order');
        Route::any('/new/purchase/order', 'newPurchaseOrder')->name('new.purchase.orders');

    });
    Route::controller(PurchaseManagementAjaxController::class)->group(function() {
        Route::post('/get/purchase/product/order/option', 'getPurchaseProductOrderOption')->name('get.purchase.product.order.option');
        Route::get('/add/purchase/product/manually', 'addPurchaseProductManualy')->name('add.purchase.product.manually');
        Route::post('/get/manually/all/options', 'getManuallyAllOptions')->name('get.manually.all.options');
        Route::post('/get/purchase/product/sku', 'getPurchaseProductSku')->name('get.purchase.product.sku');
        Route::post('/add/product', 'addProduct')->name('add.product');
    });
});
// Route::post('/add/inventory/product', [InventoryManagementController::class, 'addInventoryProduct']);
Route::get('/employee-performance/operation/records/{user_id}/{filter}', [HomeController::class, 'employeeOperationRecords']);
