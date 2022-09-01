<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\omsSetting\categorySettingController;
use App\Http\Controllers\inventoryManagement\InventoryManagementController;
use App\Http\Controllers\Orders\OrdersAjaxController;
use App\Http\Controllers\Orders\OrdersController;
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
Route::prefix('orders')->middleware('auth')->group(function(){
    Route::controller(OrdersController::class)->group(function() {
        Route::get("/","index")->name('orders');
        Route::get("/online","online")->name('orders.online');
        Route::any("/update-customer-details","updateCustomerDetails")->name('orders.update-customer-details');
    });
    Route::controller(OrdersAjaxController::class)->group(function() {
        Route::post('/cancel-order','cancelOrder')->name('orders.cancel-order');
        Route::any('/activity-details', 'activityDetails')->name('orders.activity-details');
    });
});
Route::prefix('omsSetting')->middleware('auth')->group(function () {
    Route::controller(categorySettingController::class)->group(function() {
        route::get('/category/setting', 'categorySetting')->name('category.name');
        route::post('/save/group/main/category', 'saveMainCategory')->name('save.main.category');
        route::post('/save/sub/category', 'saveSubCategory')->name('save.sub.category');
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
        Route::post('/order/out/stock/product', 'orderOutStockProduct')->name('order.out.stock.product');
        Route::post('/add/purchase/order', 'addOrder')->name('add.purchase.order');
        Route::any('/place/purchase/order', 'placePurchaseOrder')->name('place.purchase.order');

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
