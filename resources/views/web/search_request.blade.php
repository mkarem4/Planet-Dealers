@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">

            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('contact_us')}}</nav>

            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <article class="has-post-thumbnail hentry">
                        <div class="caption">
                            <h1 class="entry-title" itemprop="name">{{word('search_request')}}</h1>
                        </div>
                        <div itemprop="mainContentOfPage" class="entry-content">
                            <div class="vc_row-full-width vc_clearfix"></div>
                            <div class="vc_row wpb_row vc_row-fluid">
                                <div class="contact-form wpb_column vc_column_container vc_col-sm-9 col-sm-9">
                                    <div class="vc_column-inner ">
                                        <div class="wpb_wrapper">
                                            <div class="wpb_text_column wpb_content_element ">
                                                <div class="wpb_wrapper">
                                                    <h2 class="contact-page-title">{{word('we_search_for_u')}}</h2>
                                                </div>
                                            </div>
                                            <div lang="en-US" dir="{{lang() == 'ar' ? 'rtl' : 'ltr'}}" id="wpcf7-f2507-p2508-o1" class="wpcf7" role="form">
                                                <div class="screen-reader-response"></div>
                                                <form class="wpcf7-form" method="post" action="/search_request" enctype="multipart/form-data">
                                                    {{csrf_field()}}
                                                    <div class="form-group row">
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('search_for')}}*</label><br>
                                                            @include('error',['input' => 'type'])<br/>
                                                            <select class="form-control" name="type">
                                                                <option disabled selected>{{word('choose_from_below')}}</option>
                                                                <option value="product" {{old('type') == 'product' ? 'selected' : ''}}>{{word('product')}}</option>
                                                                <option value="seller" {{old('type') == 'seller' ? 'selected' : ''}}>{{word('seller')}}</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('name')}}*</label><br>
                                                            @include('error',['input' => 'name'])<br/>
                                                            <span class="wpcf7-form-control-wrap first-name"><input type="text" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required input-text" size="40" value="{{user() ? user()->first_name .' '. user()->last_name : old('name')}}" name="name"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('email')}}*</label><br>
                                                            @include('error',['input' => 'email'])<br/>
                                                            <span class="wpcf7-form-control-wrap last-name"><input type="email" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required input-text" size="40" value="{{user() ? user()->email : old('email')}}" name="email"></span>
                                                        </div>
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('phone')}}*</label><br>
                                                            @include('error',['input' => 'phone'])<br/>
                                                            <span class="wpcf7-form-control-wrap last-name"><input type="text" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required input-text" size="40" value="{{user() ? user()->phone : old('phone')}}" name="phone"></span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('address')}}*</label><br>
                                                            @include('error',['input' => 'address'])<br/>
                                                            <span class="wpcf7-form-control-wrap last-name"><input type="text" aria-invalid="false" aria-required="true" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required input-text" size="40" value="{{old('address')}}" name="address"></span>
                                                        </div>
                                                        <div class="col-xs-12 col-md-6">
                                                            <label>{{word('attachments')}}</label><br>
                                                            @include('error',['input' => 'attachments'])<br/>
                                                           <input type="file" aria-required="true" class="wpcf7-form-control wpcf7-text" style="display: block;" name="attachments[]" multiple>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{word('your_msg')}}*</label><br>
                                                        @include('error',['input' => 'text'])<br/>
                                                        <span class="wpcf7-form-control-wrap your-message"><textarea aria-invalid="false" class="wpcf7-form-control wpcf7-textarea" rows="10" cols="40" name="text" value="{{old('text')}}"></textarea></span>
                                                    </div>
                                                    <div class="form-group clearfix">
                                                        <p><input type="submit" value="{{word('submit')}}"></p>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
