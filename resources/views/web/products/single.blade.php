@extends('web.layout')
@section('style')
    <style>
        .variant_div_border
        {
            border: solid grey 1px;
            padding: 20px;
            border-radius: 20px;
        }

    </style>
@endsection
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" >
                <a href="/">{{word('home')}}</a>
                <span ><i class="fa fa-angle-right"></i></span>
                <a href="/profile/products">{{word('my_products')}}</a>
                <span><i class="fa fa-angle-right"></i></span>
                {{word(isset($product) ? 'edit' : 'add')}}
            </nav>
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <article class="hentry">
                        <header class="entry-header">
                            <h1 class="entry-title">{{word(isset($product) ? 'edit_product' : 'add_product')}}</h1>
                        </header><!-- .entry-header -->
                        <div class="entry-content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div role="form" class="wpcf7">
                                        <div class="screen-reader-response"></div>
                                            <form action="/profile/product{{isset($product) ? '/update' : '/store'}}" method="post" class="wpcf7-form" enctype="multipart/form-data">
                                                {{csrf_field()}}
                                                @if(isset($product))
                                                    <input type="hidden" name="id" value="{{$product->id}}">
                                                @endif
                                                <div class="form-group row">
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('ar_name')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'ar_name'])
                                                        <span class="wpcf7-form-control-wrap en_name">
                                                            <input type="text" name="ar_name" value="{{isset($product) ? $product->ar_name : old('ar_name')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('en_name')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'en_name'])
                                                        <span class="wpcf7-form-control-wrap en_name">
                                                            <input type="text" name="en_name" value="{{isset($product) ? $product->ar_name : old('en_name')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                                        </span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('main_image')}} {{word(isset($product) ? 'optional' : 'required')}}</label><br />
                                                        @include('error',['input' => 'image'])
                                                        <span class="wpcf7-form-control-wrap image">
                                                            <input type="file" name="image" class="wpcf7-form-control" style="display: block;" aria-required="true" aria-invalid="false"/>
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('sub_images')}} {{word('optional')}}</label><br />
                                                        @include('error',['input' => 'images'])
                                                        @include('error',['input' => 'images.*'])
                                                        <span class="wpcf7-form-control-wrap image">
                                                            <input type="file" name="images[]" class="wpcf7-form-control" style="display: block;" aria-required="true" aria-invalid="false" multiple/>
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-12">


                                                       <span class=""><small>{{word('description-image-1')}}</small></span><br>
                                                       <span class=""><small>{{word('description-image-2')}}</small></span><br>
                                                       <span class=""><small>{{word('description-image-3')}}</small></span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-xs-12 col-md-12">
                                                        <label>{{word('ar_desc')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'ar_desc'])
                                                        <span class="wpcf7-form-control-wrap your-message"><textarea name="ar_desc" cols="40" rows="10" class="wpcf7-form-control input-text wpcf7-textarea" aria-invalid="false">{{isset($product) ? $product->getMeta($product->id,'ar_desc') : old('ar_desc')}}</textarea></span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-12">
                                                        <label>{{word('en_desc')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'en_desc'])
                                                        <span class="wpcf7-form-control-wrap your-message"><textarea name="en_desc" cols="40" rows="10" class="wpcf7-form-control input-text wpcf7-textarea" aria-invalid="false">{{isset($product) ? $product->getMeta($product->id,'en_desc') : old('en_desc')}}</textarea></span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('ar_custom')}} {{word('optional')}}</label><br />
                                                        @include('error',['input' => 'ar_custom'])
                                                        <span class="wpcf7-form-control-wrap your-message"><textarea name="ar_custom" cols="40" rows="10" class="wpcf7-form-control input-text wpcf7-textarea" aria-invalid="false" placeholder="{{word('product_custom_empty')}} -{{word('leave_empty')}}">{{isset($product) ? $product->getMeta($product->id,'ar_special') : old('ar_special')}}</textarea></span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('en_custom')}} {{word('optional')}}</label><br />
                                                        @include('error',['input' => 'en_custom'])
                                                        <span class="wpcf7-form-control-wrap your-message"><textarea name="en_custom" cols="40" rows="10" class="wpcf7-form-control input-text wpcf7-textarea" aria-invalid="false" placeholder="{{word('product_custom_empty')}} - {{word('leave_empty')}}">{{isset($product) ? $product->getMeta($product->id,'en_special') : old('en_special')}}</textarea></span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('city')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'city'])
                                                        <span class="wpcf7-form-control-wrap type">
                                                            <select name="city_id" class="form-control wpcf7-form-control" aria-required="true" aria-invalid="false">
                                                                <option selected disabled>{{word('choose_from_below')}}</option>
                                                                @foreach($cities as $city)
                                                                    <option value="{{$city->id}}" @if(isset($product) && $product->city_id == $city->id) selected @elseif(! isset($product) && old('city_id') == $city->id) selected @endif>{{$city->name}}</option>
                                                                @endforeach
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('category')}} {{word('required')}}</label><br />
                                                        @include('error',['input' => 'category'])
                                                        <span class="wpcf7-form-control-wrap type">
                                                            <select name="sec_cat_id" class="form-control wpcf7-form-control" aria-required="true" aria-invalid="false">
                                                                <option selected disabled>{{word('choose_from_below')}}</option>
                                                                @foreach($categories as $category)
                                                                    <optgroup label=" + {{$category->name}}">
                                                                        @foreach($category->subs as $sub)
                                                                            <optgroup label=" - {{$sub->name}}">
                                                                                @foreach($sub->subs as $sec)
                                                                                    <option value="{{$sec->id}}" @if(isset($product) && $product->sec_cat_id == $sec->id) selected @elseif(! isset($product) && old('sec_cat_id') == $sec->id) selected @endif>{{$sec->name}}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        @endforeach
                                                                    </optgroup>
                                                                @endforeach
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="clearfix"></div>
                                                    <div class="col-xs-12 col-md-6">
                                                        <label>{{word('type')}} {{word('required')}}</label><br />
                                                        <span class="wpcf7-form-control-wrap type">
                                                            <select name="type" class="form-control wpcf7-form-control" id="type_select" aria-required="true" aria-invalid="false">
                                                                <option selected disabled>{{word('choose_from_below')}}</option>
                                                                <option value="static" {{isset($product) && $product->type == 'static'? 'selected' : ''}}>{{word('static')}}</option>
                                                                <option value="variable" {{isset($product) && $product->type == 'variable'? 'selected' : ''}}>{{word('variable')}}</option>
                                                            </select>
                                                        </span>
                                                    </div>
                                                    <div id="static_div" class="col-xs-6 col-md-6" style="display: {{isset($product) && $product->type == 'static' ? 'block' : 'none'}};">
                                                        <div class="col-md-6">
                                                            <label>{{word('price')}} {{word('required')}}</label><br />
                                                            <span class="wpcf7-form-control-wrap type">
                                                            <input type="number" min="0" step="0.1" name="price" value="{{isset($product) && $product->type == 'static' ? $product->price_meta->price : old('price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6" >
                                                            <label>{{word('sale_price')}}</label>  <span class="side_text">{{word('empty_no_sale')}}</span><br />
                                                            <span class="wpcf7-form-control-wrap type">
                                                            <input type="number" min="0" step="0.1" name="sale_price" value="{{isset($product) && $product->type == 'static' && $product->price_meta->price != $product->price_meta->sale_price ? $product->price_meta->sale_price : old('sale_price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6" >
                                                            <label>{{word('sale_till')}}</label>  <span class="side_text">{{word('empty_no_sale')}}</span><br />
                                                            <span class="wpcf7-form-control-wrap type">
                                                            <input type="date" min="{{\Carbon\Carbon::now()->toDateString()}}" name="sale_till" value="{{isset($product) && $product->type == 'static' ? $product->discount_till : old('sale_till')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>{{word('count')}} {{word('required')}}</label><br />
                                                            <span class="wpcf7-form-control-wrap type">
                                                            <input type="number" min="0" step="1" name="count" value="{{isset($product) && $product->type == 'static' ? $product->price_meta->count : old('count')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/>
                                                        </span>
                                                        </div>
                                                    </div >
                                                    <div id="variant_div">
                                                        <div class="col-xs-6 col-md-6 variant_div_border" id="variations_div" style="display: {{isset($product) && $product->type == 'variable' ? 'block' : 'none'}}; border: none;">
                                                                <label>{{word('choose_variations')}} {{word('required')}}</label><br />
                                                                <span class="wpcf7-form-control-wrap">
                                                                <select class="form-control wpcf7-form-control" id="variation_ids" aria-required="true" aria-invalid="false" multiple>
                                                                    <option disabled>{{word('choose_from_below')}}</option>
                                                                    @foreach($variations as $variation)
                                                                        <option value="{{$variation->id}}" {{isset($product) && $product->type == 'variable' && in_array($variation->id,$product->variations->pluck('id')->toArray()) ? 'selected' : ''}}>{{$variation->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                                </span>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="col-xs-12 col-md-12 variant_div_border" id="variations_prices_div" style="display: {{isset($product) && $product->type == 'variable' ? 'block' : 'none'}};">
                                                            @if(isset($product) && $product->type == 'variable')
                                                                @foreach($product->variations_data as $variation_data)
                                                                    @php $i = $loop->iteration; @endphp
                                                                    <div id="pricing_div_{{$i}}">
                                                                        @foreach($product->variations as $variation)
                                                                            <div class="col-xs-12 col-md-3"><label>{{$variation->name}}</label><br>
                                                                                <span class="wpcf7-form-control-wrap">
                                                                                    <select class="form-control wpcf7-form-control" name="options[{{$i}}][ids][]" aria-required="true" aria-invalid="false">
                                                                                        <option selected disabled>{{word('choose_from_below')}}</option>
                                                                                        @foreach(\App\Models\ProductVariation::getVariationOptions($variation->id) as $option)
                                                                                            <option value="{{$option->id}}" @if(in_array($option->id,$variation_data->options)) selected @endif>{{$option->name}}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </span>
                                                                            </div>
                                                                        @endforeach
                                                                        <div class="col-xs-1 col-md-1" style="float : {{lang() == 'ar' ? 'left' : 'right'}};">
                                                                            <button type="button" class="btn btn-danger" onclick="remove_div({{$i}})"><i class="fa fa-trash"></i></button>
                                                                        </div>
                                                                        <div class="form-group clearfix"></div><hr>
                                                                        <div class="col-xs-12 col-md-12">
                                                                            <div class="col-md-3">
                                                                                <label>{{word('price')}}</label><br>
                                                                                <span class="wpcf7-form-control-wrap type">
                                                                                    <input type="number" min="0" step="0.1" value="{{$variation_data->price}}" name="options[{{$i}}][price]" placeholder="{{word('price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false">
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label>{{word('sale_price')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br>
                                                                                <span class="wpcf7-form-control-wrap type">
                                                                                    <input type="number" min="0" step="0.1" value="{{$variation_data->price != $variation_data->sale_price ? $variation_data->sale_price : old('sale_price')}}" name="options[{{$i}}][sale_price]" placeholder="{{word('sale_price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false">
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-md-3" >
                                                                                <label>{{word('sale_till')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br />
                                                                                <span class="wpcf7-form-control-wrap type">
                                                                                    <input type="date" min="{{\Carbon\Carbon::now()->toDateString()}}" name="sale_till" value="{{isset($product) && $product->type == 'variable' ? $variation_data->sale_till : old('sale_till')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/>
                                                                                </span>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label>{{word('count')}}</label><br>
                                                                                <span class="wpcf7-form-control-wrap type">
                                                                                    <input type="number" min="0" step="1" value="{{$variation_data->count}}" name="options[{{$i}}][count]" placeholder="{{word('count')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false">
                                                                                </span>
                                                                            </div>
                                                                            </div>
                                                                        <div class="form-group clearfix"></div><hr>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="clearfix" id="add_more_btn" style="display: {{isset($product) && $product->type == 'variable' ? 'block' : 'none'}};">
                                                            <button type="button" class="wpcf7-form-control wpcf7-submit" style="margin-top: 20px;">{{word('add_more')}}</button>
                                                        </div>
                                                    </div>


                                                    <div class="form-group clearfix"></div>
                                                    <div class="form-group clearfix"></div>
                                                    <div class="form-group clearfix">
                                                        <p><input type="submit" value="{{isset($product) ? word('update') : word('create')}}" class="wpcf7-form-control wpcf7-submit" /></p>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
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
        var variation_ids;
        var x = 9999;

        @if(isset($product) && $product->type == 'variable')
            variation_ids = $('#variation_ids').val();

            $('#static_div').hide();
            $('#variations_div').show();
        @endif

        $('#type_select').on('change',function()
        {
            $val = $(this).val();

            if($val ==='static')
            {
                $('.variant_div').hide();
                $('#static_div').show();
            }
            else
            {
                $('#static_div').hide();
                $('#variations_div').show();

                $('#variation_ids').on('change',function()
                {
                    variation_ids = $('#variation_ids').val();
                    $.ajax
                    (
                        {
                            async: false,
                            url: '/ajax/get_variations_options',
                            method: 'POST',
                            data: { variation_ids: variation_ids, _token: '{{csrf_token()}}' },
                            dataType: 'json',
                            success: function (data)
                            {
                                var i = 0;
                                var div = '';

                                $('#variations_prices_div').empty();

                                $.each(data,function(key,variation)
                                {
                                    var sub_div = '<div id="pricing_div_'+ i +'"><div class="col-xs-12 col-md-3"><label>'+ variation.name +'</label><br/><span class="wpcf7-form-control-wrap">' +
                                        '<select class="form-control wpcf7-form-control" name="options['+ i +'][ids][]" aria-required="true" aria-invalid="false">' +
                                        '<option selected disabled>{{word('choose_from_below')}}</option>';

                                    $.each(variation.options,function(key,option)
                                    {
                                        sub_div += '<option value="' + option.id + '">' + option.name + '</option>';
                                    });

                                    sub_div += '</select></span></div>';

                                    div += sub_div;
                                });

                                div+= ' <div class="col-xs-1 col-md-1" style="float : {{lang() == 'ar' ? 'left' : 'right'}};"><button type="button" class="btn btn-danger" onclick="remove_div('+ i +')"><i class="fa fa-trash"></i></button></div>';
                                div += '<div class="form-group clearfix"></div><hr>';
                                div += '<div class="col-xs-12 col-md-12" id="pricing_div_'+ i +'"><div class="col-md-3" ><label>{{word('price')}}</label><br /><span class="wpcf7-form-control-wrap type">' +
                                    '<input type="number" min="0" step="0.1" name="options['+ i +'][price]" placeholder="{{word('price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>' +
                                    '<div class="col-md-3" ><label>{{word('sale_price')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br /><span class="wpcf7-form-control-wrap type"><input type="number" min="0" step="0.1" name="options['+ i +'][sale_price]" placeholder="{{word('sale_price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>'+
                                    '<div class="col-md-3" ><label>{{word('sale_till')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br /><span class="wpcf7-form-control-wrap type"><input type="date" min="{{\Carbon\Carbon::now()->toDateString()}}" name="options['+ i +'][sale_till]" placeholder="{{word('sale_till')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>'+
                                    '<div class="col-md-3" ><label>{{word('count')}}</label><br />' +
                                    '<span class="wpcf7-form-control-wrap type">' +
                                    '<input type="number" min="0" step="1" name="options['+ i +'][count]" placeholder="{{word('count')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div></div></div>';

                                i++;

                                $('#variations_prices_div').append(div);
                                $('#variations_prices_div').show();
                                $('#add_more_btn').show();

                                remove_div();
                            },
                            error : function(data)
                            {
                                console.log('variation_options ajax error',data);
                            }
                        }
                    );
                });
            }
        });

        $('#add_more_btn').on('click',function()
        { console.log('in');
            var div_copy = '';

            $.ajax
            (
                {
                    async: false,
                    url: '/ajax/get_variations_options',
                    method: 'POST',
                    data: { variation_ids: variation_ids, _token: '{{csrf_token()}}' },
                    dataType: 'json',
                    success: function (data)
                    {
                        $.each(data,function(key,variation)
                        {
                            var sub_div_copy = '<div id="pricing_div_'+ x +'"><div class="col-xs-12 col-md-3"><label>'+ variation.name +'</label><br/><span class="wpcf7-form-control-wrap">' +
                                '<select class="form-control wpcf7-form-control" name="options['+ x +'][ids][]" aria-required="true" aria-invalid="false">' +
                                '<option selected disabled>{{word('choose_from_below')}}</option>';

                            $.each(variation.options,function(key,option)
                            {
                                sub_div_copy += '<option value="' + option.id + '">' + option.name + '</option>';
                            });

                            sub_div_copy += '</select></span></div>';

                            div_copy += sub_div_copy;
                        });

                        div_copy+= ' <div class="col-xs-1 col-md-1" style="float : {{lang() == 'ar' ? 'left' : 'right'}};"><button type="button" class="btn btn-danger" onclick="remove_div('+ x +')"><i class="fa fa-trash"></i></button></div>';
                        div_copy += '<div class="form-group clearfix"></div><hr>';

                        div_copy += '<div class="col-xs-12 col-md-12" id="pricing_div_'+ x +'"><div class="col-md-3" ><label>{{word('price')}}</label><br /><span class="wpcf7-form-control-wrap type">' +
                            '<input type="number" min="0" step="0.1" name="options['+ x +'][price]" placeholder="{{word('price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>' +
                            '<div class="col-md-3" ><label>{{word('sale_price')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br /><span class="wpcf7-form-control-wrap type"><input type="number" min="0" step="0.1" name="options['+ x +'][sale_price]" placeholder="{{word('sale_price')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>'+
                            '<div class="col-md-3" ><label>{{word('sale_till')}}</label> <span class="side_text">{{word('empty_no_sale')}}</span><br /><span class="wpcf7-form-control-wrap type"><input type="date" min="{{\Carbon\Carbon::now()->toDateString()}}" name="options['+ x +'][sale_till]" placeholder="{{word('sale_till')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div>'+
                            '<div class="col-md-3" ><label>{{word('count')}}</label><br />' +
                            '<span class="wpcf7-form-control-wrap type">' +
                            '<input type="number" min="0" step="1" name="options['+ x +'][count]" placeholder="{{word('count')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false"/></span></div></div>';

                        x++;

                        remove_div();
                    },
                    error : function(data)
                    {
                        console.log('variation_options_copy ajax error',data);
                    }
                }
            );
            $('#variations_prices_div').append('<div class="form-group clearfix"></div><hr>' + div_copy);
        });

        function remove_div(id)
        {
            $('#pricing_div_'+id).remove();
        }
    </script>
@endsection
