<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\omsSetting\categorySettingController;
use App\Http\Controllers\inventoryManagement\InventoryManagementController;
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
        Route::get('/inventory/product/history/{id}', 'inventoryProductHistory')->name('inventory.product.history');
        Route::any('/inventory/edit/location/{id}', 'inventoryEditProductLocation')->name('inventory.edit.product.location');
        Route::any('/inventory/edit/product/{id}', 'EditInventoryProduct')->name('edit.inventory.product');
    });
});
// Route::post('/add/inventory/product', [InventoryManagementController::class, 'addInventoryProduct']);
Route::get('/employee-performance/operation/records/{user_id}/{filter}', [HomeController::class, 'employeeOperationRecords']);
