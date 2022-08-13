<?php

namespace App\Http\Controllers;

use App\Models\OpenCart\Customers\CustomersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $delived_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 25)
                        // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                        ->whereYear('date_added', Carbon::now()->year)
                        ->whereMonth('date_added', Carbon::now()->month)
                        ->count();
        $shipped_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 3)
                        // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                        ->whereYear('date_added', Carbon::now()->year)
                        ->whereMonth('date_added', Carbon::now()->month)
                        ->count();
        $pendding_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 1)
                        // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                        ->whereYear('date_added', Carbon::now()->year)
                        ->whereMonth('date_added', Carbon::now()->month)
                        ->count();
        return view('home')->with(compact('delived_orders','shipped_orders','pendding_orders'));
    }
}
