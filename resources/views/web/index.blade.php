@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <div class="home-v1-slider" >
                        <div id="owl-main" style="direction: ltr" class="owl-carousel owl-inner-nav owl-ui-sm">
                            @foreach($slides as $slide)
                                <a href="{{$slide->url}}" target="_blank">
                                <div class="item" style="background-image: url({{$slide->image}});">
                                </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="home-v1-deals-and-tabs deals-and-tabs row animate-in-view fadeIn animated" data-animation="fadeIn">
                        @php
                            $discount_counter = \App\Http\Controllers\Web\HomeController::getDiscount(1,1)->first();
                        @endphp
                        <div class="deals-block col-lg-4">

                            @if($discount_counter)

                            <section class="section-onsale-product">
                                <header>
                                    <h2 class="h1">{{word('special_offer')}}</h2>
                                    <div class="savings">
                                        <span class="savings-text">{{word('save_sale')}} <span class="amount">{{$discount_counter->price_meta->price - $discount_counter->price_meta->sale_price}} {{currency()}}</span></span>
                                    </div>
                                </header><!-- /header -->

                                <div class="onsale-products">
                                    <div class="onsale-product">
                                        <a href="/product/{{$discount_counter->id}}/details">
                                            <div class="product-thumbnail">
                                                <img class="wp-post-image" data-echo="{{$discount_counter->image}}" src="{{asset('web_assets/images/blank.gif')}}" alt=""></div>

                                            <h3>{{$discount_counter->name}}</h3>
                                        </a>

                                        <span class="price">
                            						<span class="electro-price">
                            							<ins><span class="amount">{{$discount_counter->price_meta->sale_price}} {{currency()}}</span></ins>
                            							<del><span class="amount">{{$discount_counter->price_meta->price}} {{currency()}}</span></del>
                            						</span>
                            					</span><!-- /.price -->

                                        <div class="deal-progress">
                                            <div class="deal-stock">
                                                <span class="stock-sold">{{word('already_sold')}}: <strong>{{$discount_counter->sold}}</strong></span>
                                                <span class="stock-available">{{word('available')}}: <strong>{{$discount_counter->price_meta->count}}</strong></span>
                                            </div>

                                            <div class="progress">
                                                <span class="progress-bar" style="width:{{$discount_counter->sold / $discount_counter->price_meta->count * 100}}%">{{$discount_counter->sold / $discount_counter->price_meta->count}}</span>
                                            </div>
                                        </div><!-- /.deal-progress -->

                                        <div class="deal-countdown-timer">
                                            <div class="marketing-text text-xs-center">{{word('hurry_up_ends_in')}}:	</div>
                                            <div id="deal-countdown" class="countdown">
                                                <span class="hours"><span class="value">0</span><b>{{word('hours')}}</b></span>
                                                <span class="minutes"><span class="value">0</span><b>{{word('mins')}}</b></span>
                                                <span class="seconds"><span class="value">0</span><b>{{word('secs')}}</b></span>
                                            </div>
                                            <span class="deal-end-date" style="display:none;">{{$discount_counter->discount_till}}</span>
                                            <script>
                                                // set the date we're counting down to
                                                var deal_end_date = document.querySelector(".deal-end-date").textContent;
                                                var target_date = new Date( deal_end_date ).getTime();

                                                // variables for time units
                                                var days, hours, minutes, seconds;

                                                // get tag element
                                                var countdown = document.getElementById( 'deal-countdown' );

                                                // update the tag with id "countdown" every 1 second
                                                setInterval( function () {

                                                    // find the amount of "seconds" between now and target
                                                    var current_date = new Date().getTime();
                                                    var seconds_left = (target_date - current_date) / 1000;

                                                    // do some time calculations
                                                    days = parseInt(seconds_left / 86400);
                                                    seconds_left = seconds_left % 86400;

                                                    hours = parseInt(seconds_left / 3600);
                                                    seconds_left = seconds_left % 3600;

                                                    minutes = parseInt(seconds_left / 60);
                                                    seconds = parseInt(seconds_left % 60);

                                                    // format countdown string + set tag value
                                                    countdown.innerHTML = '<span data-value="' + days + '" class="days"><span class="value">' + days +  '</span><b>{{word('days')}}</b></span><span class="hours"><span class="value">' + hours + '</span><b>{{word('hours')}}</b></span><span class="minutes"><span class="value">'
                                                        + minutes + '</span><b>{{word('mins')}}</b></span><span class="seconds"><span class="value">' + seconds + '</span><b>Secs</b></span>';

                                                }, 1000 );
                                            </script>
                                        </div><!-- /.deal-countdown-timer -->
                                    </div><!-- /.onsale-product -->
                                </div><!-- /.onsale-products -->
                            </section>

                            @endif
                        </div>
                        <div class="tabs-block col-lg-8">
                            <div class="products-carousel-tabs">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab-products-1" role="tabpanel">
                                        <div class="woocommerce columns-3">
                                            <ul class="products columns-3">
                                                @foreach(\App\Http\Controllers\Web\HomeController::getBestSelling(6) ??[] as $product)
                                                    <li class="product {{$loop->first ? 'first' : ''}} {{is_int($loop->iteration /3) ? 'last' : ''}}">
                                                        <div class="product-outer">
                                                            <div class="product-inner">
                                                                <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                                <a href="/product/{{$product->id}}/details">
                                                                    <h3>{{$product->name}}</h3>
                                                                    <div class="product-thumbnail">
                                                                        <img src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" class="img-responsive thumbnail" alt="">
                                                                    </div>
                                                                </a>

                                                                <div class="price-add-to-cart">
                                                            <span class="price">
                                                                <span class="electro-price">
                                                                    @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                        <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                                    @endif
                                                                    <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                                </span>
                                                            </span>
                                                                    @if(user() && user()->type == 'buyer')
                                                                        <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                                    @endif
                                                                </div>
                                                                <div class="hover-area">
                                                                    <div class="action-buttons">
                                                                        @if(user())
                                                                            <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                                        @endif
                                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.deals-and-tabs -->

                    <section class="products-2-1-2 animate-in-view fadeIn animated" data-animation="fadeIn">
                        <div class="container">
                            <ul class="nav nav-inline nav-justified">
                                <li class="nav-item"><a href="/products?type=discount" class="active nav-link">{{word('discounts')}}</a></li>
                                @foreach($all_cats->take(9) as $cat)
                                    <li class="nav-item"><a class="nav-link" href="/products?category={{$cat->id}}">{{$cat->l_name}}</a></li>
                                @endforeach
                            </ul>
                            <div class="columns-2-1-2">
                                <ul class="products exclude-auto-height">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getFeatured(2) ??[] as $product)
                                        <li class="product">
                                            <div class="product-outer">
                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                <a href="/product/{{$product->id}}/details">
                                                    <h3>{{$product->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img data-echo="{{$product->thumb_image}}" src="{{asset('web_assets/images/blank.gif')}}" alt="{{$product->thumb_image}}">
                                                    </div>
                                                </a>
                                                <div class="price-add-to-cart">
                                                    <span class="price">
                                                        <span class="electro-price">
                                                            @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                            @endif
                                                            <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="hover-area">
                                                    <div class="action-buttons">
                                                        @if(user())
                                                            <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                        @endif
                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @php
                                    $_product = \App\Http\Controllers\Web\HomeController::getFeatured(1)->first();
                                @endphp
                                <ul class="products exclude-auto-height product-main-2-1-2">
                                    <li class="last product">
                                        <div class="product-outer">

                                            @if($_product)

                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$_product->main_cat_id}}" rel="tag">{{$_product->main_cat->name}}</a></span>
                                                <a href="/product/{{$_product->id}}/details">
                                                    <h3>{{$_product->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img class="wp-post-image" data-echo="{{$_product->image}}" src="{{asset('web_assets/images/blank.gif')}}" alt="">

                                                    </div>
                                                </a>
                                                <div class="price-add-to-cart">
                                                    <span class="price">
                                                        <span class="electro-price">
                                                            @if($_product->price_meta->price != $_product->price_meta->sale_price)
                                                                <del><span class="amount">{{$_product->price_meta->price}} {{currency()}}</span></del>
                                                            @endif
                                                            <ins><span class="amount">{{$_product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                        </span>
                                                    </span>
                                                </div>

                                                <div class="hover-area">
                                                     <div class="action-buttons">
                                                        @if(user())
                                                            <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                        @endif
                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                    </div>
                                                </div>
                                            </div>

                                            @endif
                                        </div>
                                    </li>
                                </ul>
                                <ul class="products exclude-auto-height">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getFeatured(2) ??[] as $product)
                                        <li class="product">
                                            <div class="product-outer">
                                                <div class="product-inner">
                                                    <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                    <a href="/product/{{$product->id}}/details">
                                                        <h3>{{$product->name}}</h3>
                                                        <div class="product-thumbnail">
                                                            <img data-echo="{{$product->thumb_image}}" src="{{asset('web_assets/images/blank.gif')}}" alt="{{$product->thumb_image}}">
                                                        </div>
                                                    </a>
                                                    <div class="price-add-to-cart">
                                                        <span class="price">
                                                            <span class="electro-price">
                                                                @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                    <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                                @endif
                                                                <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <div class="hover-area">
                                                        <div class="action-buttons">
                                                            @if(user())
                                                                <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                            @endif
                                                            <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </section>

                    <section class="section-product-cards-carousel animate-in-view fadeIn animated" data-animation="fadeIn">
                        <header>
                            <h2 class="h1">{{word('best_selling')}}</h2>
                            <ul class="nav nav-inline">

                                <li class="nav-item active"><span class="nav-link">{{word('top_12')}}</span></li>
                                @foreach($all_cats->take(6) as $cat)
                                    <li class="nav-item"><a class="nav-link" href="/products?category={{$cat->id}}">{{$cat->l_name}}</a></li>
                                @endforeach
                            </ul>
                        </header>

                        <div id="home-v1-product-cards-careousel">
                            <div class="woocommerce columns-3 home-v1-product-cards-carousel product-cards-carousel owl-carousel">
                                <ul class="products columns-3">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getBestSelling(6) ??[] as $product)
                                        <li class="product product-card">
                                            <div class="product-outer">
                                            <div class="media product-inner">
                                                <a class="media-left" href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                                    <img class="media-object wp-post-image img-responsive" src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" alt="">
                                                </a>
                                                <div class="media-body">
                                                    <span class="loop-product-categories">
                                                        <a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a>
                                                    </span>
                                                    <a href="/product/{{$product->id}}/details">
                                                        <h3>{{$product->name}}</h3>
                                                    </a>
                                                    <div class="price-add-to-cart">
                                                        <span class="price">
                                                            <span class="electro-price">
                                                                @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                    <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                                @endif
                                                                <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                            </span>
                                                        </span>
                                                        @if(user() && user()->type == 'buyer')
                                                            <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                        @endif
                                                    </div>
                                                    <div class="hover-area">
                                                        <div class="action-buttons">
                                                            @if(user())
                                                                <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                            @endif
                                                            <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <ul class="products columns-3">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getBestSelling(6) as $product)
                                        <li class="product product-card">
                                            <div class="product-outer">
                                                <div class="media product-inner">
                                                    <a class="media-left" href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                                        <img class="media-object wp-post-image img-responsive" src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" alt="">
                                                    </a>
                                                    <div class="media-body">
                                                    <span class="loop-product-categories">
                                                        <a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a>
                                                    </span>
                                                        <a href="/product/{{$product->id}}/details">
                                                            <h3>{{$product->name}}</h3>
                                                        </a>
                                                        <div class="price-add-to-cart">
                                                        <span class="price">
                                                            <span class="electro-price">
                                                                @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                    <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                                @endif
                                                                <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                            </span>
                                                        </span>
                                                            @if(user() && user()->type == 'buyer')
                                                                <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                            @endif
                                                        </div>
                                                        <div class="hover-area">
                                                            <div class="action-buttons">
                                                                @if(user())
                                                                    <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                                @endif
                                                                <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </section>

                    @php
                        $banner = \App\Http\Controllers\Web\HomeController::getBanner();
                    @endphp
                    <div class="home-v1-banner-block animate-in-view fadeIn animated" data-animation="fadeIn">
                        <div class="home-v1-fullbanner-ad fullbanner-ad" style="margin-bottom: 70px">
                            <a href="{{$banner->url}}" target="_blank"><img src="{{$banner->image}}" class="img-responsive" alt=""></a>
                        </div>
                    </div>


                    <section class="home-v1-most-viewed-products-carousel section-products-carousel animate-in-view fadeIn animated" data-animation="fadeIn">
                        <header>
                            <h2 class="h1"> {{word('most_viewed')}}  </h2>
                            <a href="/products?type=most_viewed" style="font-size: 15px;" class="show-more">{{word('show_more')}}</a>
                        </header>
                        <div id="recently-added-products-carousel">
                            <div class="woocommerce columns-6">
                                <div class="products owl-carousel recently-added-products products-carousel columns-6">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getMostViewed(9)??[] as $product)
                                        <div class="product">
                                        <div class="product-outer">
                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                <a href="/product/{{$product->id}}/details">
                                                    <h3>{{$product->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" class="img-responsive thumbnail" alt="">
                                                    </div>
                                                </a>

                                                <div class="price-add-to-cart">
                                                    <span class="price">
                                                        <span class="electro-price">
                                                            @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                            @endif
                                                            <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                        </span>
                                                    </span>
                                                    @if(user() && user()->type == 'buyer')
                                                        <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                    @endif
                                                </div>
                                                <div class="hover-area">
                                                    <div class="action-buttons">
                                                        @if(user())
                                                            <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                        @endif
                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>

                    @php
                        $banner = \App\Http\Controllers\Web\HomeController::getBanner();
                    @endphp
                    <div class="home-v1-banner-block animate-in-view fadeIn animated" data-animation="fadeIn">
                        <div class="home-v1-fullbanner-ad fullbanner-ad" style="margin-bottom: 70px">
                            <a href="{{$banner->url}}" target="_blank"><img src="{{$banner->image}}" class="img-responsive" alt=""></a>
                        </div>
                    </div>

                    <section class="home-v1-discount-products-carousel section-products-carousel animate-in-view fadeIn animated" data-animation="fadeIn">
                        <header>
                            <h2 class="h1"> {{word('discounts')}}  </h2>
                            <a href="/products?type=discounts" style="font-size: 15px;" class="show-more">{{word('show_more')}}</a>
                        </header>
                        <div id="recently-added-products-carousel">
                            <div class="woocommerce columns-6">
                                <div class="products owl-carousel recently-added-products products-carousel columns-6">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getDiscount(9)??[] as $product)
                                        <div class="product">
                                            <div class="product-outer">
                                                <div class="product-inner">
                                                    <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                    <a href="/product/{{$product->id}}/details">
                                                        <h3>{{$product->name}}</h3>
                                                        <div class="product-thumbnail">
                                                            <img src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" class="img-responsive thumbnail" alt="">
                                                        </div>
                                                    </a>

                                                    <div class="price-add-to-cart">
                                                        <span class="price">
                                                            <span class="electro-price">
                                                                @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                    <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                                @endif
                                                                <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                            </span>
                                                        </span>
                                                        @if(user() && user()->type == 'buyer')
                                                            <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                        @endif
                                                    </div>
                                                    <div class="hover-area">
                                                        <div class="action-buttons">
                                                            @if(user())
                                                                <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                            @endif
                                                            <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                    @php
                        $banner = \App\Http\Controllers\Web\HomeController::getBanner();
                    @endphp
                    <div class="home-v1-banner-block animate-in-view fadeIn animated" data-animation="fadeIn">
                        <div class="home-v1-fullbanner-ad fullbanner-ad" style="margin-bottom: 70px">
                            <a href="{{$banner->url}}" target="_blank" target="_blank"><img src="{{$banner->image}}" class="img-responsive" alt=""></a>
                        </div>
                    </div>

                    <section class="home-v1-recently-added-products-carousel section-products-carousel animate-in-view fadeIn animated" data-animation="fadeIn">
                        <header>
                            <h2 class="h1"> {{word('recently_added')}}  </h2>
                            <a href="/products?type=recently_added" style="font-size: 15px;" class="show-more">{{word('show_more')}}</a>
                            <div class="owl-nav">
                                <a href="#recentyly-products-carousel-prev" data-target="#recently-added-products-carousel" class="slider-prev"><i class="fa fa-angle-left"></i></a>
                                <a href="#recently-products-carousel-next" data-target="#recently-added-products-carousel" class="slider-next"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </header>
                        <div id="recently-added-products-carousel">
                            <div class="woocommerce columns-6">
                                <div class="products owl-carousel recently-added-products products-carousel columns-6">
                                    @foreach(\App\Http\Controllers\Web\HomeController::getRecentlyAdded(9)??[] as $product)
                                        <div class="product">
                                        <div class="product-outer">
                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                <a href="/product/{{$product->id}}/details">
                                                    <h3>{{$product->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img src="{{asset('web_assets/images/blank.gif')}}" data-echo="{{$product->image}}" class="img-responsive thumbnail" alt="">
                                                    </div>
                                                </a>

                                                <div class="price-add-to-cart">
                                                    <span class="price">
                                                        <span class="electro-price">
                                                            @if($product->price_meta->price != $product->price_meta->sale_price)
                                                                <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                            @endif
                                                            <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                        </span>
                                                    </span>
                                                    @if(user() && user()->type == 'buyer')
                                                        <a rel="nofollow" class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                    @endif
                                                </div>
                                                <div class="hover-area">
                                                    <div class="action-buttons">
                                                        @if(user())
                                                            <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                                        @endif
                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>

        </div>
    </div>
@endsection
