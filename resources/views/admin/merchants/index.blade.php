@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li class="active"><a href="/admin/merchants/index">{{word('merchants')}}</a></li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
            <div class="col-md-12 index-widget">
                <div class="col-md-3">
                    <div class="widget widget-info widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-cog fa-spin"></span>
                        </div>
                        <div class="widget-data" >
                            <div class="widget-int num-count">{{$pending_count}}</div>
                            <div class="widget-title">{{word('pending_sellers')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-success widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-check"></span>
                        </div>
                        <div class="widget-data" >
                            <div class="widget-int num-count">{{$active_count->count()}}</div>
                            <div class="widget-title">{{word('active_merchants')}}</div>
                            <div class="widget-title">{{$active_count->where('type','seller')->count().' '.word('sellers')}}</div>
                            <div class="widget-title">{{$active_count->where('type','buyer')->count().' '.word('buyers')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-primary widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-minus"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$suspended_count->count()}}</div>
                            <div class="widget-title">{{word('suspended_merchants')}}</div>
                            <div class="widget-title">{{$suspended_count->where('type','seller')->count().' '.word('sellers')}}</div>
                            <div class="widget-title">{{$suspended_count->where('type','buyer')->count().' '.word('buyers')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-danger widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-trash"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$deleted_count}}</div>
                            <div class="widget-title">{{word('deleted_merchants')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="keyword" class="form-control" placeholder="{{word('name_email_phone')}}" value="{{isset($inputs['keyword']) ? $inputs['keyword'] : ''}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="pending" {{isset($inputs['status']) && $inputs['status'] == 'pending' ? 'selected' : ''}}>{{word('pending')}}</option>
                            <option value="active" {{isset($inputs['status']) && $inputs['status'] == 'active' ? 'selected' : ''}}>{{word('active')}}</option>
                            <option value="suspended" {{isset($inputs['status']) && $inputs['status'] == 'suspended' ? 'selected' : ''}}>{{word('suspended')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="featured">
                            <option value="all" selected>{{word('all_types')}}</option>
                            <option value="seller" {{isset($inputs['type']) && $inputs['type'] == 'seller' ? 'selected' : ''}}>{{word('seller')}}</option>
                            <option value="buyer" {{isset($inputs['type']) && $inputs['type'] == 'buyer' ? 'selected' : ''}}>{{word('buyer')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="featured">
                            <option value="all" selected>{{word('all_merchants')}}</option>
                            <option value="1" {{isset($inputs['featured']) && $inputs['featured'] == '1' ? 'selected' : ''}}>{{word('featured')}}</option>
                            <option value="0" {{isset($inputs['featured']) && $inputs['featured'] == '0' ? 'selected' : ''}}>{{word('not_featured')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row index-widget mt-2">
                <div class="form-group" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}}">
                    <a><button type="submit" class="btn btn-info">{{word('filter')}}</button></a>
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
                    <div class="panel-heading heading-{{lang()}}">
                        <a href="/admin/merchants/export">
                            <button type="button" class="btn btn-success">{{word('export_excel')}}</button>
                        </a>
                        <a href="/admin/merchant/create">
                            <button type="button" class="btn btn-info">{{word('create_merchant')}}</button>
                        </a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('name')}}</th>
                                    <th class="'rtl_th">{{word('type')}}</th>
                                    <th class="'rtl_th">{{word('pack')}}</th>
                                    <th class="'rtl_th">{{word('the_city')}}</th>
                                    <th class="'rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($merchants as $merchant)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <span class="label label-form label-info" title="{{word('name')}}">{{$merchant->name}}</span><br/>
                                            <a href="mailTo:{{$merchant->email}}" title="{{word('email')}}">{{$merchant->email}}</a><br/>
                                            <a href="tel:{{$merchant->phone}}" title="{{word('phone')}}">{{$merchant->phone}}</a><br/>
                                            @if($merchant->type == 'seller')
                                                <span class="label label-form label-success" title="{{word('company_name')}}">{{$merchant->company_name}}</span><br/>
                                            @endif
                                            <span class="label label-form label-primary" title="{{word('date')}}">{{$merchant->created_at->toDateString()}}</span>
                                        </td>
                                        <td>
                                            <span class="label @if($merchant->type == 'seller') label-success @else label-info @endif label-form">{{word($merchant->type)}}</span><br/>
                                            @if($merchant->featured)
                                                <span class="label label-warning label-form">{{word('featured_till') . ' ' . $merchant->featured_till}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($merchant->type == 'seller')
                                                @if($merchant->pack_id)
                                                    <span class="label label-success label-form">{{$merchant->pack->name .' - '.word('till') . ' ' . $merchant->expire_at}}</span>
                                                @else
                                                    <span class="label label-primary label-form">{{word('not_yet')}}</span>
                                                @endif
                                            @else
                                                <span class="label label-primary label-form">-</span>
                                            @endif
                                        </td>
                                        <td><span class="label label-primary label-form">{{$merchant->city->name}}</span></td>
                                        <td>
                                            @if($merchant->type == 'seller' && $merchant->getOriginal('pack_id'))
                                                <span class="label label-form label-success">{{word('active')}}</span>
                                            @elseif($merchant->type == 'seller' && !$merchant->getOriginal('pack_id'))
                                                <span class="label label-form label-info">{{word('under_sub')}}</span>
                                            @else
                                                <span class="label label-form label-{{$merchant->status == 'active' ? 'success' : 'primary'}}">{{word($merchant->status)}}</span>
                                            @endif
                                        <td>
                                            <a href="/admin/merchant/{{$merchant->id}}/edit"><button class="btn btn-condensed btn-warning" title="{{word('edit')}}"><i class="fa fa-edit"></i></button></a>
                                            @if($merchant->status == 'active')
                                                <button class="btn btn-primary btn-condensed mb-control" onclick="modal_suspend({{$merchant->id}})" data-box="#message-box-primary" title="{{word('suspend')}}"><i class="fa fa-minus-circle"></i></button>
                                            @else
                                                <button class="btn btn-success btn-condensed mb-control" onclick="modal_activate({{$merchant->id}})" data-box="#message-box-success" title="{{word('activate')}}"><i class="fa fa-check-square"></i></button>
                                            @endif

                                            <button class="btn btn-danger btn-condensed mb-control" onclick="modal_destroy({{$merchant->id}})" data-box="#message-box-danger" title="{{word('delete')}}"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">{{word('sorry_no_data')}}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                                {!! $merchants->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- success with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert" id="message-box-success">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-check-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('activate_merchant')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/merchant/change_status" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_activate" value="">
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-success btn-lg pull-right">{{word('activate')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end success with sound -->

    <!-- warning with sound -->
    <div class="message-box message-box-primary animated fadeIn" data-sound="alert" id="message-box-primary">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-minus-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('suspend_merchant')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/merchant/change_status" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_suspend" value="">
                        <input type="hidden" name="status" value="suspended">
                        <button class="btn btn-primary btn-lg pull-right">{{word('suspend')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end warning with sound -->

    <!-- danger with sound -->
    <div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="message-box-danger">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-trash"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('delete_merchant')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/merchant/delete" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_destroy" value="">
                        <button class="btn btn-danger btn-lg pull-right">{{word('delete')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end danger with sound -->
@endsection
