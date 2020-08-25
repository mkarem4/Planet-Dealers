<!DOCTYPE html>
<html lang="en-US" itemscope="itemscope" itemtype="http://schema.org/WebPage" dir="{{lang() == 'ar' ? 'rtl' : 'ltr'}}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{word('app_name')}}</title>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/bootstrap.min.css')}}" media="all"/>

    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/font-awesome.min.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/animate.min.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/font-electro.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/owl-carousel.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/slick.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/style.css')}}" media="all"/>
    @if(lang() == 'ar')
        <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/bootstrap-rtl.min.css')}}" media="all"/>
    @endif
    @if(lang() == 'ar')
        <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/style.ar.css')}}" media="all"/>

        <style>
            .products-2-1-2 {
                margin-right: -50vw;
                margin-left: auto;
                right: 50%;

                left: auto;
            }
        </style>
    @endif
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/colors/yellow.css')}}" media="all"/>
    <link rel="stylesheet" type="text/css" href="{{asset('web_assets/css/custom.css')}}" media="all"/>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,700italic,800,800italic,600italic,400italic,300italic'
          rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="{{asset('web_assets/images/logoo.png')}}">
    <script type="text/javascript" src="{{asset('web_assets/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('web_assets/js/jquery-3.3.1.min.js')}}"></script>
    <script src="https://js.pusher.com/6.0/pusher.min.js"></script>


    <style>

        @media (max-width: 413px) {
            .copyright-bar .copyright {
                font-size: 11px;
            }
        }

    </style>

    @yield('style')
</head>
<body class="page home page-template-default">
<div id="page" class="hfeed site">
    <div class="top-bar">
        <div class="container">
            <nav>
                <ul id="menu-top-bar-left" class="nav nav-inline pull-left animate-dropdown flip">
                    @if(! user())
                        <li class="menu-item animate-dropdown"><a title="{{word('login')}}" href="/login"> <i
                                        class="fa fa-sign-in"></i>{{word('login')}}</a></li>
                        <li class="menu-item animate-dropdown"><a title="{{word('register')}}" href="/register"><i
                                        class="fa fa-user-plus" aria-hidden="true"></i>{{word('register')}}</a></li>

                </ul>
            </nav>
            <nav>
                <ul class="nav nav-inline pull-right">
                    <li class="menu-item">
                        <div class="dropdown">
                            <a href="javascript:void(0)" title="{{word('countries')}}" class="dropdown-toggle"
                               data-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-globe"></i>
                                <span>{{country()->name}}</span>
                            </a>
                            <ul class="dropdown-menu animated fadeInUp ">
                                @foreach($all_countries as $country)
                                    <li>
                                        <a href="/change_country/{{$country->id}}" class="hvr-bounce-to-right">
                                            <span>{{$country->l_name}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                    @else
                        <li class="menu-item">
                            <a href="/profile/inbox" style="color: white;"><i class="fa fa-envelope"></i><span> {{word('inbox')}} (<label
                                            id="msg-counter"> {{user()->get_unread()}} </label>) </span></a>
                        </li>
                        <li class="dropdown menu-item">
                            <a class="nav-link text-light" href="#" id="navbarDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span class="color">{{word('notifications')}}</span>
                            </a>
                            <ul class="dropdown-menu notification">
                                <li class="head text-light bg-dark">
                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12 col-12">
                                            <span>{{word('notifications')}} ({{user()->mini_nots->count()}})</span>
                                        </div>
                                    </div>
                                </li>
                                @foreach(user()->mini_nots as $not)
                                    <li class="notification-box">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-3 col-xs-3 text-center"
                                                 style="text-align: center; margin-top: 10px;">
                                                <i class="fa fa-bell fa-3x" style="text-align: center;"></i>
                                            </div>
                                            <div class="col-lg-9 col-sm-9 col-xs-9">
                                                <strong class="text-info name">
                                                    @if($not->type == 'global')
                                                        {{word('global_not')}}
                                                    @elseif($not->type == 'order')
                                                        {{word('order_not')}}
                                                    @else
                                                        {{word('message_not')}}
                                                    @endif
                                                </strong>
                                                <div>
                                                    <a @if($not->type == 'order') href="/profile/order/{{$not->action_id}}"
                                                       @elseif($not->type == 'message') href="/profile/inbox" @endif>
                                                        {{$not->text}}
                                                    </a>
                                                </div>
                                                <small class="text-warning">{{$not->created_at->diffForHumans()}}</small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                <li class="footer bg-dark text-center">
                                    <a href="/notifications" class="text-light">{{word('view_all')}}</a>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item">
                            <img width="20px" height="20px" style="border-radius: 360px;" src="{{user()->image}}">
                            <a href="/profile" style="color: white;"><span> {{user()->first_name}}</span></a>
                            {{--                                    <a href="/profile" style="color: white;"><i class="fa fa-user"></i><span> {{word('profile')}}  </span></a>--}}
                        </li>
                </ul>
            </nav>
            <nav>
                <ul class="nav nav-inline pull-right">

                    <li class="menu-item">
                        <a href="/logout" style="color: white;"><i
                                    class="fa fa-sign-out"></i><span> {{word('logout')}}  </span></a>
                    </li>


                    @endif
                    <lwori class="menu-item animate-dropdown">
                        @if(lang() == 'ar')
                            <a title="{{word('en')}}" href="javascript:void(0)" onClick="$('#en_form').submit()">
                                <img src="{{asset('web_assets/images/england-flag.png')}}" class="flag-img"
                                     alt="flag-img"/>{{word('e')}}</a>
                            <form method="post" action="/user/change_language" id="en_form">
                                {{csrf_field()}}
                                <input type="hidden" name="lang" value="en">
                            </form>
                        @else
                            <a title="{{word('arabic')}}" href="javascript:void(0)" onClick="$('#ar_form').submit()">
                                <img src="{{asset('web_assets/images/saudi-arabia-flag.png')}}" class="flag-img"
                                     alt="flag"> {{word('ar')}}</a>
                            <form method="post" action="/user/change_language" id="ar_form">
                                {{csrf_field()}}
                                <input type="hidden" name="lang" value="ar">
                            </form>
                        @endif
                    </lwori>
                </ul>
            </nav>
        </div>
    </div>
</div>
<header id="masthead" class="site-header header-v1">
    <div class="container">
        <div class="row">

            <!-- ============================================================= Header Logo ============================================================= -->
            <div class="header-logo">
                <a href="/" class="header-logo-link">
                    <img src="{{asset('web_assets/images/logo.png')}}" class="img-responsive">
                </a>
            </div>
            <!-- ============================================================= Header Logo : End============================================================= -->

            <form class="navbar-search" method="get" action="/products">
                <label class="sr-only screen-reader-text" for="search">{{word('search_for')}}</label>
                <div class="input-group">
                    <input type="text" id="search" class="form-control search-field"
                           dir="{{lang() == 'ar' ? 'rtl' : 'ltr'}}" name="name"
                           placeholder="{{word('search_for_products')}}"/>
                    <div class="input-group-addon search-categories">
                        <select name='category' id='product_cat' class='postform resizeselect'>
                            <option value='all' selected='selected'>{{word('all_cats')}}</option>
                            @foreach($all_cats as $cat)
                                <option class="level-0" value="{{$cat->id}}">{{$cat->l_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-secondary"><i class="ec ec-search"></i></button>
                    </div>
                </div>
            </form>
            @if(user())
                @php
                    $mini_cart = user()->getCartMini();
                @endphp
                <ul class="navbar-mini-cart navbar-nav animate-dropdown nav pull-right flip">
                    <li class="nav-item dropdown">
                        <a href="/cart" class="nav-link" data-toggle="dropdown">
                            <i class="ec ec-shopping-bag"></i>
                            <span class="cart-items-count count">{{$mini_cart['count']}}</span>
                            <span class="cart-items-total-price total-price"><span
                                        class="amount mini_cart_total">{{$mini_cart['sum']}} {{currency()}}</span></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-mini-cart">
                            <li>
                                <div class="widget_shopping_cart_content">
                                    <ul class="cart_list product_list_widget">
                                        @foreach($mini_cart['products'] as $cart)
                                            <li class="mini_cart_item">
                                                <a title="{{word('remove')}}" class="remove remove_cart"
                                                   data-info="{{$cart}}" href="javascript:void(0)">×</a>
                                                <a href="/product/{{$cart->product_id}}/details">
                                                    <img class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image"
                                                         src="{{$cart->product->image}}" alt="">
                                                    {{$cart->product->name}}&nbsp;
                                                </a>
                                                <span class="quantity">{{$cart->count}} × <span
                                                            class="amount">{{$cart->price / $cart->count}} {{currency()}}</span></span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <p class="buttons">
                                        <a class="button wc-forward" href="/cart">{{word('view_all')}}</a>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-wishlist nav navbar-nav pull-right flip">
                    <li class="nav-item">
                        <a href="/wishlist" class="nav-link" title="{{word('wishlist')}}"><i
                                    class="ec ec-favorites"></i></a>
                    </li>
                </ul>
            @endif
            <ul class="navbar-compare nav navbar-nav pull-right flip">
                <li class="nav-item">
                    <a href="/compare" class="nav-link"><i class="ec ec-compare"></i></a>
                </li>
            </ul>
        </div><!-- /.row -->
    </div>
    <!-- Available in two variations: "light" and "dark" | Change <header> class to see impact. -->
    <header class="dark">
        <nav role="navigation">
            <a href="javascript:void(0);" class="ic menu" tabindex="1">
                <span class="line"></span>
                <span class="line"></span>
                <span class="line"></span>
            </a>
            <a href="javascript:void(0);" class="ic close"></a>
            <ul class="main-nav">
                <li class="top-level-link">
                    <a href="/"><span>{{word('home')}}</span></a>
                </li>

                <li class="top-level-link">
                    <a class="mega-menu"><span>{{word('categories')}}</span></a>
                    <div class="sub-menu-block">
                        @foreach($all_cats as $cat)
                            <div class="row">
                                @foreach($cat->subs as $sub)
                                    <div class="col-md-4 col-lg-4 col-sm-12">
                                        <h2 class="sub-menu-head"><a
                                                    href="/products?category={{$sub->id}}">{{$sub->name}}</a></h2>
                                        <ul class="sub-menu-lists">
                                            @foreach($sub->subs as $sec)
                                                <li><a href="/products?category={{$sec->id}}">{{$sec->name}}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </li>

                <li class="top-level-link">
                    <a href="/about_us"><span>{{word('about_us')}}</span></a>
                </li>
                <li class="top-level-link">
                    <a href="/contact_us"><span>{{word('contact_us')}}</span></a>
                </li>
                <li class="top-level-link">
                    <a href="/search_request"><span>{{word('search_request')}}</span></a>
                </li>
                @if((user() && user()->type == 'seller') || !user())
                    <li class="top-level-link">
                        <a href="/packs"><span>{{word('packs_and_subs')}}</span></a>
                    </li>
                @endif
            </ul>
        </nav>
    </header>
</header>
@include('message')
@yield('content')
<section class="brands-carousel">
    <h2 class="sr-only">{{word('brands')}}</h2>
    <div class="container">
        <div id="owl-brands" class="owl-brands owl-carousel unicase-owl-carousel owl-outer-nav">
            @foreach(\App\Http\Controllers\Web\HomeController::getBrands() as $brand)
                <div class="item">
                    <a href="#">
                        <figure>
                            <img src="{{$brand}}" data-echo="{{$brand}}" class="img-responsive" alt="">
                        </figure>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
<footer id="colophon" class="site-footer">
    <div class="footer-widgets">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-xs-12">
                    <aside class="widget clearfix">
                        <div class="body">
                            <h4 class="widget-title">{{word('featured_products')}}</h4>
                            <ul class="product_list_widget">
                                @foreach(\App\Http\Controllers\Web\HomeController::getFeatured(3) as $product)
                                    <li>
                                        <a href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                            <img class="wp-post-image" data-echo="{{$product->image}}"
                                                 src="{{asset('web_assets/images/blank.gif')}}" alt="">
                                            <span class="product-title">{{$product->name}}</span>
                                        </a>
                                        <span class="electro-price">
                                                     @if($product->price_meta->price != $product->price_meta->sale_price)
                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                            @endif
                                                    <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-12">
                    <aside class="widget clearfix">
                        <div class="body"><h4 class="widget-title">{{word('on_sale')}}</h4>
                            <ul class="product_list_widget">
                                @foreach(\App\Http\Controllers\Web\HomeController::getDiscount(3) as $product)
                                    <li>
                                        <a href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                            <img class="wp-post-image" data-echo="{{$product->image}}"
                                                 src="{{asset('web_assets/images/blank.gif')}}" alt="">
                                            <span class="product-title">{{$product->name}}</span>
                                        </a>
                                        <span class="electro-price">
                                                     @if($product->price_meta->price != $product->price_meta->sale_price)
                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                            @endif
                                                    <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </div>
                <div class="col-lg-4 col-md-4 col-xs-12">
                    <aside class="widget clearfix">
                        <div class="body">
                            <h4 class="widget-title">{{word('top_rated')}}</h4>
                            <ul class="product_list_widget">
                                @foreach(\App\Http\Controllers\Web\HomeController::getTopRated(3) as $product)
                                    <li>
                                        <a href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                            <img class="wp-post-image" data-echo="{{$product->image}}"
                                                 src="{{asset('web_assets/images/blank.gif')}}" alt="">
                                            <span class="product-title">{{$product->name}}</span>
                                        </a>
                                        <span class="electro-price">
                                                     @if($product->price_meta->price != $product->price_meta->sale_price)
                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                            @endif
                                                    <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-newsletter">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-7">
                    <h5 class="newsletter-title">{{word('sign_up_news_letter')}}</h5>
                    <span class="newsletter-marketing-text">{{word('enjoy_news_and_discounts')}}</span>
                </div>
                <div class="col-xs-12 col-sm-5">
                    <form method="post" action="/newsletter/subscribe">
                        {{csrf_field()}}
                        <div class="input-group">
                            <input type="text" class="form-control" name="email"
                                   placeholder="{{word('type_ur_email')}}">
                            <span class="input-group-btn">
                                        <button class="btn btn-secondary" type="submit">{{word('sign_up')}}</button>
                                    </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom-widgets">
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-7">
                    <div class="columns">
                        <aside id="nav_menu-2" class="widget clearfix widget_nav_menu">
                            <div class="body">
                                <h4 class="widget-title">{{word('categories')}}</h4>
                                <div class="menu-footer-menu-1-container">
                                    <ul id="menu-footer-menu-1" class="menu">
                                        @foreach($all_cats->take(6) as $cat)
                                            <li class="menu-item"><a
                                                        href="/products?category={{$cat->id}}">{{$cat->l_name}}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div>
                    <div class="columns">
                        <aside id="nav_menu-4" class="widget clearfix widget_nav_menu">
                            <div class="body">
                                <h4 class="widget-title">{{word('pages')}}</h4>
                                <div class="menu-footer-menu-3-container">
                                    <ul id="menu-footer-menu-3" class="menu">
                                        <li class="menu-item"><a href="/">{{word('home')}}</a></li>
                                        <li class="menu-item"><a href="/about_us">{{word('about_us')}}</a></li>
                                        <li class="menu-item"><a href="/terms">{{word('terms')}}</a></li>
                                        <li class="menu-item"><a href="/packs">{{word('packs_and_subs')}}</a></li>
                                        <li class="menu-item"><a href="/contact_us">{{word('contact_us')}}</a></li>
                                        <li class="menu-item"><a href="/search_request">{{word('search_requests')}}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </aside>
                    </div><!-- /.columns -->

                </div><!-- /.col -->

                <div class="footer-contact col-xs-12 col-sm-12 col-md-5">
                    <div class="footer-logo">
                        <img src="{{asset('web_assets/images/logo.png')}}">
                    </div><!-- /.footer-contact -->

                    <div class="footer-address">
                        <div class="row">
                            <div class="apple col-xs-6 col-sm-6 col-md-6 col-lg-4">
                                <a href="{{$info->ios_link}}" target="_blank">
                                    <img src="{{asset('web_assets/images/footer/apple.png')}}">
                                </a>
                            </div>
                            <div class="apple col-xs-6 col-sm-6 col-md-6 col-lg-4">
                                <a href="{{$info->android_link}}">
                                    <img src="{{asset('web_assets/images/footer/android.png')}}">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="footer-social-icons">
                        <ul class="social-icons list-unstyled">
                            @foreach(\App\Http\Controllers\Web\HomeController::getSocials() as $social)
                                <li><a href="{{$social->link}}" target="_blank"><img src="{{$social->image}}"
                                                                                     style="border-radius: 360px;"></a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <nav>
        <ul id="menu-top-bar-right" class="nav nav-inline pull-right animate-dropdown flip social">
            @foreach(\App\Http\Controllers\Web\HomeController::getSocials() as $social)
                <li class="menu-item animate-dropdown"><a href="{{$social->link}}" target="_blank"><img
                                src="{{$social->image}}" style="border-radius: 360px;"></a></li>
            @endforeach
        </ul>
    </nav>
    <div class="copyright-bar">
        <div class="container">
            <div class="{{lang() == 'ar' ? 'pull-right' : 'pull-left'}} flip copyright"><a
                        href="/">{{word('app_name')}}</a> - {{word('all_rights_reserved') . ' ' .date('Y')}} - <a
                        href="http://peekssolution.com/">{{word('by_peeks')}}</a></div>
        </div><!-- /.container -->
    </div><!-- /.copyright-bar -->
</footer>
</body>

<!-- START SCRIPTS -->
<audio id="myAudio" class="hidden">
    <source src="{{asset('admin_assets/audio/fail.mp3')}}" type="audio/ogg">
    <source src="{{asset('admin_assets/audio/fail.mp3')}}" type="audio/mpeg">
</audio>

<!-- START PRELOADS -->
<audio id="audio-alert" src="{{asset('admin_assets/audio/alert.mp3')}}" preload="auto"></audio>
<audio id="audio-fail" src="{{asset('admin_assets/audio/fail.mp3')}}" preload="auto"></audio>
<!-- END PRELOADS -->

<script type="text/javascript" src="{{asset('web_assets/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/tether.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/bootstrap-hover-dropdown.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/owl.carousel.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/slick.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/echo.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/wow.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/jquery.easing.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/jquery.waypoints.min.js')}}"></script>
<script type="text/javascript" src="{{asset('web_assets/js/electro.js')}}"></script>


<script>
    var path = '{{request()->path()}}';

    @if(user())
    var audio_alert = document.getElementById('myAudio');
    var target_id = $('#target_id');

    // Enable pusher logging - don't include this in production
    // Pusher.logToConsole = false;

    var pusher = new Pusher('1208bb67bd1ed58a290b', {
        cluster: 'eu'
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('Broad-Msg', function (data) {
        if (data.payload.message.target_id == '{{user()->id}}') {
            if (path === 'profile/inbox') {
                if (target_id !== data.payload.message.sender_id) {
                    $side_counter = $('#user_counter_' + data.payload.message.sender_id);
                    $side_old_val = parseInt($side_counter.data('value'));
                    $side_counter.html('( ' + ($side_old_val + 1) + ' )');
                    $side_counter.data('value', $side_old_val + 1);
                    $old_val = parseInt($('#msg-counter').html());
                    $('#msg-counter').html($old_val + 1);
                }
                $('.messages-list').append('<li class="sent"><img src="' + data.payload.image + '" alt="" />\n' + '<p>' + data.payload.message.text + '<span class="message-time-{{lang()}}">' + data.payload.date + '</span></p></li>');
            } else {
                $old_val = parseInt($('#msg-counter').html());
                $('#msg-counter').html($old_val + 1);
            }
        }
        console.log('else', data.payload.date);
    });

    $('.add_to_wishlist').on('click', function () {
        $a = $(this);
        $product_id = $(this).data('id');

        $.ajax
        (
            {
                async: false,
                url: '/ajax/wishlist_handle',
                method: 'post',
                data: {product_id: $product_id, _token: '{{csrf_token()}}'},
                dataType: 'json',
                success: function (data) {
                    if (data.type === true) {
                        $a.html('');
                        $a.html('<span class="fa fa-heart"></span> {{word('added')}}</span>');
                    } else {
                        $a.html('');
                        $a.html('<span class="fa fa-heart-o"></span> {{word('removed')}}</span>');
                        if (path === 'wishlist') {
                            $a.closest('li').remove();
                        }
                    }
                },
                error: function (data) {
                    console.log('error', data);
                }
            }
        );
    });


    $('.remove_cart').on('click', function () {
        $a = $(this);
        $info = $(this).data('info');

        $.ajax
        (
            {
                async: false,
                url: '/ajax/cart_remove',
                method: 'post',
                data: {cart_id: $info.id, _token: '{{csrf_token()}}'},
                dataType: 'json',
                success: function (data) {
                    if (data.status === 'success') {
                        if (path === 'cart') {
                            $a.closest('tr').remove();
                        } else {
                            $a.closest('li').remove();
                        }

                        $('.cart-items-count').html(data.count);
                        $('.mini_cart_total').html(data.total);
                    } else {
                        console.log('remove cart response error')
                    }
                },
                error: function (data) {
                    console.log('remove cart error', data);
                }
            }
        );
    });

    @endif

    $('.add-to-compare-link').on('click', function () {
        $a = $(this);
        $product_id = $(this).data('id');

        $.ajax
        (
            {
                async: false,
                url: '/ajax/compare_handle',
                method: 'post',
                data: {product_id: $product_id, _token: '{{csrf_token()}}'},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.type === 'full') {
                        $a.html('');
                        $a.html('{{word('compare_list_3_max')}}');
                    } else if (data.type === true) {
                        $a.prop('title', '{{word('added')}}');
                        $a.html('');
                        $a.html('<span class="ec ec-compare"></span> {{word('added')}}</span>');
                    } else {
                        console.log('else', data);
                        $a.prop('title', '{{word('removed')}}');
                        $a.html('');
                        $a.html('<span class="ec ec-compare"></span> {{word('removed')}}</span>');
                        if (path === 'compare') {
                            $('.td_' + $product_id).remove();
                        }
                    }
                },
                error: function (data) {
                    console.log('error', data);
                }
            }
        );
    });
</script>
@yield('script')
</html>
