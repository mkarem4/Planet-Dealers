<!DOCTYPE html>
<html lang="{{lang()}}">
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
    <!-- START PLUGINS -->

    <link rel="stylesheet" type="text/css" id="theme" href="{{asset('admin_assets/css/custom.css')}}"/>

    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/jquery/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/bootstrap/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/morris/raphael.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/morris/morris.min.js')}}"></script>


    <!-- END PLUGINS -->


    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/jquery-validation/jquery.validate.js')}}"></script></head>
<body>
    <!-- START PAGE CONTAINER -->
    <div class="page-container @if(lang() == 'ar') page-mode-rtl page-content-rtl @endif">
        <!-- START PAGE SIDEBAR -->
        <div class="page-sidebar page-sidebar-fixed scroll" style="height: 0px !important">
            <!-- START X-NAVIGATION -->
            <ul class="x-navigation">
                <li class="xn-logo">
                    <a href="/admin/dashboard">{{word('dashboard')}}</a>
                    <a href="#" class="x-navigation-control"></a>
                </li>
                <li class="xn-profile">
                    <div class="profile">
                        <div class="profile-image">
                            <a href="/admin/profile" title="{{word('profile')}}"><img src="{{admin()->image}}" alt="{{word('dashboard')}}"/></a>
                        </div>
                        <div class="profile-data">
                            <div class="profile-data-name">{{admin()->name}}</div>
                            <div class="profile-data-title"><h5><span class="label label-info label-form">{{word('system_admin')}}</span></h5></div>
                        </div>
                    </div>
                </li>
                <li class="@if(Request::is('admin/dashboard')) active @endif switch_{{lang()}}">
                    <a href="/admin/dashboard"><span class="xn-text">{{word('dashboard')}}</span> <span class="fa fa-home"></span></a>
                </li>
                <li class="@if(Request::is('admin/admins/index') || Request::is('admin/admin/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/admins/index"><span class="xn-text">{{word('system_admins')}}</span> <span class="fa fa-user-secret"></span></a>
                </li>
                <li class="@if(Request::is('admin/countries/index') || Request::is('admin/country/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/countries/index"><span class="xn-text">{{word('countries')}}</span> <span class="fa fa-flag"></span></a>
                </li>
                <li class="@if(Request::is('admin/categories/index') || Request::is('admin/category/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/categories/index"><span class="xn-text">{{word('categories')}}</span> <span class="fa fa-navicon"></span></a>
                </li>
                <li class="@if(Request::is('admin/variations/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/variations/index"><span class="xn-text">{{word('variations')}}</span> <span class="fa fa-random"></span></a>
                </li>
                <li class="@if(Request::is('admin/banks/index') || Request::is('admin/bank/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/banks/index"><span class="xn-text">{{word('banks')}}</span> <span class="fa fa-bank"></span></a>
                </li>
                <li class="@if(Request::is('admin/packs/index') || Request::is('admin/pack/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/packs/index"><span class="xn-text">{{word('packs')}}</span> <span class="fa fa-trophy"></span></a>
                </li>
                <li class="@if(Request::is('admin/transfers/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/transfers/index"><span class="xn-text">{{word('bank_transfers')}}</span> <span class="fa fa-money"></span></a>
                </li>
                <li class="@if(Request::is('admin/merchants/index') || Request::is('admin/merchant/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/merchants/index"><span class="xn-text">{{word('merchants')}}</span> <span class="fa fa-users"></span></a>
                </li>
                <li class="@if(Request::is('admin/products/index') || Request::is('admin/product/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/products/index"><span class="xn-text">{{word('products')}}</span> <span class="fa fa-cubes"></span></a>
                </li>
                <li class="@if(Request::is('admin/orders/index') || Request::is('admin/order/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/orders/index"><span class="xn-text">{{word('orders')}}</span> <span class="fa fa-shopping-cart"></span></a>
                </li>
                <li class="@if(Request::is('admin/contacts/index') || Request::is('admin/contact/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/contacts/index"><span class="xn-text">{{word('contact_us_msgs')}}</span> <span class="fa fa-envelope"></span></a>
                </li>
                <li class="@if(Request::is('admin/search_requests/index') || Request::is('admin/search_request/*')) active @endif switch_{{lang()}}">
                    <a href="/admin/search_requests/index"><span class="xn-text">{{word('search_requests')}}</span> <span class="fa fa-search"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/slides/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/slides/index"><span class="xn-text">{{word('slides')}}</span><span class="fa fa-image"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/banners/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/banners/index"><span class="xn-text">{{word('banners')}}</span><span class="fa fa-arrows-h"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/brands/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/brands/index"><span class="xn-text">{{word('brands')}}</span><span class="fa fa-tags"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/socials/index')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/socials/index"><span class="xn-text">{{word('socials')}}</span><span class="fa fa-facebook-square"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/abouts/edit')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/abouts/edit"><span class="xn-text">{{word('about_us')}}</span><span class="fa fa-info-circle"></span></a>
                </li>
                <li class="@if(Request::is('admin/settings/terms/edit')) active @endif switch_{{lang()}}">
                    <a href="/admin/settings/terms/edit"><span class="xn-text">{{word('terms')}}</span><span class="fa fa-info-circle"></span></a>
                </li>
            </ul>
            <!-- END X-NAVIGATION -->
        </div>
        <!-- END PAGE SIDEBAR -->

        <!-- PAGE CONTENT -->
        <div class="page-content">
            <!-- START X-NAVIGATION VERTICAL -->
            <ul class="x-navigation x-navigation-horizontal x-navigation-panel">
                <!-- POWER OFF -->
                <li class="xn-icon-button @if(lang() == 'ar') pull-left @else pull-right @endif 'last">
                    <a href="#" class="mb-control" data-box="#mb-signout" title="Logout"><span class="fa fa-power-off"></span></a>
                </li>
                <!-- END POWER OFF -->

                <!-- LANG BAR -->
                <li class="xn-icon-button @if(lang() == 'ar') pull-left @else pull-right @endif last">
                        @if (lang() == 'ar')
                            <a href="javascript:void(0)" onclick="change_lang('en')" title="{{word('english')}}" class="lang" data-value="en">{{word('e')}}</a>
                        @else
                            <a href="javascript:void(0)" onclick="change_lang('ar')" title="{{word('arabic')}}" class="lang" data-value="en">{{word('ar')}}</a>
                        @endif
                </li>
                <!-- END LANG BAR -->
                <form method="post" id="lang_form" action="/admin/change_language">
                    {{csrf_field()}}
                    <input type="hidden" name="lang" id="lang_input" value="">
                </form>
            </ul>
            <!-- END X-NAVIGATION VERTICAL -->

            <!-- MESSAGE BOX-->
            <div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
                <div class="mb-container">
                    <div class="mb-middle">
                        <div class="mb-title"><span class="fa fa-sign-out"></span>{{word('logout')}}</div>
                        <div class="mb-content">
                            <p>{{word('log_out_are_you_sure')}}</p>
                        </div>
                        <div class="mb-footer">
                            <div class="pull-right">
                                <a href="/admin/logout" class="btn btn-success btn-lg">{{word('yes')}}</a>
                                <button class="btn btn-default btn-lg mb-control-close">{{word('no')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MESSAGE BOX-->
            @yield('content')
        </div>
    </div>

    <!-- BLUEIMP GALLERY -->
    <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <h3 class="title"></h3>
        <a class="prev">‹</a>
        <a class="next">›</a>
        <a class="close">×</a>
        <ol class="indicator"></ol>
    </div>
    <!-- END BLUEIMP GALLERY -->

    <!-- START SCRIPTS -->
    <audio id="myAudio" class="hidden">
        <source src="{{asset('admin_assets/audio/fail.mp3')}}" type="audio/ogg">
        <source src="{{asset('admin_assets/audio/fail.mp3')}}" type="audio/mpeg">
    </audio>

    <!-- START PRELOADS -->
    <audio id="audio-alert" src="{{asset('admin_assets/audio/alert.mp3')}}" preload="auto"></audio>
    <audio id="audio-fail" src="{{asset('admin_assets/audio/fail.mp3')}}" preload="auto"></audio>
    <!-- END PRELOADS -->

    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/noty/jquery.noty.js')}}"></script>
    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/noty/themes/default.js')}}"></script>
    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/noty/layouts/bottomCenter.js')}}"></script>
    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/icheck/icheck.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/owl/owl.carousel.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/actions.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/bootstrap/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/bootstrap/bootstrap-file-input.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/tagsinput/jquery.tagsinput.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('admin_assets/js/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/blueimp/jquery.blueimp-gallery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin_assets/js/plugins/summernote/summernote.js')}}"></script>

    @if(lang() == 'ar')
        <script type="text/javascript" src="{{asset('admin_assets/js/plugins/bootstrap/bootstrap-select-ar.js')}}"></script>
        <script type='text/javascript' src="{{asset('admin_assets/js/plugins/jquery-validation/localization/messages_ar.js')}}"></script>
    @else
        <script type="text/javascript" src="{{asset('admin_assets/js/plugins/bootstrap/bootstrap-select-en.js')}}"></script>
        <script type='text/javascript' src="{{asset('admin_assets/js/plugins/jquery-validation/localization/messages_en.js')}}"></script>
    @endif

    <script type="text/javascript" src="{{asset('admin_assets/js/custom.js')}}"></script>
    <script>
        setTimeout(
            function() { $(':password').val(''); },
            1000  //1,000 milliseconds = 1 second
        );

        $('#links').on('click',function(event)
        {
            event = event || window.event;
            var target = event.target || event.srcElement;
            var link = target.src ? target.parentNode : target;
            var options = {index: link, event: event,onclosed: function(){
                    setTimeout(function(){
                        $("body").css("overflow","");
                    },200);
                }};
            var links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        });
    </script>
</body>
</html>
