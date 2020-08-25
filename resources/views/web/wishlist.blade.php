@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a>
                <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                {{word('wishlist')}}
            </nav>
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <header class="page-header">
                        <h1 class="page-title">{{word('wishlist')}}</h1>
                        <p class="woocommerce-result-count">{{$products->total()}} {{word('products')}}</p>
                    </header>
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="grid" aria-expanded="true">
                            <ul class="products columns-3">
                                @foreach($products as $product)
                                    <li class="product">
                                        <div class="product-outer">
                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$product->main_cat_id}}" rel="tag">{{$product->main_cat->name}}</a></span>
                                                <a href="/product/{{$product->id}}/details">
                                                    <h3>{{$product->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img data-echo="{{$product->image}}" src="{{asset('web_assets/images/blank.gif')}}" alt="">
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
                                                        <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart-o"></span> {{word('remove')}}</span></a>
                                                        <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                {{$products->links('vendor.pagination.bootstrap-4')}}
                            </ul>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
