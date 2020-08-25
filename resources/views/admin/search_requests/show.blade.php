@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li><a href="/admin/saerch_requests/index">{{word('request')}}</a></li>
        <li class="active">
            {{word('details')}}
        </li>
    </ul>
    <div class="page-content-wrap" style="direction : {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
        <div class="row">
            <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>
                                    {{word('details')}}
                                </strong>
                            </h3>
                        </div>
                        <div class="panel-body form-horizontal">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$search->name}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('email')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$search->email}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('phone')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$search->phone}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('address')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$search->address}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('text')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-label">{{$search->text}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('attachments')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    @forelse($search->attachments as $file)
                                        <a href="{{asset('/uploads/search_requests/'.$file)}}" target="_blank"><span class="label label-info label-form">{{asset('uploads/search_requests/'.$file)}}</span></a><br/>
                                    @empty
                                        <span class="label label-form label-default">{{word('none')}}</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('date')}}</label>
                                <div class="col-md-6 col-xs-12">
{{--                                    <label class="form-control">--}}
                                        <span class="label label-primary label-form">{{$search->created_at->toTimeString()}}</span><br/>
                                        <span class="label label-default label-form">{{$search->created_at->toDateString()}}</span>
{{--                                    </label>--}}
                                </div>
                            </div>
                        </div>

                    </div>

            </div>
        </div>
    </div>
@endsection
