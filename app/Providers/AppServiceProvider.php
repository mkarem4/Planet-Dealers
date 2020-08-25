<?php

namespace App\Providers;

use App\Http\Middleware\Web;
use App\Models\About;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Country;
use App\Models\Notification;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
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

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.default');

        if(user())
        {
            Session::put('country_id',user()->country_id);
            Session::put('city_id',user()->city_id);
        }
        else
        {
            Session::put('country_id',1);
            Session::put('city_id',2);

            $currency = Country::where('id',1)->select('ar_currency','en_currency')->first();
            Session::put('currency',lang() == 'ar' ? $currency->ar_currency : $currency->en_currency);
        }


        $all_countries = Country::where('status','active')->where('type','main')->select('id','ar_name','en_name','code')->get();
        $all_cats = Category::where('type','main')->where('status','active')->select('id','ar_name','en_name')->get();

        $info = About::select('android_link','ios_link')->first();

        View::share
        (
            [
                'all_countries' => $all_countries,
                'all_cats' => $all_cats,
                'info' => $info,
            ]
        );
    }
}
