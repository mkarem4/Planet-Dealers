@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a>
                <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                {{word('products')}}
            </nav>
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <header class="page-header">
                        <h1 class="page-title">{{word('search_result')}}</h1>
                        <p class="woocommerce-result-count">{{$products->total()}} {{word('products')}}</p>
                    </header>

                    <div class="shop-control-bar">
                        <form class="woocommerce-ordering" method="get" style="float:none;">
                            <div style="float : {{lang() == 'ar' ? 'right' : 'left'}};">
                                <select name="order_by" class="orderby form-submit">
                                    <option value="default" {{isset($inputs['order_by']) && $inputs['order_by'] == 'default' ? 'selected' : ''}}>{{word('default')}}</option>
                                    <option value="featured" {{isset($inputs['order_by']) && $inputs['order_by'] == 'featured' ? 'selected' : ''}}>{{word('sort_by')}} {{word('featured')}}</option>
                                    <option value="best_selling" {{isset($inputs['order_by']) && $inputs['order_by'] == 'best_selling' ? 'selected' : ''}}>{{word('sort_by')}} {{word('best_selling')}}</option>
                                    <option value="views" {{isset($inputs['order_by']) && $inputs['order_by'] == 'views' ? 'selected' : ''}}>{{word('sort_by')}} {{word('views_count')}}</option>
                                    <option value="rating" {{isset($inputs['order_by']) && $inputs['order_by'] == 'rating' ? 'selected' : ''}}>{{word('sort_by')}} {{word('rating')}}</option>
                                    <option value="discount" {{isset($inputs['order_by']) && $inputs['order_by'] == 'discount' ? 'selected' : ''}}>{{word('sort_by')}} {{word('discount')}}</option>
                                    <option value="latest" {{isset($inputs['order_by']) && $inputs['order_by'] == 'latest' ? 'selected' : ''}}>{{word('sort_by')}} {{word('latest')}}</option>
                                    <option value="oldest" {{isset($inputs['order_by']) && $inputs['order_by'] == 'oldest' ? 'selected' : ''}}>{{word('sort_by')}} {{word('oldest')}}</option>
                                    <option value="price_desc" {{isset($inputs['order_by']) && $inputs['order_by'] == 'price_desc' ? 'selected' : ''}}>{{word('sort_by')}} {{word('price_high_low')}}</option>
                                    <option value="price_asc" {{isset($inputs['order_by']) && $inputs['order_by'] == 'price_asc' ? 'selected' : ''}}>{{word('sort_by')}} {{word('price_low_high')}}</option>
                                </select>
                                <select name="category"  class="electro-wc-wppp-select c-select form-submit">
                                    <option value='all' selected='selected'>{{word('all_cats')}}</option>
                                    @foreach($all_cats as $cat)
                                        <option class="level-0" value="{{$cat->id}}">{{$cat->l_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="float : {{lang() == 'ar' ? 'left' : 'right'}};">
                                <select name="show" id="show_select" class="electro-wc-wppp-select c-select form-submit" style="float : {{lang() == 'ar' ? 'left' : 'right'}};">
                                    <option value="21" {{isset($inputs['show']) && $inputs['show'] == '21' ? 'selected' : ''}}>{{word('show_21')}}</option>
                                    <option value="all" {{isset($inputs['show']) && $inputs['show'] == 'all' ? 'selected' : ''}}>{{word('show_all')}}</option>
                                </select>
                            </div>

                        </form>
                    </div>

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
                                                <a rel="nofollow" href="single-product.html" class="button add_to_cart_button">Add to cart</a>
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


                                @if(!isset($inputs['show']) || (isset($inputs['show']) && $inputs['show'] != 'all'))
                                    {{$products->links('vendor.pagination.bootstrap-4')}}
                                @endif
                            </ul>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('.form-submit').on('change',function()
        {
            $('.woocommerce-ordering').submit();

        });
    </script>
@endsection
