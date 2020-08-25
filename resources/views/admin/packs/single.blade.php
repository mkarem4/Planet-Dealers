@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/packs/index">{{word('packs')}}</a></li>
        @if(isset($edit))
            <li>{{$edit->name}}</li>
            <li class="active">{{word('edit')}}</li>
        @else
            <li class="active">{{word('create')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/pack/update" @else action="/admin/pack/store" @endif enctype="multipart/form-data">
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
                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="en_name" @if(isset($edit)) value="{{$edit->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                    @include('error', ['input' => 'en_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('price')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="number" min="0" class="form-control" name="price" @if(isset($edit)) value="{{$edit->price}}" @else value="{{old('price')}}" @endif/>
                                    @include('error', ['input' => 'price'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('month_count') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('month_count')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="number" min="0" class="form-control" name="month_count" @if(isset($edit)) value="{{$edit->month_count}}" @else value="{{old('month_count')}}" @endif/>
                                    @include('error', ['input' => 'month_count'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('image')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <button type="button" class="btn btn-info btn-file">{{word('browse')}}</button>
                                    @if(isset($edit))
                                        <br/><span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                        <br/>
                                        <div class="gallery">
                                            <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}" href="{{$edit->image}}" title="{{$edit->image}}" data-gallery style="padding : 10px 0px 0px 0px;">
                                                <div class="image">
                                                    <img src="{{$edit->image}}" alt="{{$edit->image}}"/>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="input-file"/>
                                    @include('error', ['input' => 'image'])
                                </div>
                            </div>
                            @if(isset($edit))
                                <input type="hidden" name="id" id="edit_id" value="{{$edit->id}}">
                            @else
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
        var bool = $('#edit_id').length === 0;

        var jvalidate = $("#jvalidate").validate({
            ignore: [],
            rules: {
                'image' : {
                    required : bool
                },
                'ar_name': {
                    required: true,
                },
                'en_name': {
                    required: true,
                },
                'price': {
                    required: true
                }
            },
        });
    </script>
@endsection
