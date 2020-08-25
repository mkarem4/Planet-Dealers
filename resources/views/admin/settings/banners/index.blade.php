@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li>{{word('settings')}}</li>
        <li class="active">{{word('banners')}}</li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        @if(! isset($parent))
            <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-12 index-widget">
                    <div class="col-md-3">
                        <div class="widget widget-success widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-check"></span>
                            </div>
                            <div class="widget-data" >
                                <div class="widget-int num-count">{{$active_count}}</div>
                                <div class="widget-title">{{word('active_banners')}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-primary widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-minus"></span>
                            </div>
                            <div class="widget-data">
                                <div class="widget-int num-count">{{$suspended_count}}</div>
                                <div class="widget-title">{{word('suspended_banners')}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-danger widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-minus"></span>
                            </div>
                            <div class="widget-data">
                                <div class="widget-int num-count">{{$expired_count}}</div>
                                <div class="widget-title">{{word('expired_banners')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="{{word('url')}}" value="{{isset($inputs['url']) ? $inputs['url'] : ''}}" name="url">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="active" {{isset($inputs['status']) && $inputs['status'] == 'active' ? 'selected' : ''}}>{{word('active')}}</option>
                            <option value="suspended" {{isset($inputs['status']) && $inputs['status'] == 'suspended' ? 'selected' : ''}}>{{word('suspended')}}</option>
                            <option value="expired" {{isset($inputs['status']) && $inputs['status'] == 'expired' ? 'selected' : ''}}>{{word('expired')}}</option>
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
                        <a href="/admin/settings/banner/create">
                            <button type="button" class="btn btn-info">{{word('create_banner')}}</button>
                        </a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('url')}}</th>
                                    <th class="rtl_th">{{word('image')}}</th>
                                    <th class="rtl_th">{{word('expire_at')}}</th>
                                    <th class="'rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($banners as $banner)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td><span class="label label-form label-info">{{$banner->url}}</span><br/></td>
                                        <td>
                                            <div class="gallery">
                                                <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}" href="{{$banner->image}}" title="{{$banner->image}}" data-gallery style="padding : 10px 0px 0px 0px;">
                                                    <div class="image">
                                                        <img src="{{$banner->image}}" alt="{{$banner->image}}" style="width: 400px; height: 90px;"/>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td><span class="label {{$banner->expire_at >= \Carbon\Carbon::today()->toDateString() ? 'label-success' : 'label-danger'}} label-form">{{$banner->expire_at}}</span></td>
                                        <td><span class="label {{$banner->status == 'active' ? 'label-success' : 'label-primary'}} label-form">{{word($banner->status)}}</span></td>
                                        <td>
                                            <a href="/admin/settings/banner/{{$banner->id}}/edit"><button class="btn btn-condensed btn-warning" title="{{word('edit')}}"><i class="fa fa-edit"></i></button></a>
                                            @if($banner->status == 'active')
                                                <button class="btn btn-primary btn-condensed mb-control" onclick="modal_suspend({{$banner->id}})" data-box="#message-box-primary" title="{{word('suspend')}}"><i class="fa fa-minus-circle"></i></button>
                                            @else
                                                <button class="btn btn-success btn-condensed mb-control" onclick="modal_activate({{$banner->id}})" data-box="#message-box-success" title="{{word('activate')}}"><i class="fa fa-check-square"></i></button>
                                            @endif
                                            <button class="btn btn-danger btn-condensed mb-control" onclick="modal_destroy({{$banner->id}})" data-box="#message-box-danger" title="{{word('delete')}}"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">{{word('sorry_no_data')}}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                               {!! $banners->links() !!}
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
                        <p>{{word('activate_banner')}}</p>
                    </div>
                    <div class="mb-footer buttons">
                        <form method="post" action="/admin/settings/banner/change_status" class="buttons">
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
                    <div class="mb-title"><span class="fa fa-minus-circle"></span>{{word('alert')}}</div>
                    <div class="mb-content">
                        <p>{{word('suspend_banner')}}</p>
                    </div>
                    <div class="mb-footer">
                        <form method="post" action="/admin/settings/banner/change_status" class="buttons">
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
                    <p>{{word('delete_banner')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/settings/banner/delete" class="buttons">
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
