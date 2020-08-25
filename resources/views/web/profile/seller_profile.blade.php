@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">

            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i
                            class="fa fa-angle-right"></i></span>{{word('seller')}}</nav>

            <div id="primary" class="content-area order-details">
                <main id="main" class="site-main">
                    <article class="page type-page status-publish hentry">
                        <header class="entry-header"><h1 itemprop="name" class="entry-title">{{$user->name}}</h1>
                        </header>
                        <div class="order-product">
                            <div class="supplier-details">
                                <img src="{{$user->image}}" class="img-circle" alt="supplier-img">
                                <p class="supplier-name">{{$user->company_name}}</p>


                                <p class="supplier-country"><span
                                            class="fa fa-map-marker"></span> {{$user->country->name}}
                                    , {{$user->city->name}}
                                    <br>
                                    <span class="fa fa-envelope"></span> {{$user->email}}
                                    <br>
                                    <span class="fa fa-phone"></span> {{$user->phone}}
                                    <br>
                                    <span class="fa fa-whatsapp"></span> {{$user->whatsapp}}
                                </p>


                            </div>
                            @if(user() && user()->id != $user->id)
                                <div class="footer-newsletter">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <form method="post" action="/profile/message/store_profile"
                                                      id="chat_form">
                                                    {{csrf_field()}}
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="text_msg"
                                                               name="text" placeholder="{{word('type_ur_message')}}">
                                                        @include('error',['input' => 'text'])
                                                        <input type="hidden" name="target_id" value="{{$user->id}}">
                                                        <span class="input-group-btn">
                                                            <button class="btn btn-secondary" type="button"
                                                                    onclick="validate_msg()">{{word('send_msg')}}</button>
                                                        </span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="cart-collaterals  supplier-product">
                            <h2>{{word('products')}}</h2>
                            <ul class="products columns-3">
                                @foreach($products as $product)
                                    <li class="product product-card">
                                        <div class="product-outer" style="height: 215px;">
                                            <div class="media product-inner">
                                                <a class="media-left" href="/product/{{$product->id}}/details"
                                                   title="{{$product->name}}">
                                                    <img class="media-object wp-post-image img-responsive"
                                                         src="{{$product->image}}" alt="">
                                                </a>
                                                <div class="media-body">
                                                <span class="loop-product-categories">
                                                    <a href="/products?category={{$product->main_cat_id}}"
                                                       rel="tag">{{$product->main_cat->name}}</a>
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
                                                            <a rel="nofollow"
                                                               class="button add_to_cart_button {{$product->is_cart ? 'added_cart' : 'no_cart'}}"></a>
                                                        @endif
                                                    </div>
                                                    <div class="hover-area">
                                                        @if(user())
                                                            <div class="action-buttons">
                                                                @if($product->is_favorite)
                                                                    <a href="javascript:void(0)" class="add_to_wishlist"
                                                                       data-id="{{$product->id}}">{{word('remove_from_wishlist')}}</a>
                                                                @else
                                                                    <a href="javascript:void(0)" class="add_to_wishlist"
                                                                       data-id="{{$product->id}}">{{word('add_to_wishlist')}}</a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            {{$products->links('vendor.pagination.bootstrap-4')}}
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function validate_msg() {
            if ($('#text_msg').val() != '') $('#chat_form').submit();
        }
    </script>
@endsection
