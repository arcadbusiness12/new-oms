<?php

use App\Http\Controllers\Accounts\ReceiptController;
use App\Http\Controllers\Accounts\PaymentController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Catalog\AttributeController;
use App\Http\Controllers\Catalog\ProductListingController;
use App\Http\Controllers\Exchange\ExchangeOrdersAjaxController;
use App\Http\Controllers\Exchange\ExchangeOrdersController;
use App\Http\Controllers\Exchange\ReturnOrdersController;
use App\Http\Controllers\PlaceOrder\DressFairPlaceOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\omsSetting\categorySettingController;
use App\Http\Controllers\inventoryManagement\InventoryManagementController;
use App\Http\Controllers\omsSetting\localisation\GeoZoneController;
use App\Http\Controllers\omsSetting\PaymentMethodController;
use App\Http\Controllers\omsSetting\ShippingMethodController;
use App\Http\Controllers\Orders\OrdersAjaxController;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\PlaceOrder\PlaceOrderController;
use App\Http\Controllers\productgroup\ProductGroupController;
use App\Http\Controllers\productgroup\PromotionScheduleSettingController;
use App\Http\Controllers\PurchaseManagement\PurchaseManagementAjaxController;
use App\Http\Controllers\PurchaseManagement\PurchaseManagementController;
use App\Http\Controllers\rolepermision\RolePermissionController;
use App\Http\Controllers\performance\MarketingPerformanceController;
use App\Http\Controllers\performance\SalePerformancaeController;
use App\Http\Controllers\performance\StockPerformanceController;
use App\Http\Controllers\performance\OperationPerformanceController;
use App\Http\Controllers\performance\DesignerPerformanceeController;
use App\Http\Controllers\performance\ItController;
use App\Http\Controllers\Settings\CommissionController;
use App\Http\Controllers\ShippingProvider\DiliveryPanda;
use App\Http\Controllers\ShippingProvider\JTCourier;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;

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
Route::prefix('place/order')->middleware('auth')->group(function(){
    Route::controller(PlaceOrderController::class)->group(function() {
        Route::get('/{store}', 'view')->name('place.order');
        Route::post('/ajax/search', 'searchProducts')->name('place.order.ajax.search');
        Route::post('/get/product/sku', 'getProductSku')->name('place.order.get.product.sku');
        Route::post('/add/to/cart', 'addToCart')->name('place.order.add.to.cart');
        Route::post('/get/cart', 'getCart')->name('place.order.get.cart');
        Route::post('/remove/cart', 'removeCart')->name('place.order.remove.cart');
        Route::post('/update/cart', 'updateCart')->name('place.order.update.cart');
        Route::post('/search/customer', 'searchCustomer')->name('place.order.search.customer');
        Route::post('/load/areas', 'loadAreas')->name('place.order.load.areas');
        Route::post('/save/customer', 'saveCustomer')->name('place.order.save_customer');
        Route::post('/set/payment/method', 'setPaymentMethod')->name('place.order.set.payment.method');
        Route::post('/set/shipping/method', 'setShippingMethod')->name('place.order.set.shipping.method');
        Route::get('/shipping/payment', 'paymentShipping')->name('place.order.shipping.payment');
        Route::post('/confirm', 'confirmOrder')->name('place.order.confirm');
        // Route::post('/ajax/getCustomerDetails', 'getCustomerDetails')->name('place.order.ajax.getCustomerDetails');
        // Route::post('/ajax/addUserOrder', 'addUserOrder')->name('place.order.ajax.addUserOrder');
        // Route::post('/ajax/get_customer', 'get_customer')->name('place.order.ajax.get_customer');
        // Route::post('/ajax/get_product_name', 'get_product_name')->name('place.order.ajax.get_product_name');
        // Route::post('/ajax/get_zone', 'get_zone')->name('place.order.ajax.get_zone');
        // Route::post('/ajax/get_area', 'get_area')->name('place.order.ajax.get_area');
        // Route::post('/ajax/set_payment_address', 'set_payment_address')->name('place.order.ajax.set_payment_address');
    });
});
//orders routes
Route::prefix('orders')->middleware('auth')->group(function(){
    Route::controller(OrdersController::class)->group(function() {
        Route::get("/","index")->name('orders');
        Route::get("/online","online")->name('orders.online');
        Route::post("/online/approve","onlineApprove")->name('orders.online.approve');
        Route::any("/update-customer-details","updateCustomerDetails")->name('orders.update-customer-details');
        Route::any('/reship-orders', 'approveReshipment')->name('orders.reship-orders');
        Route::get('/picking-list-awaiting', 'pickingListAwaiting')->name('orders.picking-list-awaiting');
        Route::get('/pack/order', 'packOrder')->name('orders.pack.order');
        Route::post('/get/pack/order', 'getPackOrder')->name('orders.get.pack.order');
        Route::post('/update/pack/order', 'updatePackOrder')->name('orders.update.pack.order');
        Route::get('/generate/awb', 'generateAwb')->name('orders.generate.awb');
        Route::get('/awb', 'awb')->name('orders.awb');
        Route::get('/awb/generated', 'awbGenerated')->name('orders.awb.generated');
        Route::get('/ship/order', 'shipOrdersToCourier')->name('orders.ship.order');
        Route::post('/ship/orders/to/courier', 'shipOrders')->name('orders.ship.orders.to.courier');
        Route::any('/ready/for/return', 'readyForReturn')->name('orders.ready.for.return');
        Route::get('/return/order', 'returnOrder')->name('orders.return.order');
        Route::post('/get/return/order', 'getReturnOrder')->name('orders.get.return.order');
        Route::get('/print/label/{id}', 'printLabel')->name('orders.print.label');
        Route::post('/orders/update/return/order', 'updateReturnOrder')->name('orders.update.return.order');
    });
    Route::controller(OrdersAjaxController::class)->group(function() {
        Route::post('/cancel-order','cancelOrder')->name('orders.cancel-order');
        Route::post('/reship', 'reship')->name('orders.reship');
        Route::post('/track/courier', 'trackOrderCourier')->name('orders.track.courier');
        Route::any('/activity-details', 'activityDetails')->name('orders.activity-details');
        Route::post('/get/order/detail', 'getOrderDetail')->name('orders.get.order.detail');
        Route::post('/forward/for/shipping', 'forwardForShipping')->name('orders.forward.for.shipping');
        Route::any('/print/awb', 'printAwb')->name('orders.print.awb');
        Route::post('/get/order/id/from/airwaybill', 'getOrderIdFromAirwayBill')->name('orders.get.order.id.from.airwaybill');
        Route::post('/get/user/order/history', 'userOrderHistory')->name('get.user.order.history');
        Route::post('/forword/for/awb/generation', 'forwardOrderToQueueForAirwayBillGeneration')->name('orders.forword.for.awb.generation');
    });
});
//exchange routes
Route::prefix('exchange')->middleware('auth')->group(function(){
    Route::controller(ExchangeOrdersController::class)->group(function() {
        Route::get("/","index")->name('exchange');
        Route::post('/create', 'createExchange')->name('exchange.create');
        Route::post('/delete', 'delete')->name('exchange.delete');
        Route::get('/pack', 'pack')->name('exchange.pack');
        Route::post('/get/pack', 'getPack')->name('exchange.get.pack');
        Route::post('/update/pack', 'updatePack')->name('exchange.update.pack');
        Route::get('/generate/awb', 'generateAwb')->name('exchange.generate.awb');
        Route::get('/awb', 'awb')->name('exchange.awb');
        Route::get('/awb/generated', 'awbGenerated')->name('exchange.awb.generated');
        Route::get('/ship', 'shipExchangeToCourier')->name('exchange.ship.to.courier');
        Route::post('/ship/to/courier', 'shipExchange')->name('exchange.ship');
        Route::get('/return', 'return')->name('exchange.return');
        Route::post('/get/return', 'getReturn')->name('exchange.get.return');
        Route::post('/update/return', 'updateReturn')->name('exchange.update.return');
        Route::get('/print/label/{id}', 'printLabel')->name('exchange.print.label');
        Route::get('/picking/list/awaiting', 'pickingListAwaiting')->name('exchange.picking.list.awaiting');
    });
    Route::controller(ExchangeOrdersAjaxController::class)->group(function() {
        Route::post('/add/to/cart', 'addToCart')->name('exchange.add.to.cart');
        Route::post('/get/cart', 'getCart')->name('exchange.get.cart');
        Route::post('/update/cart', 'updateCart')->name('exchange.update.cart');
        Route::post('/remove/cart', 'removeCart')->name('exchange.remove.cart');
        Route::post('/set/payment/method', 'setPaymentMethod')->name('exchange.set.payment.method');
        Route::post('/set/shipping/method', 'setShippingMethod')->name('exchange.set.shipping.method');
        Route::post('/cancel', 'cancel')->name('exchange.cancel');
        Route::get('/shipping/payment', 'paymentShipping')->name('exchange.shipping.payment');
        Route::post('/get/exchange/detail', 'getExchangeDetail')->name('exchange.get.exchange.detail');
        Route::post('/confirm', 'confirm')->name('exchange.confirm');
        Route::post('/get/id/from/airwaybill', 'getExchangeIdFromAirwayBill')->name('exchange.get.order.id.from.airwaybill');

        Route::get('/cancel/quantity', 'cancelQuantity')->name('exchange.cancel.quantity');
        Route::post('/forword/for/awb/generation', 'forwardOrderToQueueForAirwayBillGeneration')->name('exchange.forword.for.awb.generation');
        Route::any('/print/awb', 'printAwb')->name('exchange.print.awb');
        Route::post('/forward/for/shipping', 'forwardForShipping')->name('exchange.forward.for.shipping');
    });
});
//Return routes
Route::prefix('return')->middleware('auth')->group(function(){
    Route::controller(ReturnOrdersController::class)->group(function(){
        Route::get("/","index")->name('return');
        Route::get('/awb/generated', 'awbGenerated')->name('return.awb.generated');
        Route::any('/print/awb', 'printAwb')->name('return.print.awb');
        Route::get('/search', 'return')->name('return.search');
        Route::post('/get/return', 'getReturn')->name('return.get');
        Route::post('/update', 'updateReturn')->name('return.update');
    });
});
// =======================  Accounts routes start ===========================
Route::prefix('accounts')->middleware('auth')->group(function() {
    Route::controller(ReceiptController::class)->group(function(){
        Route::any('/receipts', 'index')->name('accounts.receipts');
        Route::any('/get/receipt/popup', 'getReceiptPopup')->name('accounts.get.receipt.popup');
        Route::get('/pending/receipts', 'pendingReciepts')->name('accounts.pending.receipts');
        Route::post('/save/pending/receipts', 'savePendingReciepts')->name('accounts.save.pending.receipts');
        // Route::post('/accounts/receive-pending-receipts', 'receivePendingReciepts')->name('receive.pending.receipts');
        // Route::post('/accounts/update-shipping-payment', 'updateShippingPayment')->name('accounts.update.payment');
        Route::post('/process/courier/excel/file', 'processPendingExReceiptFile')->name('accounts.process.courier.excel.file');
    });
    Route::controller(PaymentController::class)->group(function(){
        Route::any('/payments', 'index')->name('accounts.payments');
    });
});
Route::group(['namespace' => 'ShippingProvider', 'middleware' => ['auth']], function() {
    // Route::get('/jeebly/invoice/{id}', 'JeeblyCourier@invoice')->name('jeebly.invoice');
    // Route::get('/risingstar/invoice/{id}', 'RisingStar@invoice')->name('risingstar.invoice');
    Route::get('/deliverypanda/invoice/{id}',[DiliveryPanda::class, 'invoice'])->name('deliverypanda.invoice');
    Route::get('/JT/invoice/{id}', [JTCourier::class,'invoice'])->name('jtexpress.invoice');
});
Route::prefix('omsSetting')->middleware('auth')->group(function () {
    Route::controller(UserController::class)->group(function() {
        Route::get('/users', 'getUsers')->name('setting.users');
        Route::any('/users/add', 'addUser')->name('setting.users.add');
        Route::any('/users/edit/{id}', 'editUser')->name('setting.users.edit');
    });
    Route::controller(categorySettingController::class)->group(function() {
        route::get('/category/setting', 'categorySetting')->name('category.name');
        route::post('/save/group/main/category', 'saveMainCategory')->name('save.main.category');
        Route::get('/get/sub/cates/{cate}', 'getSubCategories')->name('get.sub.cates');
        route::post('/save/sub/category', 'saveSubCategory')->name('save.sub.category');
        Route::post('/destroy/group/sub/cate/setting', 'destroySubCategory')->name('destroy.sub.category');
    });

    Route::controller(PaymentMethodController::class)->group(function() {
        Route::get('/payment/method', 'paymentMethods')->name('payment.method');
        Route::post('/add/payment/method', 'addPaymentMethods')->name('add.payment.method');
    });
    Route::controller(ShippingMethodController::class)->group(function() {
        Route::get('/shipping/method', 'shippingMethods')->name('shipping.method');
        Route::get('/add/shipping/method', 'addShippingMethods')->name('add.shipping.method');
        Route::post('/save/shipping/method', 'saveShippingMethods')->name('save.shipping.method');
        Route::get('/edit/shipping/method/{shippingMethod}', 'editShippingMethod')->name('edit.shipping.method');
        Route::post('/update/shipping/method', 'updateShippingMethods')->name('update.shipping.method');
        Route::get('/get/countries', 'getCountries')->name('get.countries');
        Route::post('/free/shipping/setting/form', 'AddFreeShippingSetting')->name('free.shipping.setting.form');
        Route::get('/destroy/weight/amount/{id}', 'destroyWeightAmount')->name('destroy.weight.amount');
    });
    Route::prefix('localisations')->controller(GeoZoneController::class)->group(function() {
        Route::get('/geo/zones', 'geoZones')->name('geo.zones');
        Route::get('/add/geo/zones', 'addGeoZones')->name('add.geo.zone');
        Route::get('/get/zones/{country}', 'getZones')->name('get.zones');
        Route::get('/get/areas/{city}', 'getAreas')->name('get.areas');
        Route::post('/save/goe/zone', 'saveGeoZone')->name('save.geo.zones');
        Route::get('/edit/goe/zone/{id}', 'editGeoZone')->name('edit.geo.zone');
        Route::post('/update/goe/zone', 'updateGeoZone')->name('update.geo.zones');
    });
});

Route::prefix('rolepermision')->middleware('auth')->group(function () {
    Route::controller(RolePermissionController::class)->group(function() {
        Route::get('/roles', 'getRoles')->name('get.roles');
        Route::post('/add/role', 'addEditRole')->name('add.edit.role');
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
        Route::post('/update/awaiting/action/order', 'updateAwaitingActionArder')->name('update.awaiting.action.order');
        Route::post('/awaiting/action/update/request', 'updateAwaitingActionCancelled')->name('awaiting.action.update.request');
        Route::post('/add/approval/comment', 'addApprovalComment')->name('add.approval.comment');
        Route::post('/supplier/cancelled/awaiting/action/order/request', 'supplierCancelledAwaitingActionOrderRequest')->name('supplier.cancelled.awaiting.action.order.request');
        Route::get('/edit/purchase/orders/{order}', 'editPurchaseOrders')->name('edit.purchase.orders');
        Route::post('/update/purchase/order', 'updatePurchaseOrders')->name('update.purchase.order');
        Route::any('/awaiting/approval', 'awaitingApproval')->name('awaiting.approval.purchase.orders');
        Route::post('/update/awaiting/approval/order', 'updateAwaitingApprovalOrder')->name('update.awaiting.approval.order');
        Route::post('/update/confirmed/approval/order', 'updateConfirmedActionCancelled')->name('update.confirmed.approval.order');
        Route::any('/confirmed', 'confirmedOrders')->name('confirmed.purchase.orders');
        Route::post('/confirmed/order/cancelled', 'confirmedOrderCancelled')->name('confirmed.order.cancelled');
        Route::get('/ship/order/{id}', 'orderShipping')->name('ship.order');
        Route::post('/add/to/ship', 'addToShip')->name('add.to.ship');
        Route::post('/purchaseManage/ship/to/dubai', 'shipToDubai')->name('ship.to.dubai');
        Route::any('/get/to/be/shipped', 'getToBeShipped')->name('get.to.be.shipped');
        Route::post('/tobe/ship/order/stock/cancel/request', 'toBeShipOrderCancelRequest')->name('tobe.ship.order.stock.cancel.request');
        Route::get('/view/confirmed/{order}', 'viewConfirmed')->name('view.confirmed');
        Route::get('/shipped/orders', 'shippedOrders')->name('get.shipped.orders');
        Route::get('/add/to/deliver/orders', 'addToDeliver')->name('add.to.deliver');
        Route::post('/update/deliver', 'UpdateDeliver')->name('update.deliver');
        Route::get('/barcode/generate/{label}/{id}', 'barcodeGenerate')->name('barcode.generate');
        Route::get('/delivered/orders', 'deliveredOrders')->name('get.delivered.orders');
        Route::get('/cancelled/orders', 'cancelledOrders')->name('get.cancelled.orders');
        Route::any('/shipped/stock/cancelled/requests', 'shippedStockCancelledRequests')->name('shipped.stock.cancelled.requests');
        Route::post('/update/stock/cancel/order/request',  'updateStockCancelOrderRequest')->name('update.stock.cancel.order.request');
        Route::any('/to/be/shipped/stock/cancelled/requests', 'toBeShippedStockCancelledRequests')->name('to.be.shipped.stock.cancelled.requests');
        Route::post('/update/to/be/stock/cancel/order/request',  'updateToBeStockCancelOrderRequest')->name('update.to.be.stock.cancel.order.request');
        Route::post('/update/to/be/stock/cancel/order/request',  'updateToBeStockCancelOrderRequest')->name('update.to.be.stock.cancel.order.request');
        Route::get('/add/complaint', 'addComplaint')->name('add.complaint');
        Route::any('/update/complaint/order', 'updateComplaintOrder')->name('update.complaint.order');
        Route::any('/accounts', 'accounts')->name('accounts');
        Route::any('/account/summary/report', 'accountSummaryReport')->name('account.summary.report');
        Route::post('/account/summary/report/ajax', 'accountSummaryReportAjax')->name('account.summary.report.ajax');
        Route::get('/withdraw/requests', 'withdrawRequests')->name('withdraw.request');
        Route::post('/withdraw/payment', 'withdrawPayment')->name('withdraw.payment');
        Route::get('/update/withdraw/request/status', 'updateWithdrawRequestStatus')->name('update.withdraw.request.status');
        Route::any('/withdraw/money', 'withdrawMoney')->name('withdraw.money');
        Route::get('/supplier/account/summary', 'accountSummary')->name('account.summary');
        Route::any('/shipping_providers', 'shipping_providers')->name('shipping.providers');
        Route::post('/purchase_manage/updateShippingProvider', 'updateShippingProvider')->name('purchase_manage.updateShippingProvider');
    });
    Route::controller(PurchaseManagementAjaxController::class)->group(function() {
        Route::post('/get/purchase/product/order/option', 'getPurchaseProductOrderOption')->name('get.purchase.product.order.option');
        Route::get('/add/purchase/product/manually', 'addPurchaseProductManualy')->name('add.purchase.product.manually');
        Route::post('/get/manually/all/options', 'getManuallyAllOptions')->name('get.manually.all.options');
        Route::post('/get/purchase/product/sku', 'getPurchaseProductSku')->name('get.purchase.product.sku');
        Route::post('/search/group/code', 'searchGroupCode')->name('search.group.code');
        Route::post('/add/product', 'addProduct')->name('add.product');
    });
});


Route::prefix('Catalog')->middleware('auth')->group(function() {
    Route::controller(ProductListingController::class)->group(function() {
        Route::any('/product/listing', 'ProductListing')->name('product.listing');
        Route::get('/edit/product/listing/{product}', 'EditProductListing')->name('edit.product.listing');
        Route::post('/save/listing/description', 'saveListingDescription')->name('save.listing.description');
        Route::get('/product/listing/details/{product}/{store}', 'productListingDetails')->name('product.listing.details');
        Route::post('/upload/cropped/image', 'uploadCroppedImage')->name('upload.cropped.image');
        Route::post('/upload/gsllery/images', 'uploadGalleryImages')->name('upload.gallery.images');
        Route::post('/remove/gsllery/image', 'removeGalleryImage')->name('remove.gallery.image');
        Route::post('/generate/product/seo/url', 'generateSeoUrl')->name('generate.product.seo.url');
        Route::post('save/special/price', 'saveSpecialPrice')->name('save.special.price');
        Route::post('save/discount/price', 'saveDiscountPrice')->name('save.discount.price');
        Route::post('save/reward/points', 'saveRewardPoints')->name('save.reward.points');
        Route::post('remove/special/price', 'removeSpecialPrice')->name('remove.speacial.price');
        Route::post('/save/listing/data/form', 'saveListingDataForm')->name('save.listing.data.form');
    });
});

Route::prefix('productgroup')->middleware('auth')->group(function() {
    Route::controller(ProductGroupController::class)->group(function() {
        Route::get('/add/main/category/to/group/{cate}/{group}', 'addMainCategoryToGroup')->name('add.main.category.to.group');
        // Route::post('/add/sub/category/to/group', 'addSubCategoryToGroup')->name('add.sub.category.to.group');
        Route::get('/add/sub/category/to/group/{cate}/{group}', 'addSubCategoryToGroup')->name('add.sub.category.to.group');
        Route::get('/change/group/type/{type}/{group}', 'changeGroupType')->name('change.group.type');
        Route::any('/product/group/change/product/status', 'groupChangeProductStatus')->name('group.change.product.status');
        Route::any('/get/product/size/chart', 'getProductSizeChart')->name('get.product.size.chart');
        Route::any('/update/product/size/chart', 'updateProductSizeChart')->name('update.product.size.chart');
        Route::get('/promotion/product/{page}', 'productGroup')->name('promotion.product');
        Route::get('/promotion/organic/{page}', 'productGroup')->name('promotion.organic');
        Route::get('/promotion/paid/ad/product/list/{page}', 'producpaidAdProductListtGroup')->name('promotion.paid.ad.product.list');
        Route::any('/update/site/prices', 'sitePrice')->name('update_site_prices');
        Route::any('/prices/update/site/promotion/prices', 'sitePromotionPrice')->name('update.site.promotion.prices');
        Route::get('/promotion/paid/ads/template/settings/{type}', 'promotionPaidAdsTemplateSettings')->name('promotion.paid.ads.template');
        Route::get('/get/setting/template/{store}/{group}/{type}/{cate}', 'getSettingTemplate')->name('get.setting.template');
        Route::get('/get/template/schedules/{schedule}/{type}/{group_id}/{store}/{selected_cate}', 'getTemplateSchedules')->name('get.template.schedules');
        Route::any('/promotion/settings/{type}', 'promotionOrganicSettings')->name('promotion.settings');
        Route::any('/group/product/by/type/', 'groupProductByType')->name('get.product.type');
    });
    Route::controller(PromotionScheduleSettingController::class,)->group(function() {
        Route::get('/get/paid/ads/setting/template/form/{setting?}', 'paidAdsSettingTemplateForm')->name('paid.ads.setting.template.form');
        Route::get('/sub/categories/for/paid/setting/{cate}', 'getSubCategoriesForPaidSetting')->name('sub.categories.for.paid.setting');
        Route::post('/save/promotion/paid/ad/setting', 'savePaidAdsSetting')->name('save.promotion.paid.ad.setting');
        Route::get('/create/main/setting/copy/{setting}', 'createMainSettingCopy')->name('create.main.setting.copy');
        Route::get('/destroy/main/setting/{setting}', 'destroyMainSetting')->name('destroy.main.setting');
        Route::get('/get/paid/ads/template/for/compaign/{setting}', 'getPaidAdsCompaignTemplate')->name('get.paid.ads.template.for.compaign');
        Route::get('/promotion/get/new/schedule/For/empty/paid/ads/{campaign_id}/{row}/{main_setting_id}/{setting}/{type}/{category}/{category_ids}/{group_type}/{socials}/{store}/{post_type}/{range}/{budget}/{action?}/{start?}/{end_date?}/{sub_category?}', 'getnewFormissingDayschedulesGroupsForPaidAds')->name('promotion.get.new.schedule.paid.ads');
        Route::get('/search/group/code/{search}/{cate}/{selected_cate}/{group_type}/{type}/{sub_category?}', 'searchGroupCodeForSchedule')->name('search.group.code.for.schedule');
        Route::get('/get/group/for/selected/category/{group_type}/{type}/{cate}/{sub_cate?}', 'getGroupListForSelectedCategory')->name('get.group.list.for.cate');
        Route::get('/get/searched/group/code/id/{group}', 'getSelectedGroupId')->name('get.selected.group.id.for.schedule');
        Route::get('/get/paid/schedule/group/detail/{group}/{posting_type?}', 'scheduleGroupDetail')->name('promotion.paid.schedule.group.detail');
        Route::post('/save/change/schedule', 'saveChangedSchedule')->name('save.change.schedule');
        Route::get('/promotion/get/new/schedule/{main_setting_id}/{setting}/{type}/{category}/{group_type}/{group_code}/{group_id}/{post_id}/{socials}/{date}/{store}/{post_type}/{time?}/{action?}/{sub_category?}', 'getnewschedulesGroups')->name('promotion.get.new.schedule');
        Route::get('/destroy/setting/{setting}', 'destroySetting')->name('destroy.setting');
        Route::get('/get/schedule/group/detail/{group}/{posting_type?}', 'scheduleGroupDetail')->name('promotion.schedule.group.detail');
        Route::get('/promotion/ba/work/{setting}/{store}/{post_type}', 'getBaWorkReports')->name('ba.work');
        Route::get('/promotion/ba/work-history/{setting}/{store}/{post_type}', 'getBaWorkReportsHistory')->name('ba.work.history');
        Route::get('/promotion/ba/paid/ads/work/{setting}/{store}/{post_type}/{action}', 'getPaidWorkReports')->name('ba.paid.ads.work');
        Route::get('/organic/promotion/new/schedule/For/empty/day/{row}/{main_setting_id}/{setting}/{type}/{category}/{category_ids}/{group_type}/{socials}/{date}/{store}/{post_type}/{time?}', 'getOrganicschedulesGroupForNewDays')->name('organic.promotion.get.new.schedule');
        Route::post('/svae/change/schedule', 'saveChangedScheduleOr')->name('svae.change.schedule');
        Route::get('/promotion/dressf/work/{setting}/{store}/{post_type}', 'getBaWorkReports')->name('df.work');
        Route::get('/get/setting/template/form/{setting?}', 'settingTemplateForm')->name('setting.template.form');
        Route::post('/svae/promotion/setting', 'savsSetting')->name('svae.promotion.setting');
       
    });
    
    Route::controller(AttributeController::class)->group(function() {
        Route::any('/attribute', 'attributes')->name('attributes');
        Route::any('/add/attribute', 'addAttribute')->name('add.attribute');
        Route::any('/save/attribute', 'saveAttribute')->name('save.attribute');
        Route::any('/edit/attribute/{attribute}', 'editAttribute')->name('edit.attribute');
        Route::any('/destory/preset', 'destoryPreset')->name('destory.preset');
        Route::post('/update/attribute', 'updateAttribute')->name('update.attribute');
        Route::get('/assign/attributes/form/{group}/{cate}', 'assignAttributeForm')->name('assign.attributes.form');
        Route::get('/fetch/preset/values/{attribute}/{cate}', 'fetchPresetValues')->name('fetch.preset.values');
        Route::post('/save/assigned/attributes', 'saveAssignAttribute')->name('save.assign.attributes');
        Route::any('/destory/attribute', 'destoryAttribute')->name('attribute.destory');
        Route::any('/get/preset/category', 'getPresetCategory')->name('get.preset.category');
        //attribute group routes
        Route::any('/attribute/groups', 'attributeGroups')->name('attribute.groups');
        Route::any('/attribute/groups/add', 'attributeGroupsAdd')->name('attribute.groups.add');
        Route::any('/attribute/groups/save', 'attributeGroupsSave')->name('attribute.groups.save');
        Route::get('/attribute/groups/{row}/edit', 'attributeGroupsEdit')->name('attribute.groups.edit');
        Route::any('/attribute/groups/update', 'attributeGroupsUpdate')->name('attribute.groups.update');
        //attribute templates routes
        Route::any('/attribute/templates', 'attributeTemplates')->name('attribute.templates');
    });
});

Route::prefix('performance')->middleware('auth')->group(function() {
    Route::controller(MarketingPerformanceController::class)->group(function() {
        Route::get('/get/out/stock/paid/ad/detail/{group}', 'getOutStockPaidAdsDetails')->name('out.stock.paid.ads.detail');
        Route::post('/create/paid/ads/campaign', 'CreateCampaign')->name('create.paid.ads.campaign');
        Route::get('/get/group/for/selected/category/for/marketing/{group_type}/{type}/{cate}/{duration?}/{sub_cate?}', 'getGroupListForSelectedCategory')->name('employee.performance.marketing.get.group.list.for.cate');
        Route::get('/activate/single/comming/paid/ads/{main_setting}/{setting}/{duration}/{post}/{capaign}', 'ActiveSinglePaidAd')->name('activate.single.comming.paid.ads');
        Route::get('/stop/single/paid/ads/{post}/{main_setting}/{setting}/{duration}/{campaign}', 'stopSinglePaidAd')->name('stop.single.paid.ad');
        Route::post('/marketing/save/paid/ad/chat/', 'savePaidAdChat')->name('marketing.save.ad.chat');
        Route::post('/save/paid/post/remark/', 'saveRemark')->name('save.paid.post.remark');
        Route::get('/change/status/paid/ad/setting/{setting}/{status}', 'changePaidAdsSettingStatus')->name('change.status.paid.ad.setting');
        Route::any('/employee-performance/operation/commission/report', 'commissionReport')->name('employee-performance.commission.report');
        Route::post('/save/smart/look/custom/duty', 'saveSmartLookCustomDuty')->name('save.smart.look.duties');
        Route::get('/employee-performance/smart/look/form', 'smartLookForm')->name('employee-performance.smart.look.form');
        Route::get('/employee-performance/smart/look', 'smartLook')->name('employee-performance.smart.look');
        Route::get('/employee-performance/marketer/product/listing/{action?}', 'productListing')->name('employee-perfomance.marketer.product.listing');
        Route::get('/new/arrival/product/list/detail/{group_id}', 'detailOfNewArrivalProductList')->name('new.new.arrival.product.list.detail');
        Route::get('/employee-performance/marketer/listing/new/product/{id}/{action}/{button}', 'listingNewArrivalProduct')->name('employee-performance.marketer.listing.new.arrival.product');
    });
    
    Route::controller(StockPerformanceController::class)->group(function() {
        Route::get('stock', 'index')->name('stock.performance');
    });
    Route::controller(SalePerformancaeController::class)->group(function() {
        Route::get('/sale/staff/duty/report', 'index')->name('sale.staff.duty.report');
        Route::any('/sale/save/daily/progress', 'saveDailyProgress')->name('performance.sale.save.daily.progress');
    });

    Route::controller(OperationPerformanceController::class)->group(function() {
        Route::any('/operation/save/conversation', 'saveConversation')->name('performance.operation.save.conversation');
        Route::any('/employee-performance/operation/commission/report', 'commissionReport')->name('employee-performance.commission.report');
    });
    Route::controller(DesignerPerformanceeController::class)->group(function() {
        Route::any('/employee-performance/designer/save-daily-work/{id?}', 'saveDailyWork')->name('employee-performance.designer.save-daily-work');
        // Route::any('/employee-performance/designer/save-daily-work/{id?}', 'saveDailyWork')->name('employee-performance.designer.save-daily-work');
        Route::any('/employee-performance/designer/change-post-status/{id}/{action}/{page?}', 'changePostStatus')->name('employee-performance.designer.changePostStatus');
        Route::get('/employee-performance/designer/new/product/image/{action?}', 'newProductImage')->name('new.designer.product.image');
        Route::get('/view/new/arrival/product/detail/{group}', 'detailOfNewArrivalProduct')->name('detail.of.new.arrival.product');
        Route::get('/employee-performance/designer/design/new/arrival/product/image/{id}', 'designNewArrivalProductImage')->name('design.new.arrival.product.image');
    });
    Route::controller(ItController::class)->group(function() {
        Route::get('/employee-performance/web/developer/smart/look/{user?}/{action?}', 'smartLook')->name('developerweb.smart.look');
        Route::get('/employee-performance/app/developer/smart/look/{user?}/{action?}', 'smartLook')->name('employee-performance.app.developer.smart.look');
        Route::get('/employee-performance/developer/smart/look/form/{user?}/{action?}', 'smartLookForm')->name('employee-performance.itdeveloper.smart.look.form');
        Route::get('/employee-performance/web/developer/R&D/{user?}/{action?}', 'RAndD')->name('webdeveloper.R&D');
        Route::get('/employee-performance/webdeveloper/R&D/{user?}/{action?}', 'RAndD')->name('employee-performance.app.developer.R&D');
        Route::get('/employee-performance/developer/rAndD/form/{user?}/{action?}', 'rAndDForm')->name('employee-performance.itdeveloper.rAndD.form');
        Route::post('/save/rAndD/form', 'saveRAndDRecord')->name('save.rAndD.record');
    });
});

Route::prefix('Settings')->middleware('auth')->group(function() {
    Route::controller(CommissionController::class)->group(function() {
        Route::any('/sale/on-total-delivered-amount', 'saleOnTotalDeliveredAmount')->name('commission.sale.saleOnTotalDeliveredAmount');
        Route::any('/sale/courier-summary', 'courierSummary')->name('commission.sale.courierSummary'); 
        Route::any('/chat/sale/order/report', 'chatSaleOrderReport')->name('chat.sale.order.report');
    });
});

// Route::post('/add/inventory/product', [InventoryManagementController::class, 'addInventoryProduct']);
Route::get('/employee-performance/operation/records/{user_id}/{filter}', [HomeController::class, 'employeeOperationRecords']);
Route::get('/custom/duty/report', [App\Http\Controllers\Settings\CustomDutiesController::class, 'employeeCustomDutiesReport'])->name('custom.duties.report');
Route::get('/designer/custom/duties/{arg}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'employeeCustomDuties'])->name('assigned.custom.duties.designer');
Route::get('/add/irregular/duty/{action}/{duty?}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'irregularDutyForm'])->name('add.irregular.duty.form');
Route::get('/get/irregular/group/users/{user_group}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getGroupUsers'])->name('get.irregular.user.irregular.duties');
Route::get('/get/custom/duty/details/{duty}/{action?}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getCustomDutyDetails'])->name('get.custom.duty.detail');
Route::post('/add/attachment/to/duty', [App\Http\Controllers\Settings\CustomDutiesController::class, 'addAttachment'])->name('add.duty.attachment');
Route::post('/save/duty/comment', [App\Http\Controllers\Settings\CustomDutiesController::class, 'saveComment'])->name('save.duty.comment');
Route::post('/save/attachment/comment', [App\Http\Controllers\Settings\CustomDutiesController::class, 'saveAttachmentComment'])->name('save.duty.comment');
Route::post('/save/comment/reply', [App\Http\Controllers\Settings\CustomDutiesController::class, 'saveCommentReply'])->name('save.duty.comment.reply');
Route::get('/duty/attachment/comments/{file}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getAttachmentComment'])->name('get.attachment.comments');
Route::get('/check/pending/desginer/duties/end/date', [App\Http\Controllers\Landing\LandingController::class, 'checkDesgnerPendingDutiesEndDate'])->name('check.pending.designer.duties.end.date');
Route::get('/admin/request/response/{notif}/{time}/{action}/{request}', [App\Http\Controllers\Landing\LandingController::class, 'adminRequestResponse'])->name('admin.request.response');
Route::get('/count/duty/comment', [App\Http\Controllers\Settings\CustomDutiesController::class, 'countOfDutyComment'])->name('count.duty.comment');
Route::post('/update/comment', [App\Http\Controllers\Settings\CustomDutiesController::class, 'updateComment'])->name('update.duty.comment');
Route::post('/update/comment/reply', [App\Http\Controllers\Settings\CustomDutiesController::class, 'updateCommentReply'])->name('update.duty.comment.reply');
Route::get('/change/duty/active/actin/{duty}/{action}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'changeDutyActiveAction'])->name('change.duty.active.action');
Route::post('/save/duty/description/content', [App\Http\Controllers\Settings\CustomDutiesController::class, 'saveDutyDescription'])->name('save.duty.description');
Route::get('/remove/duty/attachment/{attachment}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'removeAttachment'])->name('remove.duty.attachment');
Route::get('/duty/delete/{duty}/{action}/{redirectUrl}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'destroyCustomDuty'])->name('destroy.custom.duty');
Route::get('/get/user/irregular/duties/{user?}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getUserIrregularDuties'])->name('get.user.irregular.duties');
Route::get('/get/user/irregular/sub/duties/{user?}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getIrregularSubDuties'])->name('get.user.irregular.sub.duties');
Route::post('/save/assigned/custom/duty', [App\Http\Controllers\Settings\CustomDutiesController::class, 'saveAssignCustomDuty'])->name('save.custom.duties');
Route::any('/marketer/custom/duties/{arg?}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'employeeCustomDuties'])->name('update.assign.custom.duties');
Route::post('/changes/duty/status', [App\Http\Controllers\Settings\CustomDutiesController::class, 'changeDutyStatus'])->name('change.duty.status');
Route::get('/web/developer/custom/duties/{arg}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'employeeCustomDuties'])->name('assigned.custom.duties.w_developer');
Route::get('/app/developer/custom/duties/{arg}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'employeeCustomDuties'])->name('assigned.custom.duties.a_developer');
Route::get('/get/group/users/{user_group}', [App\Http\Controllers\Settings\CustomDutiesController::class, 'getGroupUsers'])->name('get.group.users');
Route::get('/admin/approve/request/response/{id}/{status}/{user}/{action}', [App\Http\Controllers\performance\ItController::class, 'adminApproveRequestResponse'])->name('admin.approve.request.response');