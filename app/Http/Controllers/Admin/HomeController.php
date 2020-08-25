<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTransfer;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class HomeController extends Controller
{
    public function index()
    {
        $merchants = User::select('status','type')->get();
        $products = Product::select('status')->get();
        $orders = Order::select('status')->get();
        $transfers = BankTransfer::select('status')->get();

        $top_products = Product::orderBy('sold','desc')->select('id','seller_id',lang().'_name as name','sold')->take(5)->get();
        foreach($top_products as $product) $product['seller'] = User::where('id',$product->seller_id)->select('first_name','last_name')->first();


        return view('admin.dashboard',get_defined_vars());
    }


    public function days_orders_graph()
    {
        $this_date = date('Y-m');
        $past_date = date("Y-m",strtotime("-1 month"));

        $count = Carbon::now()->daysInMonth;
        $days = [];

        for($i = 1; $i < $count; $i++)
        {
            $days[] = $i;
        }

        $data = new \Illuminate\Support\Collection();

        foreach($days as $day)
        {
            if($day < 10) $this_day = '0'.$day;
            else $this_day = $day;

            $this_month = User::whereDate('created_at',$this_date.'-'.$this_day)->count();
            $past_month = User::whereDate('created_at',$past_date.'-'.$this_day)->count();

            $data[] = collect(['x' => $this_date.'-'.$this_day, 'y' => $this_month,'z' => $past_month]);
        }

        return response()->json($data);
    }


    public function month_orders_graph()
    {
        $this_year = date('Y');
        $past_year = date('Y',strtotime("-1 year"));
        $months = ['0','1','2','3','4','5','6','7','8','9','10','11','12'];

        $data = new \Illuminate\Support\Collection();

        foreach($months as $month)
        {
            if($month == '0') continue;

            $this_count = Order::whereDate('created_at','>=',$this_year.'-'.$month.'-01')->whereDate('created_at','<=',$this_year.'-'.$month.'-31')->count();
            $past_count = Order::whereDate('created_at','>=',$past_year.'-'.$month.'-01')->whereDate('created_at','<=',$past_year.'-'.$month.'-31')->count();

            $data[] = collect(['x' => $this_year.'-'.$month, 'y' => $this_count,'z' => $past_count]);
        }

        return response()->json($data);
    }
}
