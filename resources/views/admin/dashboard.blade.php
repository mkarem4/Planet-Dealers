@extends('admin.layout')
@section('content')
    @include('message')
    <div class="page-content-wrap" style="margin-top: 10px;">
        <div class="row widget-custom">
            <div class="col-md-3 cursor">
                <div class="widget widget-info widget-item-icon" onclick="location.href='/admin/merchants/index';">
                    <div class="widget-item-left">
                        <span class="fa fa-users fa-small"></span>
                    </div>
                    <div class="widget-data">
                        <div class="widget-int num-count">{{$merchants->count()}}</div>
                        <div class="widget-title">{{word('merchants')}}</div>
                        <div class="widget-subtitle">{{$merchants->where('status','suspended')->count()}} {{word('suspended')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 cursor">
                <div class="widget widget-info widget-item-icon" onclick="location.href='/admin/products/index';">
                    <div class="widget-item-left">
                        <span class="fa fa-cubes fa-small"></span>
                    </div>
                    <div class="widget-data">
                        <div class="widget-int num-count">{{$products->count()}}</div>
                        <div class="widget-title">{{word('products')}}</div>
                        <div class="widget-subtitle">{{$products->where('status','suspended')->count()}} {{word('suspended')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 cursor">
                <div class="widget widget-info widget-item-icon" onclick="location.href='/admin/orders/index';">
                    <div class="widget-item-left">
                        <span class="fa fa-shopping-cart fa-small"></span>
                    </div>
                    <div class="widget-data">
                        <div class="widget-int num-count">{{$orders->count()}}</div>
                        <div class="widget-title">{{word('orders')}}</div>
                        <div class="widget-subtitle">{{$orders->where('status','canceled')->count()}} {{word('canceled')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 cursor">
                <div class="widget widget-info widget-item-icon" onclick="location.href='/admin/transfers/index';">
                    <div class="widget-item-left">
                        <span class="fa fa-money fa-small"></span>
                    </div>
                    <div class="widget-data">
                        <div class="widget-int num-count">{{$transfers->count()}}</div>
                        <div class="widget-title">{{word('bank_transfers')}}</div>
                        <div class="widget-subtitle">{{$transfers->where('status','pending')->count()}} {{word('_pending')}}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>{{word('products')}}</h3>
                            <span>{{word('most_purchased')}}</span>
                        </div>
                    </div>
                    <div class="panel-body panel-body-table" style="height: 270px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th width="50%">{{word('item')}}</th>
                                    <th width="25">{{word('merchant')}}</th>
                                    <th width="25%">{{word('purchases_count')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($top_products as $product)
                                    <tr>
                                        <td><a href="/product/{{$product->id}}/details">{{$product->name}}</a></td>
                                        <td><span class="label label-info label-form">{{$product->seller->name}}</span></td>
                                        <td><span class="label label-success label-form">{{$product->sold}}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>{{word('merchants')}}</h3>
                            <span>{{word('sellers_buyers')}}</span>
                        </div>
                    </div>
                    <div class="panel-body padding-0">
                        <div class="chart-holder" id="users-donut" style="height: 270px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>{{word('orders')}}</h3>
                            <span>{{word('daily')}}</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="day-orders-count" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>{{word('orders')}}</h3>
                            <span>{{word('monthly')}}</span>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="month-orders-count" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var donut_colors = ["#0074D9","#B70004","#33414E","#FF4136","#2ECC40","#840003","#FF851B","#358E33","#7FDBFF","#B10DC9","#FFDC00","#001f3f","#39CCCC","#01FF70","#85144b","#F012BE","#3D9970","#111111","#921880"];
        var users_donut = [];

        users_donut.push({label: '{{word('sellers')}}', value: '{{$merchants->where('type','seller')->count()}}'});
        users_donut.push({label: '{{word('buyers')}}', value: '{{$merchants->where('type','buyer')->count()}}'});

        Morris.Donut({
            element: 'users-donut',
            data: users_donut,
            colors: donut_colors,
            resize: false
        });

        var morrisCharts = function()
        {
            var days_data;
            $.ajax(
                {
                    async : false,
                    url : '/admin/days_orders_graph',
                    method : 'get',
                    dataType : 'json',
                    success : function(data)
                    {
                        days_data = data;
                    },
                    error : function()
                    {
                        console.log('day graphs ajax error')
                    },
                }
            );

            Morris.Line({
                element: 'day-orders-count',
                data: days_data,
                xkey: 'x',
                ykeys: ['y','z'],
                labels: ['{{word('this_day')}}','{{word('past_month')}}'],
                resize: false,
                lineColors: ["#0074D9","#B70004"]
            });

            var months_data;
            $.ajax(
                {
                    async : false,
                    url : '/admin/month_orders_graph',
                    method : 'get',
                    dataType : 'json',
                    success : function(data)
                    {
                        months_data = data;
                    },
                    error : function()
                    {
                        console.log('month graphs ajax error')
                    },
                }
            );

            Morris.Line({
                element: 'month-orders-count',
                data: months_data,
                xkey: 'x',
                ykeys: ['y','z'],
                labels: ['{{word('this_month')}}','{{word('past_month')}}'],
                resize: false,
                lineColors: ["#0074D9","#B70004"]
            });
        }();
    </script>
@endsection
