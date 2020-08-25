@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('orders')}}</nav><!-- .woocommerce-breadcrumb -->
            <div class="container account-details">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                            <li class="title">{{word('settings')}}</li>
                            <li class="nav-item">
                                <a class="nav-link " href="/profile">{{word('personal_info')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/profile/orders">{{word('orders')}}</a>
                            </li>
                            @if(user()->type == 'seller')
                                <li class="nav-item">
                                    <a class="nav-link " href="/profile/products"  aria-selected="false">{{word('my_products')}}</a>
                                </li>
                            @endif
                            @if(user()->type == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link" href="/profile/addresses"  aria-selected="false">{{word('my_addresses')}}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active in"  role="tabpanel" aria-labelledby="contact-tab">
                                <div class="profile-card">
                                    <form class="woocommerce-ordering" method="get">
                                        <div style="float : {{lang() == 'ar' ? 'right' : 'left'}}; margin: 8px 5px 8px 5px;">
                                            <select name="status" class="orderby form-submit" onchange="$('.woocommerce-ordering').submit()">
                                                <option value="all" {{isset($inputs['status']) && $inputs['status'] == 'all' ? 'selected' : ''}}>{{word('all')}}</option>
                                                <option value="pending" {{isset($inputs['status']) && $inputs['status'] == 'pending' ? 'selected' : ''}}>{{word('pending')}}</option>
                                                <option value="confirmed" {{isset($inputs['status']) && $inputs['status'] == 'confirmed' ? 'selected' : ''}}>{{word('confirmed')}}</option>
                                                <option value="processing" {{isset($inputs['status']) && $inputs['status'] == 'processing' ? 'selected' : ''}}>{{word('processing')}}</option>
                                                <option value="delivered" {{isset($inputs['status']) && $inputs['status'] == 'delivered' ? 'selected' : ''}}>{{word('delivered')}}</option>
                                                <option value="canceled" {{isset($inputs['status']) && $inputs['status'] == 'canceled' ? 'selected' : ''}}>{{word('canceled')}}</option>
                                            </select>
                                        </div>
                                    </form>
                                    <h5 style="margin-bottom:0;">{{word('your_orders')}} ( {{$orders_count}} )</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{word('status')}}</th>
                                                <th scope="col">{{word(user()->type == 'seller' ? 'the_buyer' : 'the_seller')}}</th>
                                                <th scope="col">{{word('items_count')}}</th>
                                                <th scope="col">{{word('total_fee')}}</th>
                                                <th scope="col">{{word('actions')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($orders as $order)
                                                <tr>
                                                    <th scope="row">{{$order->code}}</th>
                                                    <td>{{$order->status}}</td>
                                                    <td>{{$order->seller_id == user()->id ? $order->buyer->name : $order->seller->name}}</td>
                                                    <td>{{$order->items_count}}</td>
                                                    <td>{{$order->total_fee}} {{currency()}}</td>
                                                    <td>
                                                        <a href="/profile/order/{{$order->id}}/details"><button class="btn btn-condensed btn-warning" title="{{word('details')}}"><i class="fa fa-eye"></i></button></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div class="pagination-{{lang()}}">
                                            {{$orders->links('vendor.pagination.bootstrap-4')}}<br/><br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

