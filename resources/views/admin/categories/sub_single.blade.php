@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/categories/index">{{word('categories')}}</a></li>
        @if(isset($edit))
            <li>{{$edit->name}}</li>
            <li class="active">{{word('edit')}}</li>
        @else
            <li>{{word('sub_cat')}}</li>
            <li class="active">{{word('create')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/category/update/sub" @else action="/admin/category/store/sub" @endif>
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
                            <div class="form-group {{ $errors->has('parent_id') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('category')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <select class="form-control select" name="parent_id">
                                        <option disabled selected>{{word('select_below')}}</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}" @if(isset($edit) && $edit->parent_id == $category->id) selected @elseif(old('parent_id') == $category->id) selected @endif>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    @include('error', ['input' => 'parent_id'])
                                </div>
                            </div>
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
                'parent_id' : {
                    required : $('#type').val() === 'sub'
                },
                'ar_name': {
                    required: true,
                },
                'en_name': {
                    required: true,
                }
            },
        });
    </script>
@endsection
