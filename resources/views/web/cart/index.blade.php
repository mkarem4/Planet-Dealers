@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">

            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a>
                <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                {{word('cart')}}
            </nav>

            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <article class="page type-page status-publish hentry">
                        <header class="entry-header"><h1 itemprop="name" class="entry-title">{{word('cart')}}</h1></header>
                            <table class="shop_table shop_table_responsive cart">
                                <thead>
                                <tr>
                                    <th class="product-remove">&nbsp;</th>
                                    <th class="product-thumbnail">&nbsp;</th>
                                    <th class="product-name">{{word('product')}}</th>
                                    <th class="product-price">{{word('price')}}</th>
                                    <th class="product-quantity">{{word('quantity')}}</th>
                                    <th class="product-subtotal">{{word('total')}}</th>
                                </tr>
                                </thead>
                                    <tbody>
                                        <form method="post" action="/cart/update">
                                            {{csrf_field()}}
                                            @forelse($carts as $cart)
                                                <tr class="cart_item">
                                                    <td class="product-remove">
                                                        <a class="remove remove_cart" href="javascript:void(0)" data-info="{{$cart}}">×</a>
                                                    </td>
                                                    <td class="product-thumbnail">
                                                        <a href="/product/{{$cart->product_id}}/details"><img width="180" height="180" src="{{$cart->product->image}}" alt=""></a>
                                                    </td>

                                                    <td data-title="Product" class="product-name">
                                                        <a href="/product/{{$cart->product_id}}/details">{{$cart->product->name}}</a>
                                                    </td>

                                                    <td data-title="Price" class="product-price">
                                                        <span class="amount">{{round($cart->price / $cart->count,2)}} {{currency()}}</span>
                                                    </td>

                                                    <td data-title="Quantity" class="product-quantity">
                                                        <div class="quantity buttons_added">
                                                            <input type="button" class="minus" data-id="{{$cart->id}}" data-count="{{$cart->product->price_meta->count}}" value="-">
                                                            <label>{{word('quantity')}}:</label>
                                                                <input type="number" size="4" class="input-text qty text" id="count_{{$cart->id}}" title="{{word('quantity')}}" value="{{$cart->count}}" name="cart[{{$cart->id}}]" max="{{$product->price_meta->count}}" min="1" step="1">
                                                            <input type="button" class="plus" data-id="{{$cart->id}}" data-count="{{$cart->product->price_meta->count}}" value="+">
                                                        </div>
                                                    </td>

                                                    <td data-title="Total" class="product-subtotal">
                                                        <span class="amount">{{$cart->price}} {{currency()}}</span>
                                                    </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">{{word('no_items')}}</td>
                                                </tr>
                                            @endforelse
                                            <tr>
                                                <td class="actions" colspan="6">
                                                    <input type="submit" value="{{word('update_cart')}}" class="button">
                                                </td>
                                            </tr>
                                        </form>
                                    </tbody>
                            </table>
                        <div class="cart-collaterals">
                            <div class="cart_totals ">
                                <h2>{{word('cart_total')}}</h2>

                                <table class="shop_table shop_table_responsive">
                                    <tbody>
                                        <tr class="cart-subtotal">
                                            <th>{{word('sub_total')}}</th>
                                            <td data-title="Subtotal"><span class="amount">{{$totals['cart']}} {{currency()}}</span></td>
                                        </tr>
                                        <tr class="cart-subtotal">
                                            <th>{{word('tax_fee')}}</th>
                                            <td data-title="Subtotal"><span class="amount">{{$totals['tax']}} {{currency()}}</span></td>
                                        </tr>
                                        <tr class="order-total">
                                            <th>{{word('total')}}</th>
                                            <td data-title="Total"><strong><span class="amount">{{$totals['total']}} {{currency()}}</span></strong> </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div style="float: {{lang() == 'ar' ? 'left' : 'right'}};">
                                    <form method="post" action="/cart/checkout">
                                        {{csrf_field()}}
                                        <select name="address_id">
                                            @if(user()->addresses->count())
                                                <option selected disabled>{{word('choose_address')}}</option>
                                                @foreach(user()->addresses as $address)
                                                    <option value="{{$address->id}}">{{$address->text}}</option>
                                                @endforeach
                                            @else
                                                <option selected disabled>{{word('add_address_first')}}</option>
                                            @endif
                                        </select>
                                        <br/>
                                        @include('error',['input' => 'address_id'])
                                        <br/>
                                        <button type="submit" class="checkout-button button alt wc-forward" {{! user()->addresses->count() ? 'disabled' : ''}}>{{word('checkout')}}</button>
                                    </form>
                                </div>
                                <br/>
                                <br/>
                            </div>
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $('.plus').on('click', function()
        {
            $id = $(this).data('id');
            $max = $(this).data('count');
            $old_val = $('#count_' + $id).val();

            $val = parseInt($old_val) + 1;

            if($val <= $max) $('#count_'+$id).val($val);
        });


        $('.minus').on('click', function()
        {
            $id = $(this).data('id');
            $min = $(this).data('count');
            $old_val = $('#count_' + $id).val();

            $val = parseInt($old_val) - 1;

            if($val > 0) $('#count_'+$id).val($val);
        });
    </script>
@endsection
