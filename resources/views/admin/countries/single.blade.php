@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/countries/index">{{word('countries')}}</a></li>
        @if(isset($edit))
            <li>{{lang() == 'ar' ? $edit->ar_name : $edit->en_name}}</li>
            <li class="active">{{word('edit')}}</li>
        @else
            <li>{{word('country')}}</li>
            <li class="active">{{word('create')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/country/update" @else action="/admin/country/store" @endif>
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>
                                    @if(isset($edit))
                                        {{word('edit')}}
                                    @else
                                        {{word('create')}}
                                    @endif
                                </strong>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('ar_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="ar_name" @if(isset($edit)) value="{{$edit->ar_name}}" @else value="{{old('ar_name')}}" @endif/>
                                    @include('error', ['input' => 'ar_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('ar_currency') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('ar_currency')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="ar_currency" @if(isset($edit)) value="{{$edit->ar_currency}}" @else value="{{old('ar_currency')}}" @endif/>
                                    @include('error', ['input' => 'ar_currency'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="en_name" @if(isset($edit)) value="{{$edit->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                    @include('error', ['input' => 'en_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('en_currency') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_currency')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="en_currency" @if(isset($edit)) value="{{$edit->en_currency}}" @else value="{{old('en_currency')}}" @endif/>
                                    @include('error', ['input' => 'en_currency'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('tax_percentage') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('tax_percentage')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="number" min="0" step="0.1" class="form-control" name="tax_percentage" @if(isset($edit)) value="{{$edit->tax_percentage}}" @else value="{{old('tax_percentage')}}" @endif/>
                                    @include('error', ['input' => 'tax_percentage'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('code') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('phone_key')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="number" class="form-control" name="code" @if(isset($edit)) value="{{$edit->code}}" @else value="{{old('code')}}" @endif/>
                                    @include('error', ['input' => 'code'])
                                </div>
                            </div>
                            @if(! isset($edit))
                                <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{word('do_activate')}}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <label class="switch">
                                            <input type="checkbox" name="status" value="active">
                                            <span></span>
                                        </label>
                                        @include('error', ['input' => 'status'])
                                    </div>
                                </div>
                            @endif
                            @if(isset($edit))
                                <input type="hidden" name="id" value="{{$edit->id}}">
                            @endif
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary pull-right">
                                @if(isset($edit))
                                    {{word('update')}}
                                @else
                                    {{word('create')}}
                                @endif
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>
        var jvalidate = $("#jvalidate").validate({
            ignore: [],
            rules: {
                'ar_name': {
                    required: true,
                },
                'en_name': {
                    required: true,
                },
                'ar_currency': {
                    required: true,
                },
                'en_currency': {
                    required: true,
                },
                'tax_percentage': {
                    required: true,
                }
            },
        });
    </script>
@endsection
