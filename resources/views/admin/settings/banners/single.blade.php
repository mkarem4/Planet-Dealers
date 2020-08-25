@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li>{{word('settings')}}</li>
        <li><a href="/admin/settings/banners/index">{{word('banners')}}</a></li>
        @if(isset($edit))
            <li class="active">{{word('edit')}}</li>
        @else
            <li class="active">{{word('create')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/settings/banner/update" @else action="/admin/settings/banner/store" @endif enctype="multipart/form-data">
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
                            <div class="form-group {{ $errors->has('country_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('country')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <select class="form-control select" name="country_id">
                                        <option selected disabled>{{word('select_below')}}</option>
                                        <option value="0"  @if(isset($edit) && $edit->country_id == '0') selected @elseif(old('country_id') == '0') selected @endif>{{word('all')}}</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" @if(isset($edit) && $edit->country_id == $country->id) selected @elseif(old('country_id') == $country->id) selected @endif>{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('error', ['input' => 'country_id'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('expire_at') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('expire_at')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="date" class="form-control" name="expire_at" @if(isset($edit)) value="{{$edit->expire_at}}" @else value="{{old('expire_at')}}" @endif/>
                                    @include('error', ['input' => 'expire_at'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('url')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="url" @if(isset($edit)) value="{{$edit->url}}" @else value="{{old('url')}}" @endif/>
                                    @include('error', ['input' => 'url'])
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
                                                    <img class="table-image" src="{{$edit->image}}" alt="{{$edit->image}}"/>
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
                'country_id': {
                    required: true,
                },
                'expire_at': {
                    required: true,
                },
                'url': {
                    required: true
                },
            },
        });
    </script>
@endsection
