@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{trans('trans.home')}}</a></li>
        <li class="active">
            {{trans('trans.profile')}}
        </li>
    </ul>
    @include('message')
    <!-- PAGE TITLE -->
        <div class="page-title">
            <h2 class="switch_order">{{trans('trans.profile')}}</h2>
        </div>
    <!-- END PAGE TITLE -->

    <!-- PAGE CONTENT WRAPPER -->
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-5">

                <form action="#" class="form-horizontal">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3>{{admin()->name}}</h3>
                            <p><span class="label label-info label-form">{{word('system_admin')}}</span></p>
                            <div class="text-center" id="user_image">
                                <img src="{{admin()->image}}" class="img-thumbnail"/>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-6 col-sm-8 col-xs-7">

                <form action="/admin/profile/update" id="jvalidate" class="form-horizontal" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-body form-group-separated">
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('name')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input type="text" name="name" value="{{admin()->name}}" class="form-control"/>
                                </div>
                                @include('error',['input' => 'name'])
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('email')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input type="email" name="email" id="email" value="{{admin()->email}}" class="form-control"/>
                                </div>
                                @include('error',['input' => 'email'])
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('phone')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input type="text" name="phone" value="{{admin()->phone}}" class="form-control"/>
                                </div>
                                @include('error',['input' => 'phone'])
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('image')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <button type="button" class="btn btn-info btn-file">{{word('browse')}}</button>
                                    <br/>
                                    <span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                    <input type="file" name="image" class="input-file"/>
                                </div>
                                @include('error',['input' => 'image'])
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('password')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="{{word('leave_it')}}"/>
                                </div>
                                @include('error',['input' => 'password'])
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 col-xs-5 control-label">{{word('password_confirmation')}}</label>
                                <div class="col-md-9 col-xs-7">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="{{word('leave_it')}}"/>
                                </div>
                                @include('error',['input' => 'password'])
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-xs-5">
                                    <button class="btn btn-primary {{lang() == 'ar' ? 'pull-left' : 'pull-right'}}">{{word('update')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-3">
                <div class="panel panel-default form-horizontal">
                    <div class="panel-body">
                        <h3>{{word('quick_info')}}</h3>
                    </div>
                    <div class="panel-body form-group-separated">
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{word('last_update')}}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{\Carbon\Carbon::parse(admin()->updated_at)->format('m-d-Y')}}<br/>{{\Carbon\Carbon::parse(admin()->updated_at)->format('h:s a')}}</div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-5 control-label">{{word('reg_at')}}</label>
                            <div class="col-md-8 col-xs-7 line-height-30">{{\Carbon\Carbon::parse(admin()->created_at)->format('m-d-Y')}}<br/>{{\Carbon\Carbon::parse(admin()->created_at)->format('h:s a')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
    <script type="text/javascript">
        setTimeout(
            function() { $('#email').val('{{admin()->email}}'); },
            2000  //1,000 milliseconds = 1 second
        );
        var jvalidate = $("#jvalidate").validate({
            ignore: [],
            rules: {
                'name': {
                    required: true,
                },
                'email': {
                    required: true,
                    email: true
                },
                'phone': {
                    required: true,
                    digits : true
                },
                password: {
                    required: false,
                    minlength: 6
                },
                'password_confirmation': {
                    required: false,
                    minlength: 6,
                    equalTo: "#password"
                },
            },
        });
    </script>
@endsection
