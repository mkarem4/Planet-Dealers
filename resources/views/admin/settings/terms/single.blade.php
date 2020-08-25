@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li>{{word('settings')}}</li>
        <li>{{word('terms')}}</li>
        <li class="active">{{word('edit')}}</li>
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" method="post" action="/admin/settings/terms/update">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>
                                    {{word('edit')}}
                                </strong>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('ar_text') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('ar_text')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <textarea class="form-control summernote" name="ar_text" rows="10">{!! $data->ar_text !!}</textarea>
                                    @include('error', ['input' => 'ar_text'])
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('en_text') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_text')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <textarea class="form-control summernote" name="en_text" rows="10">{!! $data->en_text !!}</textarea>
                                    @include('error', ['input' => 'en_text'])
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary pull-right">
                                {{word('update')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
