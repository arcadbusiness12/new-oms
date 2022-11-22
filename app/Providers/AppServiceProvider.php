<?php

namespace App\Providers;

use App\Models\Oms\storeModel;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $store_share_data = storeModel::where('place_order_in_oms',1)->get();
        view()->share('store_share_data', $store_share_data);
    }
}
