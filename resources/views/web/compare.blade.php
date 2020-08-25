@extends('web.layout')
@section('content')
    <div tabindex="-1" class="site-content" id="content">
        <div class="container">
            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('compare')}}</nav>
            <div class="content-area" id="primary">
                <main class="site-main" id="main">
                    <article class="post-2917 page type-page status-publish hentry" id="post-2917">
                        <div itemprop="mainContentOfPage" class="entry-content">
                            <div class="table-responsive">
                                <table class="table table-compare compare-list">
                                    <tbody>
                                    <tr>
                                        <th>{{word('products')}}</th>
                                        @foreach($products as $product)
                                            <td class="td_{{$product->id}}">
                                                <a class="product" href="/product/{{$product->id}}/details">
                                                    <div class="product-image">
                                                        <div class="">
                                                            <img width="250" height="232" alt="1" class="wp-post-image" src="{{$product->image}}">
                                                        </div>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title">{{$product->name}}</h3>
                                                        <h4 class="rate-stars">{!! $product->get_stars($product->rate) !!}</h4>
                                                    </div>
                                                </a>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>{{word('price')}}</th>
                                        @foreach($products as $product)
                                            <td class="td_{{$product->id}}">
                                                @if($product->type == 'static')
                                                    <span class="electro-price">
                                                        @if($product->price_meta->price != $product->price_meta->sale_price)
                                                            <del><span class="amount">{{$product->price_meta->price}} {{currency()}}</span></del>
                                                        @endif
                                                        <ins><span class="amount">{{$product->price_meta->sale_price}} {{currency()}}</span></ins>
                                                    </span>
                                                @else
                                                    @foreach($product->variations_data as $data)
                                                        <span class="electro-price">
                                                            {{$data->options_str}} |
                                                            @if($data->price != $data->sale_price)
                                                                <del><span class="amount">{{$data->price}} {{currency()}}</span></del>
                                                            @endif
                                                        <ins><span class="amount">{{$data->sale_price}} {{currency()}}</span></ins>
                                                        </span>
                                                        <br/>
                                                        <br/>
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>{{word('availability')}}</th>
                                        @foreach($products as $product)
                                            @if($product->type == 'static')
                                            <td class="td_{{$product->id}}">
                                                @if($product->price_meta->count)
                                                    <span class="in-stock">{{word('in_stock')}} ({{$product->price_meta->count}})</span>
                                                @else
                                                    <span class="out-stock">{{word('out_stock')}}</span>
                                                @endif
                                            </td>
                                            @else
                                            <td class="td_{{$product->id}}">
                                                @foreach($product->variations_data as $data)
                                                    @if($data->count)
                                                        <span class="in-stock">{{$data->options_str}} ({{$data->count}})</span>
                                                    @else
                                                        <span class="out-stock">{{$data->options_str}} ({{word('out_stock')}}) </span>
                                                    @endif
                                                    <br/>
                                                    <br/>
                                                @endforeach
                                            </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>{{word('desc')}}</th>
                                        @foreach($products as $product)
                                            <td class="td_{{$product->id}}">
                                                <ul style="text-align:left; margin-bottom: 0;">
                                                    <li><span class="a-list-item">{{$product->desc}}</span></li>
                                                </ul>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>{{word('customizations')}}</th>
                                        @foreach($products as $product)
                                            <td class="td_{{$product->id}}">
                                                <ul style="text-align:left; margin-bottom: 0;">
                                                    <li><span class="a-list-item">{{$product->custom != '' ? $product->custom : word('none')}}</span></li>
                                                </ul>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th>&nbsp;</th>
                                        @foreach($products as $product)
                                            <td class="text-center td_{{$product->id}}">
                                                <a href="javascript:void(0);" title="{{word('remove')}}" class="add-to-compare-link remove-icon" data-id="{{$product->id}}"><i class="fa fa-times"></i></a>
                                            </td>
                                        @endforeach
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>
@endsection
