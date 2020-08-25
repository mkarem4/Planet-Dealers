@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li class="active"><a href="/admin/products/index">{{word('products')}}</a></li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
            <div class="col-md-12 index-widget">
                <div class="col-md-3">
                    <div class="widget widget-success widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-check"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$active_count}}</div>
                            <div class="widget-title">{{word('active_products')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-primary widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-minus"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$suspended_count}}</div>
                            <div class="widget-title">{{word('suspended_products')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-info widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-cog fa-spin"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$on_hold_count}}</div>
                            <div class="widget-title">{{word('on_hold_products')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-danger widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-trash"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$deleted_count}}</div>
                            <div class="widget-title">{{word('deleted_products')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="{{word('name')}}"
                               value="{{isset($inputs['name']) ? $inputs['name'] : ''}}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="active" {{isset($inputs['status']) && $inputs['status'] == 'active' ? 'selected' : ''}}>{{word('active')}}</option>
                            <option value="suspended" {{isset($inputs['status']) && $inputs['status'] == 'suspended' ? 'selected' : ''}}>{{word('suspended')}}</option>
                            <option value="on_hold" {{isset($inputs['status']) && $inputs['status'] == 'on_hold' ? 'selected' : ''}}>{{word('on_hold')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="type">
                            <option value="all" selected>{{word('all_pricing')}}</option>
                            <option value="static" {{isset($inputs['type']) && $inputs['type'] == 'static' ? 'selected' : ''}}>{{word('static')}}</option>
                            <option value="variable" {{isset($inputs['type']) && $inputs['type'] == 'variable' ? 'selected' : ''}}>{{word('variable')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="featured">
                            <option value="all" selected>{{word('all_types')}}</option>
                            <option value="1" {{isset($inputs['featured']) && $inputs['featured'] == '1' ? 'selected' : ''}}>{{word('featured')}}</option>
                            <option value="0" {{isset($inputs['featured']) && $inputs['featured'] == '0' ? 'selected' : ''}}>{{word('not_featured')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="discount">
                            <option value="all" selected>{{word('all_prices')}}</option>
                            <option value="1" {{isset($inputs['discount']) && $inputs['discount'] == '1' ? 'selected' : ''}}>{{word('with_discount')}}</option>
                            <option value="0" {{isset($inputs['discount']) && $inputs['discount'] == '0' ? 'selected' : ''}}>{{word('without_discount')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row index-widget mt-2">
                <div class="form-group" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}}">
                    <a>
                        <button type="submit" class="btn btn-info">{{word('filter')}}</button>
                    </a>
                    <a href="?" class="btn btn-danger">{{word('clear')}}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="page-content-wrap" style="padding-top: 5px;">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('image')}}</th>
                                    <th class="rtl_th">{{word('name')}}</th>
                                    <th class="'rtl_th">{{word('pricing')}}</th>
                                    <th class="'rtl_th">{{word('merchant')}}</th>
                                    <th class="'rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="gallery">
                                                <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}"
                                                   href="{{$product->image}}" title="{{$product->image}}" data-gallery
                                                   style="padding : 10px 0px 0px 0px;">
                                                    <div class="image">
                                                        <img src="{{$product->thumb_image}}" alt="{{$product->image}}"
                                                             class="product-table-image"/>
                                                    </div>
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="label label-form label-info">{{$product->name}}</span><br/>
                                            <span class="label label-form label-warning">{!! $product->get_stars($product->rate) !!} ( {{$product->rate_count}} )</span><br/>
                                            <span class="label label-form label-success">{{$product->main_cat->name}} - {{$product->sub_cat->name}} - {{$product->sec_cat->name}}</span>
                                        </td>
                                        <td>
                                            <span class="label @if($product->type == 'static') label-success @else label-info @endif label-form">{{word($product->type)}}</span><br/>
                                            @if($product->type == 'static')
                                                <span class="label label-primary label-form">{{$product->price_meta->sale_price}} {{$product->country->currency}} - {{$product->price_meta->count}} {{word('unit')}}</span>
                                                <br/>
                                            @else
                                                @foreach($product->variations_data as $variation)
                                                    <span class="label label-primary label-form">{{$variation->options_str}} ( {{$variation->sale_price}} {{$product->country->currency}} - {{$variation->count}} {{word('unit')}} )</span>
                                                    @if($variation->sale)
                                                        <span class="label label-danger label-form">{{word('discount')}} {{$variation->price - $variation->sale_price}} {{$product->country->currency}} {{word('till') . ' ' . \Carbon\Carbon::parse($variation->api_sale_till)->toDateString()}}</span>
                                                    @endif
                                                    <br/>
                                                @endforeach
                                            @endif
                                            @if($product->featured)
                                                <span class="label label-warning label-form">{{word('featured_till') . ' ' . $product->featured_till}}</span>
                                                <br/>
                                            @endif

                                        </td>
                                        <td>
                                            <span class="label label-primary label-form">{{$product->seller->name .' - '. $product->seller->company_name}}</span><br/>
                                            <span class="label label-success label-form">{{$product->seller->city->name}}</span><br/>
                                            <a href="mailTo:{{$product->seller->email}}">{{$product->seller->email}}</a><br/>
                                            <a href="tel:{{$product->seller->phone}}">{{$product->seller->phone}}</a>
                                            @if(isset($seller->whatsapp))<a
                                                    href="whatsapp:{{$product->seller->whatsapp}}">{{$product->seller->whatsapp}}</a>@endif
                                        </td>

                                        <td>
                                            <span class="label @if($product->status == 'active') label-success @elseif($product->status == 'suspended') label-primary @else label-info @endif label-form">{{word($product->status)}}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-condensed mb-control"
                                                    onclick="modal_feature({{$product->id}},{{$product->featured}},'{{$product->featured_till}}')"
                                                    data-box="#message-box-info" title="{{word('featuring_product')}}">
                                                <i class="fa fa-star"></i></button>
                                            @if($product->status == 'active')
                                                <button class="btn btn-primary btn-condensed mb-control"
                                                        onclick="modal_suspend({{$product->id}})"
                                                        data-box="#message-box-primary" title="{{word('suspend')}}"><i
                                                            class="fa fa-minus-circle"></i></button>
                                            @else
                                                <button class="btn btn-success btn-condensed mb-control"
                                                        onclick="modal_activate({{$product->id}})"
                                                        data-box="#message-box-success" title="{{word('activate')}}"><i
                                                            class="fa fa-check-square"></i></button>
                                            @endif
                                            <button class="btn btn-danger btn-condensed mb-control"
                                                    onclick="modal_destroy({{$product->id}})"
                                                    data-box="#message-box-danger" title="{{word('delete')}}"><i
                                                        class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">{{word('sorry_no_data')}}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                                {!! $products->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- info with sound -->
    <div class="message-box message-box-info animated fadeIn" data-sound="alert" id="message-box-info">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-star"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('feature_product')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/product/feature_handle"
                          class="buttons form-horizontal text-{{lang()}}">
                        {{csrf_field()}}
                        <div class="form-group {{ $errors->has('featured') ? ' has-error' : '' }}">
                            <label class="col-md-3 col-xs-12 control-label">{{word('featured')}}</label>
                            <div class="col-md-6 col-xs-12">
                                <label class="switch">
                                    <input type="checkbox" id="featured" name="featured" value="1">
                                    <span></span>
                                </label>
                                @include('error', ['input' => 'featured'])
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('featured_till') ? ' has-error' : '' }}"
                             id="featured_till">
                            <label class="col-md-3 col-xs-12 control-label">{{word('featured_till')}}</label>
                            <div class="col-md-6 col-xs-12">
                                <input type="date" class="form-control" id="featured_till_input" name="featured_till"/>
                                @include('error', ['input' => 'featured_till'])
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id_feature" value="">
                        <button class="btn btn-info btn-lg pull-right">{{word('update')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close"
                            style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end info with sound -->

    <!-- success with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert" id="message-box-success">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-check-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('activate_product')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/product/change_status" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_activate" value="">
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-success btn-lg pull-right">{{word('activate')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close"
                            style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end success with sound -->

    <!-- warning with sound -->
    <div class="message-box message-box-primary animated fadeIn" data-sound="alert" id="message-box-primary">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-minus-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('suspend_product')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/product/change_status" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_suspend" value="">
                        <input type="hidden" name="status" value="suspended">
                        <button class="btn btn-primary btn-lg pull-right">{{word('suspend')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close"
                            style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end warning with sound -->

    <!-- danger with sound -->
    <div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="message-box-danger">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-trash"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('delete_product')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/product/delete" class="buttons">
                        {{csrf_field()}}
                        <input type="hidden" name="id" id="id_destroy" value="">
                        <button class="btn btn-danger btn-lg pull-right">{{word('delete')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close"
                            style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end danger with sound -->
    @if($errors->has('featured') || $errors->has('featured_till'))
        <script>
            $('#message-box-info').toggle();
        </script>
    @endif
@endsection


