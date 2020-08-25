@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li><a href="/admin/contacts/index">{{word('contact_us')}}</a></li>
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
                                    <label class="form-control">{{$contact->name}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('email')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$contact->email}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('phone')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$contact->phone}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('text')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-label">{{$contact->text}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-12 control-label">{{word('date')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="form-control">{{$contact->created_at}}</label>
                                </div>
                            </div>
                        </div>

                    </div>

            </div>
        </div>
    </div>
@endsection
