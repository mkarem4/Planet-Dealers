@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li class="active"><a href="/admin/orders/index">{{word('orders')}}</a></li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
            <div class="col-md-12 index-widget">
                <div class="col-md-2">
                    <div class="widget widget-primary widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-spinner fa-spin"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$pending_count}}</div>
                            <div class="widget-title">{{word('pending_orders')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="widget widget-info widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-check"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$confirmed_count}}</div>
                            <div class="widget-title">{{word('confirmed_orders')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="widget widget-warning widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-truck"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$processing_count}}</div>
                            <div class="widget-title">{{word('processing_orders')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="widget widget-success widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-handshake-o" style="font-size: 45px !important;"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$delivered_count}}</div>
                            <div class="widget-title">{{word('delivered_orders')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="widget widget-danger widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-times"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$canceled_count}}</div>
                            <div class="widget-title">{{word('canceled_orders')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="code" class="form-control" placeholder="{{word('code')}}"
                               value="{{isset($inputs['code']) ? $inputs['code'] : ''}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="user">
                            <option value="all" selected>{{word('all_users')}}</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" {{isset($inputs['user']) && $inputs['user'] == $user->id ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="pending" {{isset($inputs['status']) && $inputs['status'] == 'pending' ? 'selected' : ''}}>{{word('pending')}}</option>
                            <option value="confirmed" {{isset($inputs['status']) && $inputs['status'] == 'confirmed' ? 'selected' : ''}}>{{word('confirmed')}}</option>
                            <option value="processing" {{isset($inputs['status']) && $inputs['processing'] == 'active' ? 'selected' : ''}}>{{word('processing')}}</option>
                            <option value="delivered" {{isset($inputs['status']) && $inputs['status'] == 'delivered' ? 'selected' : ''}}>{{word('delivered')}}</option>
                            <option value="canceled" {{isset($inputs['status']) && $inputs['status'] == 'canceled' ? 'selected' : ''}}>{{word('canceled')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row index-widget mt-2">
                <div class="form-group" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}}">
                    <a>
                        <button type="submit" class="btn btn-info">{{word('filter')}}</button>
                    </a>
                    <a href="?" class="btn btn-danger">{{word('clear')}}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="page-content-wrap" style="padding-top: 5px;">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th"><span class="fa fa-info-circle"></span></th>
                                    <th class="rtl_th">{{word('code')}}</th>
                                    <th class="rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('seller')}}</th>
                                    <th class="rtl_th">{{word('buyer')}}</th>
                                    <th class="rtl_th">{{word('items_count')}}</th>
                                    <th class="rtl_th">{{word('total')}}</th>
                                    <th class="rtl_th">{{word('date')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$order->code}}</td>
                                        <td>
                                            <span class="label label-form label-{{$order->get_status_color($order->status)}}">{{word($order->status)}}</span>
                                        </td>
                                        <td>
                                            <span class="label label-form label-info">{{$order->seller->name}}</span><br/>
                                            <a href="mailTo:{{$order->seller->email}}">{{$order->seller->email}}</a><br/>
                                            <a href="tel:{{$order->seller->phone}}">{{$order->seller->phone}}</a>
                                            @if(isset($seller->whatsapp))<a
                                                    href="whatsapp:{{$product->seller->whatsapp}}">{{$product->seller->whatsapp}}</a>@endif
                                        </td>
                                        <td>
                                            <span class="label label-form label-success">{{$order->buyer->name}}</span><br/>
                                            <a href="mailTo:{{$order->buyer->email}}">{{$order->buyer->email}}</a><br/>
                                            <a href="tel:{{$order->buyer->phone}}">{{$order->buyer->phone}}</a>
                                        </td>
                                        <td><span class="label label-info label-form">{{$order->items_count}}</span>
                                        </td>
                                        <td>
                                            <span class="label label-success label-form">{{$order->total_fee}} {{$order->country->currency}}</span>
                                        </td>
                                        <td>
                                            <span class="label label-primary label-form">{{$order->created_at->toTimeString()}}</span><br/>
                                            <span class="label label-default label-form">{{$order->created_at->toDateString()}}</span>
                                        </td>
                                        <td>
                                            <a href="/admin/order/{{$order->id}}/details">
                                                <button class="btn btn-condensed btn-info" title="{{word('details')}}">
                                                    <i class="fa fa-eye"></i></button>
                                            </a>
                                            @if($order->status != 'canceled')
                                                <button class="btn btn-danger btn-condensed mb-control"
                                                        onclick="modal_destroy({{$order->id}})"
                                                        data-box="#message-box-danger" title="{{word('cancel')}}"><i
                                                            class="fa fa-times-circle"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">{{word('sorry_no_data')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                                {!! $orders->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- danger with sound -->
    <div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="message-box-danger">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-trash"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('cancel_order')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/order/cancel" class="buttons form-horizontal text-{{lang()}}">
                        {{csrf_field()}}
                        <div class="form-group {{ $errors->has('admin_notes') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{word('notes')}}</label>
                            <div class="col-md-6 col-xs-12">
                                <input type="text" class="form-control" name="admin_notes"
                                       placeholder="{{word('write_notes')}}"/>
                                @include('error', ['input' => 'admin_notes'])
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_destroy" value="">
                        <button class="btn btn-danger btn-lg pull-right">{{word('cancel')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close"
                            style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end danger with sound -->
    @if($errors->has('id') || $errors->has('admin_notes'))
        <script>
            $('#message-box-danger').toggle();
        </script>
    @endif
@endsection
