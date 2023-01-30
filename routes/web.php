<?php

use App\Http\Controllers\Accounts\ReceiptController;
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
use App\Http\Controllers\PurchaseManagement\PurchaseManagementAjaxController;
use App\Http\Controllers\PurchaseManagement\PurchaseManagementController;
use App\Http\Controllers\rolepermision\RolePermissionController;
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
        // Route::post('/accounts/save-pending-receipts', 'savePendingReciepts')->name('accounts.save.pending.receipts');
        // Route::post('/accounts/receive-pending-receipts', 'receivePendingReciepts')->name('receive.pending.receipts');
        // Route::post('/accounts/update-shipping-payment', 'updateShippingPayment')->name('accounts.update.payment');
        // Route::post('/pending/receipt/process-ex-file', 'processPendingExReceiptFile')->name('orders.process-delivered-orders-file');
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

// Route::post('/add/inventory/product', [InventoryManagementController::class, 'addInventoryProduct']);
Route::get('/employee-performance/operation/records/{user_id}/{filter}', [HomeController::class, 'employeeOperationRecords']);
