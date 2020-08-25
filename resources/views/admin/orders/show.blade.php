@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li>{{word('orders')}}</li>
        <li>{{$order->code}}</li>
        <li class="active">
            {{word('details')}}
        </li>
    </ul>
    <div class="page-content-wrap" style="direction : {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2>#<strong>{{$order->code}}</strong></h2>
                        <div class="invoice">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="invoice-address">
                                        <h5>{{word('buyer')}}</h5>
                                        <h3>{{$buyer->name}}</h3>
                                        <h3>{{word('address')}} : {{$buyer->address->text}}</h3>
                                        <h4><a href="mailTo:{{$buyer->email}}">{{$buyer->email}}</a></h4>
                                        <h4><a href="tel:{{$buyer->phone}}">{{$buyer->phone}}</a></h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="invoice-address">
                                        <h5>{{word('seller')}}</h5>
                                        <h3>{{$seller->name}}</h3>
                                        <h4><a href="mailTo:{{$seller->email}}">{{$seller->email}}</a></h4>
                                        <h4><a href="tel:{{$seller->phone}}">{{$seller->phone}}</a></h4>
                                        @if(isset($seller->whatsapp))<h4><a
                                                    href="whastapp:{{$seller->whatsapp}}">{{$seller->whatsapp}}</a>
                                        </h4> @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="invoice-address">
                                        <h5>{{word('invoice')}}</h5>
                                        <table class="table table-striped">
                                            <tr>
                                                <td width="200">{{word('code')}} :</td>
                                                <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}">{{$order->code}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{word('date')}} :</td>
                                                <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}">{{$order->created_at->toDateString()}}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>{{word('total')}} :</strong></td>
                                                <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}">
                                                    <strong>{{$order->total_fee}} {{$order->country->currency}}</strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="table-invoice">
                                <table class="table">
                                    <tr>
                                        <th>{{word('image')}}</th>
                                        <th>{{word('item')}}</th>
                                        <th>{{word('_count')}}</th>
                                        <th>{{word('total')}}</th>
                                    </tr>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td><img src="{{$item->image}}" class="product-table-image"/></td>
                                            <td>{{$item->name}}</td>
                                            <td>{{$item->pricing->count}}</td>
                                            <td>{{$item->pricing->price}} {{$order->country->currency}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>{{word('order_total')}}</h4>
                                    <table class="table table-striped">
                                        <tr>
                                            <td width="200"><strong>{{word('items_fee')}}:</strong></td>
                                            <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}"><span
                                                        class="currency-{{lang()}}">{{$order->country->currency}}</span>{{$order->items_fee}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{word('tax_fee')}}:</strong></td>
                                            <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}"><span
                                                        class="currency-{{lang()}}">{{$order->country->currency}}</span>{{$order->tax_fee}}
                                            </td>
                                        </tr>
                                        <tr>
                                        </tr>
                                        <tr class="total">
                                            <td>{{word('total_fee')}}:</td>
                                            <td class="text-{{lang() == 'ar' ? 'left' : 'right'}}"><span
                                                        class="currency-{{lang()}}">{{$order->country->currency}}</span>{{$order->total_fee}}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
