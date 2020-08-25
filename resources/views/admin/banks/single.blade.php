@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/banks/index">{{word('banks')}}</a></li>
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
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/bank/update" @else action="/admin/bank/store" @endif>
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
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}" @if(isset($edit) && $edit->country_id == $country->id) selected @elseif(old('country_id') == $country->id) selected @endif>{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('error', ['input' => 'country_id'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('ar_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="ar_name" @if(isset($edit)) value="{{$edit->ar_name}}" @else value="{{old('ar_name')}}" @endif/>
                                    @include('error', ['input' => 'ar_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('ar_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('ar_desc')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <textarea  class="form-control summernote" name="ar_desc">@if(isset($edit)){{$edit->ar_desc}}@else{{old('ar_desc')}}@endif</textarea>
                                    @include('error', ['input' => 'ar_desc'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="en_name" @if(isset($edit)) value="{{$edit->en_name}}" @else value="{{old('en_name')}}" @endif/>
                                    @include('error', ['input' => 'en_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('en_desc') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('en_desc')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <textarea  class="form-control summernote" name="en_desc">@if(isset($edit)){{$edit->en_desc}}@else{{old('en_desc')}}@endif</textarea>
                                    @include('error', ['input' => 'en_desc'])
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
{{--    <script>--}}
{{--        var bool = $('#edit_id').length === 0;--}}

{{--        var jvalidate = $("#jvalidate").validate({--}}
{{--            ignore: [],--}}
{{--            rules: {--}}
{{--                'parent_id' : {--}}
{{--                    required : $('#type').val() === 'sub'--}}
{{--                },--}}
{{--                'ar_name': {--}}
{{--                    required: true,--}}
{{--                },--}}
{{--                'en_name': {--}}
{{--                    required: true,--}}
{{--                },--}}
{{--                'descs[ar]': {--}}
{{--                    required: true--}}
{{--                },--}}
{{--                'descs[en]': {--}}
{{--                    required: true--}}
{{--                }--}}
{{--            },--}}
{{--        });--}}
{{--    </script>--}}
@endsection
