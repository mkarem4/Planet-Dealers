<!DOCTYPE html>
<html lang="en" class="body-full-height">
<head>
    <!-- META SECTION -->
    <title>{{word('app_name')}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    @if(lang() == 'ar')
        <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin_assets/css/theme-default_rtl.css')}}"/>
        <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin_assets/css/rtl.css')}}"/>
    @else
        <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin_assets/css/theme-default.css')}}"/>
    @endif

    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin_assets/css/custom.css')}}"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body>
    <div class="login-container" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
        <div class="login-box animated fadeInDown">
            <h2 style="text-align: center; color: white;">{{word('app_name')}}</h2>
            <div style="text-align: center; margin: 0 auto;">
                <h2><span class="label label-info">{{word('admin_admin_panel')}}</span></h2>
            </div>
            <div class="login-body" style="direction: rtl;">
                <div class="login-title" dir="{{lang() == 'ar' ? 'rtl' : 'ltr'}}"><strong>{{word('welcome')}} </strong>,{{word('please_login')}}</div>
                <div class="login-title" style="color: red;"><strong>@if(Session::get('error')) {{word(''.Session::get('error'))}} @endif</strong></div>
                <form action="/admin/login" class="form-horizontal" method="post" style="text-align: left;">
                    {{csrf_field()}}
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="text" class="form-control text-{{lang()}}" name="login" placeholder="{{word('login_param')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <input type="password" class="form-control text-{{lang()}}" name="password" placeholder="{{word('password')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <button class="btn btn-info btn-block">{{word('login')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

