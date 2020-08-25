@extends('web.layout')
@section('content')
    <body class="single-product full-width">
        <div id="content" class="site-content" tabindex="-1">
            <div class="container">
                <nav class="woocommerce-breadcrumb">
                    <a href="/">{{word('home')}}</a>
                    <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                    <a href="/products?category={{$product->main_cat_id}}">{{$product->main_cat->name}}</a>
                    <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                    <a href="/products?category={{$product->sub_cat_id}}">{{$product->sub_cat->name}}</a>
                </nav>
                <div id="primary" class="content-area">
                    <main id="main" class="site-main">
                        <div class="product">
                            <div class="single-product-wrapper">
                                <div class="product-images-wrapper">
                                    @if($product->discount)
                                        <span class="onsale">{{word('sale!')}}</span>
                                    @endif
                            <div class="images electro-gallery" style="direction: ltr">
                                <div class="slider product-slider">
                                    <div>
                                        <img src="{{$product->image}}">
                                        </div>
                                        @foreach($product->images as $image)
                                            <div>
                                                <img src="{{$image}}">
                                            </div>
                                        @endforeach
                                   </div>
                                   <div class="slide2r slider-nav">
                                       <div>
                                            <img src="{{$product->thumb_image}}">
                                       </div>
                                       @foreach($product->images as $image)
                                           <div>
                                               <img src="{{$image}}">
                                           </div>
                                       @endforeach
                                   </div>
                             </div>
                             </div>
                                  <div class="summary entry-summary">
                                    <span class="loop-product-categories">
                                        <a href="/products?category={{$product->sec_cat_id}}" rel="tag">{{$product->sec_cat->name}}</a>
                                    </span>
                                    <h1 itemprop="name" class="product_title entry-title">{{$product->name}}</h1>
                                    <div class="woocommerce-product-rating">
                                        <span title="{{$product->rate_count}} {{word('out_of_5')}}">{!! $product->get_stars($product->rate) !!}</span> ({{$product->rate_count}} {{word('customer_reviews')}})
                                    </div>
                                    <div class="brand">
                                        <a href="/seller/{{$product->seller_id}}/profile">
                                            <b>{{$seller->company_name}}</b>
                                        </a>
                                    </div>

                                    <div class="availability {{$product->price_meta->count ? 'in-stock' : 'out-stock-no-box'}}">{{word('availability')}} : <span>{{$product->price_meta->count ? word('in_stock') : word('out_of_stock')}}</span></div><!-- .availability -->

                                    <hr class="single-product-title-divider" />
                                      <div class="action-buttons">
                                          @if(user())
                                              <a href="javascript:void(0)" rel="nofollow" class="add_to_wishlist" data-id="{{$product->id}}"><span class="fa fa-heart{{$product->is_favorite ? '-o' : ''}}"></span> {{word('wishlist')}}</span></a>
                                          @endif
                                          <a href="javascript:void(0)" class="add-to-compare-link" data-id="{{$product->id}}">{{word('compare')}}</a>
                                      </div>

                                    <div itemprop="description">
                                        @if($product->type == 'variable')
                                            <ul>
                                                @foreach($product->variations as $variation)
                                                    <li>{{$variation->name}} ( {{implode(' - ',$variation->options->toArray())}} )</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                                        <p class="price">
                                            <span class="electro-price" id="single-price">
                                                 @if($product->price_meta->price != $product->price_meta->sale_price)
                                                    <del><span class="amount">&#36;{{$product->price_meta->price}} {{country()->currency}}</span></del>
                                                @endif
                                                <ins><span class="amount"> {{$product->price_meta->sale_price}} {{country()->currency}}</span></ins>
                                            </span>
                                        </p>
                                        <meta itemprop="price" content="{{$product->price_meta->sale_price}}" />
                                        <meta itemprop="priceCurrency" content="{{country()->currency}}" />
                                        <link itemprop="availability" href="http://schema.org/InStock" />
                                    </div>

                                    <form class="variations_form cart" method="post" action="/cart/store">
                                        {{csrf_field()}}
                                        <input type="hidden" name="product_id" value="{{$product->id}}">
                                        @if($product->type == 'variable')
                                            <table class="variations">
                                                <tbody>
                                                <tr>
                                                    <td class="label"><label style="color: black; font-size: 20px;">{{word('type')}}</label></td>
                                                    <td class="value">
                                                        <select class="" name="product_variation_id" id="variation_select">
                                                            <option disabled>{{word('choose_from_below')}}</option>
                                                            @foreach($product->variations_data as $data)
                                                                <option value="{{$data->id}}" data-prices="{{$data}}">{{$data->options_str}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        @else
                                            <input type="hidden" name="product_variation_id" value="0">
                                        @endif
                                        <div class="single_variation_wrap">
                                            <div class="woocommerce-variation single_variation"></div>
                                            <div class="woocommerce-variation-add-to-cart variations_button">
                                                <div class="quantity">
                                                    <label>{{word('quantity')}}:</label>
                                                    <input type="number" name="count" id="count_input" value="1" min="1" max="{{$product->price_meta->count}}" title="{{word('quantity')}}" class="input-text qty text"/>
                                                </div>
                                                @include('error',['input' => 'count'])
                                                <br/>
                                                <button type="submit" class="single_add_to_cart_button button">{{word('add_to_cart')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="woocommerce-tabs wc-tabs-wrapper">
                                    <ul class="nav nav-tabs electro-nav-tabs tabs wc-tabs" role="tablist">
                                        <li class="nav-item description_tab">
                                            <a href="#tab-description" class="{{$errors->has('rate') || $errors->has('text') || request('page') ? '' : 'active'}}" data-toggle="tab">{{word('desc')}}</a>
                                        </li>
                                        @if($product->custom != '')
                                            <li class="nav-item specification_tab">
                                                <a href="#tab-specification" data-toggle="tab">{{word('customizations')}}</a>
                                            </li>
                                        @endif
                                        <li class="nav-item reviews_tab">
                                            <a href="#tab-reviews" data-toggle="tab" class="{{$errors->has('rate') || $errors->has('text') || request('page') ? 'active' : ''}}">{{word('reviews')}}</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane {{$errors->has('rate') || $errors->has('text') || request('page') ? '' : 'active in'}} panel entry-content wc-tab" id="tab-description">
                                            <div class="electro-description" style="white-space: pre-wrap;">{!! $product->desc !!}</div>
                                        </div>
                                        @if($product->custom != '')
                                            <div class="tab-pane panel entry-content wc-tab" style="white-space: pre-wrap;" id="tab-specification">{!! $product->custom !!}</div>
                                        @endif
                                        <div class="tab-pane {{$errors->has('rate') || $errors->has('text') || request('page') ? 'active in' : ''}} panel entry-content wc-tab" id="tab-reviews">
                                            <div id="reviews" class="electro-advanced-reviews">
                                                <div class="advanced-review row">
                                                    <div class="col-xs-12 col-md-6">
                                                        <h2 class="based-title">{{word('based_on')}} {{$product->rates_count}} {{word('rates')}}</h2>
                                                        <div class="avg-rating">
                                                            <span class="avg-rating-number">{{$product->rate}}</span> {{word('over_all')}}
                                                        </div>
                                                        <div class="rating-histogram">
                                                            <div class="rating-bar">
                                                                @php
                                                                    $rate = \App\Models\Product::getRatePercentage($product->id,5);
                                                                @endphp
                                                                <span class="rate-stars" style="float: {{lang() == 'ar' ? 'right' : 'left'}};">{!! $product->get_stars(5) !!}</span>
                                                                <div class="star-rating" title="{{word('rated_1_of_5')}}">
                                                                    <span style="width:{{$rate['percent_1']}}%"></span>
                                                                </div>
                                                                <div class="rating-percentage-bar">
                                                                    <span style="width:'{{$rate['percent_2']}}'%" class="rating-percentage"></span>
                                                                </div>
                                                                <div class="rating-count">{{\App\Models\Product::getRateCount($product->id,5)}}</div>
                                                            </div>

                                                            <div class="rating-bar">
                                                                @php
                                                                    $rate = \App\Models\Product::getRatePercentage($product->id,4);
                                                                @endphp
                                                                <span class="rate-stars" style="float: {{lang() == 'ar' ? 'right' : 'left'}};">{!! $product->get_stars(4) !!}</span>
                                                                <div class="star-rating" title="{{word('rated_1_of_5')}}">
                                                                    <span style="width:{{$rate['percent_1']}}%"></span>
                                                                </div>
                                                                <div class="rating-percentage-bar">
                                                                    <span style="width:'{{$rate['percent_2']}}'%" class="rating-percentage"></span>
                                                                </div>
                                                                <div class="rating-count">{{\App\Models\Product::getRateCount($product->id,4)}}</div>
                                                            </div>

                                                            <div class="rating-bar">
                                                                @php
                                                                    $rate = \App\Models\Product::getRatePercentage($product->id,3);
                                                                @endphp
                                                                <span class="rate-stars" style="float: {{lang() == 'ar' ? 'right' : 'left'}};">{!! $product->get_stars(3) !!}</span>
                                                                <div class="star-rating" title="{{word('rated_1_of_5')}}">
                                                                    <span style="width:{{$rate['percent_1']}}%"></span>
                                                                </div>
                                                                <div class="rating-percentage-bar">
                                                                    <span style="width:'{{$rate['percent_2']}}'%" class="rating-percentage"></span>
                                                                </div>
                                                                <div class="rating-count">{{\App\Models\Product::getRateCount($product->id,3)}}</div>
                                                            </div>

                                                            <div class="rating-bar">
                                                                @php
                                                                    $rate = \App\Models\Product::getRatePercentage($product->id,2);
                                                                @endphp
                                                                <span class="rate-stars" style="float: {{lang() == 'ar' ? 'right' : 'left'}};">{!! $product->get_stars(2) !!}</span>
                                                                <div class="star-rating" title="{{word('rated_1_of_5')}}">
                                                                    <span style="width:{{$rate['percent_1']}}%"></span>
                                                                </div>
                                                                <div class="rating-percentage-bar">
                                                                    <span style="width:'{{$rate['percent_2']}}'%" class="rating-percentage"></span>
                                                                </div>
                                                                <div class="rating-count">{{\App\Models\Product::getRateCount($product->id,2)}}</div>
                                                            </div>

                                                            <div class="rating-bar">
                                                                @php
                                                                    $rate = \App\Models\Product::getRatePercentage($product->id,1);
                                                                @endphp
                                                                <span class="rate-stars" style="float: {{lang() == 'ar' ? 'right' : 'left'}};">{!! $product->get_stars(1) !!}</span>
                                                                <div class="star-rating" title="{{word('rated_1_of_5')}}">
                                                                    <span style="width:{{$rate['percent_1']}}%"></span>
                                                                </div>
                                                                <div class="rating-percentage-bar">
                                                                    <span style="width:'{{$rate['percent_2']}}'%" class="rating-percentage"></span>
                                                                </div>
                                                                <div class="rating-count">{{\App\Models\Product::getRateCount($product->id,1)}}</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if(user())
                                                        <div class="col-xs-12 col-md-6">
                                                            <div id="review_form_wrapper">
                                                                <div id="review_form">
                                                                    <div id="respond" class="comment-respond">
                                                                        <h3 id="reply-title" class="comment-reply-title">{{word('add_review')}}</h3>
                                                                        <form action="/product/rate" method="post" id="commentform" class="comment-form">
                                                                            {{csrf_field()}}
                                                                            <p class="comment-form-rating">
                                                                                <label>{{word('your_rate')}}</label>
                                                                            </p>
                                                                            <fieldset class="rating">
                                                                                <input type="radio" id="star5" class="rate_star" name="rating" value="5" /><label class="full" for="star5" title="Awesome - 5 stars"></label>
                                                                                <input type="radio" id="star4" class="rate_star" name="rating" value="4" /><label class="full" for="star4" title="Pretty good - 4 stars"></label>
                                                                                <input type="radio" id="star3" class="rate_star" name="rating" value="3" /><label class="full" for="star3" title="Meh - 3 stars"></label>
                                                                                <input type="radio" id="star2" class="rate_star" name="rating" value="2" /><label class="full" for="star2" title="Kinda bad - 2 stars"></label>
                                                                                <input type="radio" id="star1" class="rate_star" name="rating" value="1" /><label class="full" for="star1" title="Sucks big time - 1 star"></label>
                                                                            </fieldset>
                                                                            @include('error',['input' => 'rate'])
                                                                            <p class="comment-form-comment">
                                                                                <label for="comment">{{word('your_review')}}</label>
                                                                                <textarea id="comment" name="text" cols="45" rows="8" aria-required="true">{{old('text')}}</textarea>
                                                                            </p>
                                                                            @include('error',['input' => 'text'])
                                                                            <input type="hidden" name="id" value="{{$product->id}}">
                                                                            <p class="form-submit">
                                                                                <input name="submit" type="submit" id="submit" class="submit" value="{{word('submit')}}" />
                                                                            </p>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    @endif
                                                </div><!-- /.row -->

                                                <div id="comments">
                                                    <ol class="commentlist">
                                                        @forelse($product->rates as $rate)
                                                            <li itemprop="review" class="comment even thread-even depth-1">
                                                                <div id="comment-390" class="comment_container">
                                                                    <div class="comment-text">
                                                                        {!! $product->get_stars($rate->rate) !!}
                                                                        <div itemprop="description" class="description">
                                                                            <p>{{$rate->text}}</p>
                                                                        </div>
                                                                        <p class="meta">
                                                                            <strong itemprop="author">{{$rate->user->name}}</strong> &ndash; <time itemprop="datePublished" datetime="{{$rate->created_at}}">{{$rate->created_at->toDateString()}}</time>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @empty
                                                            <h2>{{word('no_rates_yet')}}</h2>
                                                        @endforelse
                                                    </ol>
                                                </div>
                                                {{$product->rates->links()}}
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                            </div>
                            <br/>
                            <br/>
                            @if($similars->count())
                            <div class="related products">
                                <h2>{{word('related_products')}}</h2>
                                <ul class="products columns-5">
                                    @foreach($similars as $similar)
                                        <li class="product">
                                            <div class="product-outer">
                                            <div class="product-inner">
                                                <span class="loop-product-categories"><a href="/products?category={{$product->sec_cat_id}}" rel="tag">{{$product->sec_cat->name}}</a></span>
                                                <a href="/product/{{$similar->id}}/details">
                                                    <h3>{{$similar->name}}</h3>
                                                    <div class="product-thumbnail">
                                                        <img data-echo="{{$similar->image}}" src="{{asset('web_assets/images/blank.gif')}}" alt="">
                                                    </div>
                                                </a>

                                                <div class="price-add-to-cart">
                                                    <span class="electro-price">
                                                        @if($similar->price_meta->price != $similar->price_meta->sale_price)
                                                               <del><span class="amount">{{$similar->price_meta->price}} {{country()->currency}}</span></del>
                                                        @endif
                                                        <ins><span class="amount">{{$similar->price_meta->sale_price}} {{country()->currency}}</span></ins>
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
                            @endif
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </body>
@endsection
@section('script')
    <script>
        $('#variation_select').on('change', function()
        {
            $price = $(this).children("option:selected").data('prices');

            if($price.sale == 1)
            {
                $text = '<del><span class="amount">&#36;' + $price.price + '{{country()->currency}}</span></del>\n<ins><span class="amount">&#36;'+ $price.sale_price +'{{country()->currency}}</span></ins>';
            }
            else
            {
                $text = '<ins><span class="amount">&#36;'+ $price.price +'{{country()->currency}}</span></ins>';
            }

            $('#single-price').html('');
            $('#single-price').html($text);

            $('#count_input').attr('max',$price.count);
        });
    </script>
    <script>
           jQuery(document).ready(function($)
           {
               $('.slider').slick({
               dots: false,
               infinite: true,
               speed: 500,
               slidesToShow: 1,
               slidesToScroll: 1,
               autoplay: false,
               autoplaySpeed: 2000,
               arrows: false,
               asNavFor: '.slider-nav',
               responsive:
               [
                   {
                       breakpoint: 600,
                       settings:
                           {
                               slidesToShow: 1,
                               slidesToScroll: 1
                           }
                   },
                   {
                       breakpoint: 400,
                       settings:
                       {
                          arrows: false,
                          slidesToShow: 1,
                          slidesToScroll: 1
                       }
                   }
               ]
           });
           $('.slider-nav').slick(
           {
               slidesToShow: 4,
               slidesToScroll: 1,
               asNavFor: '.slider',
               dots: false,
               focusOnSelect: true
           });
           });
    </script>
@endsection
