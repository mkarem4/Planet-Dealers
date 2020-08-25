@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">

            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('orders')}}<span class="delimiter"><i class="fa fa-angle-right"></i></span>#{{$order->code}}</nav>

            <div id="primary" class="content-area order-details">
                <main id="main" class="site-main">
                    <article class="page type-page status-publish hentry">
                        <header class="entry-header"><h1 itemprop="name" class="entry-title">{{word('order_details')}}</h1></header>
                        <table data-token="" data-id="" data-page="1" data-per-page="5" data-pagination="no" class="shop_table cart wishlist_table table-responsive">
                            <thead>
                                <tr>
                                    <th class="product-name">
                                        <span class="nobr">#</span>
                                    </th>
                                    <th class="product-price">
                                        <span class="nobr">{{word('date')}}</span>
                                    </th>
                                    <th class="product-stock-stauts">
                                        <span class="nobr">{{word('products_count')}}</span>
                                    </th>
                                    <th class="product-add-to-cart">{{word('status')}}</th>
                                    <th class="product-add-to-cart">{{word('transfer_image')}}</th>
                                    <th>{{word('address')}}</th>
                                    <th>{{word('seller_bank_info')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="product-name" data-label="OrderNumber">
                                    {{$order->code}}
                                </td>
                                <td class="product-price" data-label="OrderDate">
                                    <span class="electro-price">
                                        <span class="amount">
                                            {{$order->created_at->toTimeString()}}<br/>
                                            {{$order->created_at->toDateString()}}
                                        </span>
                                    </span>
                                </td>

                                <td class="product-stock-status" data-label="ProductCount">
                                    <span class="in-stock">{{$order->items_count}}</span>
                                </td>

                                <td class="product-add-to-cart" data-label="Status">
                                    <span class="Request-status">{{word($order->status)}}</span>
                                </td>
                                <td class="product-add-to-cart" data-label="Transfer image">
                                    @if($order->image == '')
                                        <span class="Request-status" style="background-color: grey;">{{word('not_yet')}}</span>
                                    @else
                                        <a href="{{$order->image}}" target="_blank"><img width="150px" height="150px" class="table-image" src="{{$order->image}}"></a>
                                    @endif
                                </td>
                                <td data-label="SupplierInfo">
                                    <span class="suppliername">{{$order->address->text}}</span>
                                    <span class="supplieraddress"> {{$order->address->notes}}</span>
                                </td>
                                <td data-label="SupplierInfo">
                                    <span class="suppliername">{{$order->seller->name}}</span>
                                    <span class="supplieraddress"> {!! $order->seller->bank_info !!}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <br/>
                        <br/>
                        <br/>
                        @if(user()->id == $order->seller_id && isset($next) && ! in_array($order->status,['delivered','canceled','declined']))
                            <div class="row">
                                <div class="col-md-{{$order->status == 'pending' ? '6' : '12'}}">
                                    <div role="form" class="wpcf7">
                                        <div class="screen-reader-response"></div>
                                        <form action="/profile/order/change_status" method="post" class="wpcf7-form order-details-form" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{$order->id}}">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <div class="container2">
                                                        <input type="hidden" name="status" value="next">
                                                        <input type="submit" value="{{word('change_to')}} {{word($next)}}" class="wpcf7-form-control wpcf7-submit"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @if($order->status == 'pending')
                                    <div class="col-md-6">
                                        <div role="form" class="wpcf7">
                                            <div class="screen-reader-response"></div>
                                            <form action="/profile/order/change_status" method="post" class="wpcf7-form order-details-form" enctype="multipart/form-data">
                                                {{csrf_field()}}
                                                <input type="hidden" name="id" value="{{$order->id}}">
                                                <div class="form-group row">
                                                    <div class="col-md-12">
                                                        <div class="container2">
                                                            <input type="hidden" name="status" value="declined">
                                                            <input type="submit" value="{{word('decline_order')}}" class="wpcf7-form-control wpcf7-submit"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if(user()->id == $order->buyer_id && !$order->getOriginal('image'))
                            <div class="row">
                                <div class="col-md-12">
                                    <div role="form" class="wpcf7">
                                        <div class="screen-reader-response"></div>
                                        <form action="/profile/order/update" method="post" class="wpcf7-form order-details-form" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{$order->id}}">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                     <div class="cart-collaterals">
                                                         <div class="cart_totals ">
                                                    <h2>{{word('transfer_image')}}</h2>
                                                    </div>
                                                    </div>
                                                    <br />
                                                    @include('error',['input' => 'image'])
                                                   <div class="container2">
                                                    <label class="label" for="input">{{word('upload_transfer_img')}}</label>
                                                    <div class="input">
                                                        <input name="image" id="file" type="file">
                                                    </div>
                                                    <input type="submit" value="{{word('submit')}}" class="wpcf7-form-control wpcf7-submit"/>
                                                </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="cart-collaterals">
                            <div class="cart_totals ">
                                <h2>{{word('order_total')}}</h2>

                                <table class="shop_table shop_table_responsive">
                                    <tbody>
                                    <tr class="cart-subtotal">
                                        <th>{{word('sub_total')}}</th>
                                        <td data-title="Subtotal">
                                            <span class="amount">{{$order->items_fee}} {{currency()}}</span></td>
                                    </tr>
                                    <tr class="shipping">
                                        <th>{{word('tax_fee')}}</th>
                                        <td data-title="Tax">
                                            {{$order->tax_fee}} {{currency()}}
                                        </td>
                                    </tr>
                                    <tr class="order-total">
                                        <th>{{word('all_total')}}</th>
                                        <td data-title="Total"><strong><span class="amount">{{$order->total_fee}} {{currency()}}<span></strong> </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="cart-collaterals  order-product">
                            <h2>{{word('products')}}</h2>
                            <ul class="product_list_widget">
                                @foreach($order->items as $product)
                                    <li>
                                    <a href="/product/{{$product->id}}/details" title="{{$product->name}}">
                                        <img class="wp-post-image" src="{{$product->image}}" alt="">
                                        <span class="product-title">{{$product->name}}</span>
                                    </a>
                                    <span class="electro-price"><span class="amount">{{$product->price->price}} {{currency()}}</span></span>
                                    <span class="product-count"> {{word('_count')}} :</span> <span class="count">{{$product->price->count}}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>

@endsection
@section('script')
   <script>
$(function(){
	var container2 = $('.container2'), inputFile = $('#file'), img, btn, txt = 'Browse', txtAfter = 'Browse another pic';
			$('#upload').click(function() {
    $('#file').show();
    $('#upload').prop('disabled', false);
    $('#file').change(function() {
        var filename = $('#file').val();
        $('#upload').html(filename);
    });
});

	if(!container2.find('#upload').length){
		container2.find('.input').append('<input type="button" value="'+txt+'" id="upload">');
		btn = $('#upload');
		container2.prepend('<img src="" class="hidden" alt="Uploaded file" id="uploadImg" width="100">');
		img = $('#uploadImg');
	}

	btn.on('click', function(){
		img.animate({opacity: 0}, 300);
		inputFile.click();
	});

	inputFile.on('change', function(e){
		container2.find('.label').html( inputFile.val() );

		var i = 0;
		for(i; i < e.originalEvent.srcElement.files.length; i++) {
			var file = e.originalEvent.srcElement.files[i],
				reader = new FileReader();

			reader.onloadend = function(){
				img.attr('src', reader.result).animate({opacity: 1}, 700);
			}
			reader.readAsDataURL(file);
			img.removeClass('hidden');
		}

		btn.val( txtAfter );
	});
});
    </script>
@endsection
