@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li class="active"><a href="/admin/variations/index">{{word('variations')}}</a></li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
            <div class="col-md-12 index-widget">
                <div class="col-md-3">
                    <div class="widget widget-success widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-check"></span>
                        </div>
                        <div class="widget-data" >
                            <div class="widget-int num-count">{{$active_count}}</div>
                            <div class="widget-title">{{word('active_variations')}}</div>
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
                            <div class="widget-title">{{word('suspended_variations')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="{{word('name')}}" value="{{isset($inputs['keyword']) ? $inputs['keyword'] : ''}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="active" {{isset($inputs['status']) && $inputs['status'] == 'active' ? 'selected' : ''}}>{{word('active')}}</option>
                            <option value="suspended" {{isset($inputs['status']) && $inputs['status'] == 'suspended' ? 'selected' : ''}}>{{word('suspended')}}</option>
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
                        <a><button class="btn btn-info" data-toggle="modal" id="btn-create-option" data-target="#create_variation_option">{{word('create_variation_option')}}</button></a>
                        <a><button class="btn btn-info" data-toggle="modal" id="btn-create" data-target="#create_variation">{{word('create_variation')}}</button></a>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('name')}}</th>
                                    <th class="'rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($variations as $variation)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{lang() == 'ar' ? $variation->ar_name : $variation->en_name}}</td>
                                        <td><span class="label {{$variation->status == 'active' ? 'label-success' : 'label-primary'}} label-form">{{word($variation->status)}}</span></td>
                                        <td>
                                            @if($variation->options->count())
                                                <a href="/admin/variation/{{$variation->id}}/options"><button class="btn btn-condensed btn-info" title="{{word('show_options')}}"><i class="fa fa-eye"></i></button></a>
                                            @endif
                                            <a><button class="btn btn-condensed btn-warning btn-edit" id="{{$variation->id}}_edit" data-toggle="modal" data-target="#edit_variation" data-model="{{$variation}}" title="{{word('edit')}}"><i class="fa fa-edit"></i></button></a>
                                            @if($variation->status == 'active')
                                            <button class="btn btn-primary btn-condensed mb-control" onclick="modal_suspend({{$variation->id}})" data-box="#message-box-primary" title="{{word('suspend')}}"><i class="fa fa-minus-circle"></i></button>
                                            @else
                                                <button class="btn btn-success btn-condensed mb-control" onclick="modal_activate({{$variation->id}})" data-box="#message-box-success" title="{{word('activate')}}"><i class="fa fa-check-square"></i></button>
                                            @endif

                                            <button class="btn btn-danger btn-condensed mb-control" onclick="modal_destroy({{$variation->id}})" data-box="#message-box-danger" title="{{word('delete')}}"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">{{word('sorry_no_data')}}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                                {!! $variations->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="create_variation" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                    <button type="button" class="close" data-dismiss="modal" style="float : {{lang() == 'ar' ? 'left' : 'right'}};"><span aria-hidden="true">&times;</span><span class="sr-only">{{word('close')}}</span></button>
                    <h4 class="modal-title" id="defModalHead">{{word('create_variation')}}</h4>
                </div>

                <form class="form-horizontal" id="edit_jvalidate" method="post" action="/admin/variation/store">
                    <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                        <div class="row">
                            <div class="col-md-12" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
                                {{csrf_field()}}
                                <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                                    <div class="form-group {{ $errors->has('ar_name') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('ar_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" name="ar_name" value="{{old('ar_name')}}"/>
                                            @include('error', ['input' => 'ar_name'])
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" name="en_name" value="{{old('en_name')}}"/>
                                            @include('error', ['input' => 'en_name'])
                                        </div>
                                    </div>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{word('close')}}</button>
                        <button type="submit" class="btn btn-success">{{word('create')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal" id="edit_variation" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                    <button type="button" class="close" data-dismiss="modal" style="float : {{lang() == 'ar' ? 'left' : 'right'}};"><span aria-hidden="true">&times;</span><span class="sr-only">{{word('close')}}</span></button>
                    <h4 class="modal-title" id="defModalHead">{{word('edit_variation')}}</h4>
                </div>

                <form class="form-horizontal" id="edit_jvalidate" method="post" action="/admin/variation/update">
                    <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                        <div class="row">
                            <div class="col-md-12" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
                                {{csrf_field()}}
                                <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                                    <div class="form-group {{ $errors->has('edit_ar_name') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('ar_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" id="ar_name" name="edit_ar_name" value=""/>
                                            @include('error', ['input' => 'edit_ar_name'])
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('en_name') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" id="en_name" name="edit_en_name" value=""/>
                                            @include('error', ['input' => 'edit_ar_name'])
                                        </div>
                                    </div>
                                    <input type="hidden" name="id" id="edit_id" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{word('close')}}</button>
                        <button type="submit" class="btn btn-success">{{word('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="create_variation_option" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                    <button type="button" class="close" data-dismiss="modal" style="float : {{lang() == 'ar' ? 'left' : 'right'}};"><span aria-hidden="true">&times;</span><span class="sr-only">{{word('close')}}</span></button>
                    <h4 class="modal-title" id="defModalHead">{{word('create_variation_option')}}</h4>
                </div>
                <form class="form-horizontal" id="create_jvalidate_option" method="post" action="/admin/variation/option/store">
                    <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                        <div class="row">
                            <div class="col-md-12" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
                                {{csrf_field()}}
                                <div class="modal-body {{lang() == 'ar' ? 'switch_en' : 'switch_ar'}}">
                                    <div class="form-group {{ $errors->has('parent_id_option') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('variation')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <select class="form-control select" name="parent_id_option">
                                                <option disabled selected>{{word('choose_from_below')}}</option>
                                                @foreach($all_variations as $var)
                                                    <option value="{{$var->id}}">{{$var->name}}</option>
                                                @endforeach
                                            </select>
                                            @include('error', ['input' => 'parent_id_option'])
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('ar_name_option') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('ar_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" name="ar_name_option" value="{{old('ar_name_option')}}"/>
                                            @include('error', ['input' => 'ar_name_option'])
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('en_name_option') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('en_name')}}</label>
                                        <div class="col-md-9 col-xs-12">
                                            <input type="text" class="form-control" name="en_name_option" value="{{old('en_name_option')}}"/>
                                            @include('error', ['input' => 'en_name_option'])
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('status_option') ? ' has-error' : '' }}">
                                        <label class="col-md-3 col-xs-12 control-label">{{word('do_activate')}}</label>
                                        <div class="col-md-6 col-xs-12">
                                            <label class="switch">
                                                <input type="checkbox" name="status_option" value="active">
                                                <span></span>
                                            </label>
                                            @include('error', ['input' => 'status'])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{word('close')}}</button>
                        <button type="submit" class="btn btn-success">{{word('create')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- success with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert" id="message-box-success">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-check-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('activate_variation')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/variation/change_status" class="buttons">
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
                    <p>{{word('suspend_variation')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/variation/change_status" class="buttons">
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
                    <p>{{word('delete_variation')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/variation/delete" class="buttons">
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

    <script>
        $('#btn-create').on('click', function()
        {
            var jvalidate = $(".jvalidate").validate({
                ignore: [],
                rules: {
                    'ar_name': {
                        required: true,
                    },
                    'en_name': {
                        required: true,
                    }
                },
            });
        });


        $('#btn-create-option').on('click', function()
        {
            var jvalidate = $("#create_jvalidate_option").validate({
                ignore: [],
                rules: {
                    'parent_id_option': {
                        required: true,
                    },
                    'ar_name_option': {
                        required: true,
                    },
                    'en_name_option': {
                        required: true,
                    }
                },
            });
        });


        $('.btn-edit').on('click', function()
        {
            $data = $(this).data('model');

            $('#ar_name').val($data.ar_name);
            $('#en_name').val($data.en_name);
            $('#edit_id').val($data.id);

            var jvalidate = $("#edit_jvalidate").validate({
                ignore: [],
                rules: {
                    'ar_name': {
                        required: true,
                    },
                    'en_name': {
                        required: true,
                    }
                },
            });
        });

        @if($errors->has('ar_name') || $errors->has('en_name'))
            $('#btn-create').click();
        @endif

        @if($errors->has('parent_id_option') || $errors->has('ar_name_option') || $errors->has('en_name_option'))
            $('#btn-create-option').click();
        @endif

        @if($errors->has('edit_ar_name') || $errors->has('edit_en_name'))
            $('#{{old('id')}}_edit').click();
        @endif
    </script>
@endsection
