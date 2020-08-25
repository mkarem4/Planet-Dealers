@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('packs_and_subs')}}</nav>

            <div id="primary" class="content-area order-details">
                <main id="main" class="site-main">
                    <article class="page type-page status-publish hentry">
                        <header class="entry-header"><h1 itemprop="name" class="entry-title">{{word('packs_and_subs')}}</h1></header><!-- .entry-header -->
                        <div class="package">
                            @if(user() && user()->type == 'seller' && user()->getOriginal('pack_id'))
                                <p class="package-details">{{word('current_pack')}}</p>
                                <div class="package-info">
                                    <p class="package-name">
                                        {{user()->pack->name}}
                                    </p>
                                    <p class="package-p">{{user()->pack->month_count}} {{word('month')}} /<i class="badge badge-dark">{{user()->pack->price}}<span>  {{country()->currency}}</span></i></p>

                                    <span class="package-title">{{word('days_remaining')}} :</span>
                                    <span class="package-days"> {{\Carbon\Carbon::parse(user()->expire_at)->diffInDays()}} {{word('days')}}</span>
                                </div>
                            @endif
                            <div class="package-types">
                                @if(user() && user()->type == 'seller')
                                    <h3>
                                        {{word('renew_subscription')}}
                                    </h3>
                                @endif
                                <ul>
                                    @foreach($packs as $pack)
                                        <li>
                                            <div class="media {{$pack->default ? 'active' : ''}}">
                                                <img class="mr-3" src="{{$pack->image}}" alt="{{$pack->image}}">
                                                <div class="media-body">
                                                    <h5 class="mt-0">{{$pack->name}}</h5>
                                                    @if(user())
                                                        <p> {{$pack->month_count}} {{word('month')}} / {{$pack->price}}
                                                            <span>{{country()->currency}}</span>
                                                        </p>
                                                    @endif
                                                    <a @if(! user()) href="/login" @endif>
                                                        <button @if(user() && user()->type == 'seller') data-toggle="modal" onclick="modal_subscribe({{$pack}})" data-target="#subscribe-pack" id="btn_{{$pack->id}}" @endif>{{word('subscribe_now')}}</button>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                   @endforeach
                                </ul>
                            </div>
                            <br/>
                            <br/>
                            <br/>
                            @if(user())
                                <div class="package-types">
                                    <div class="caption">
                                        <h2>{{word('banks')}}</h2>
                                    </div>
                                    <br/>
                                    <br/>
                                    <div class="entry-content">
                                        <div class="row about-features inner-top-md inner-bottom-sm">
                                            <div class="col-xs-12 col-md-12">
                                                @foreach($banks as $bank)
                                                    <div class="text-content" style="text-align: center !important;">
                                                        <h3>{{$bank->name}}</h3>
                                                        <label style="padding: 0px 20px 0px 20px;">{!! $bank->desc !!}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                </main>
            </div>
        </div>
    </div>
    <div class="modal fade" id="subscribe-pack" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{word('subscribe_now')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/pack/subscribe" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="package-types">
                            <ul>
                                <li>
                                    <div class="media active" style="text-align: center;">
                                        <img class="mr-3" id="pack_image" src="" alt="">
                                        <div class="media-body">
                                            <h5 class="mt-0" id="pack_name"></h5>
                                            @if(user())
                                                <p> <span id="pack_month"></span> {{word('month')}} / <span id="pack_price"></span>
                                                    <span>{{country()->currency}}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <label>{{word('choose_bank')}}</label><br/>
                            @include('error',['input' => 'bank_id'])
                            <span class="wpcf7-form-control-wrap">
                            <select class="form-control wpcf7-form-control" name="bank_id" aria-required="true" aria-invalid="false">
                                <option disabled selected>{{word('choose_from_below')}}</option>
                                 @foreach($banks as $bank)
                                    <option value="{{$bank->id}}">{{$bank->name}}</option>
                                @endforeach
                            </select>
                            </span>
                        </div>
                        <div class="col-md-12">
                            <label>{{word('name')}}</label><br />
                            @include('error',['input' => 'user_name'])
                            <span class="wpcf7-form-control-wrap en_name">
                                <input type="text" name="user_name" value="@if(old('user_name')) {{old('user_name')}} @elseif(user()) {{user()->name}} @endif" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                            </span>
                        </div>
                        <div class="col-md-12">
                            <label>{{word('your_account_no')}}</label><br />
                            @include('error',['input' => 'account_no'])
                            <span class="wpcf7-form-control-wrap en_name">
                                <input type="text" name="account_no" value="{{old('account_no')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                            </span>
                        </div>
                        <div class="col-md-12">
                            <label>{{word('notes')}} {{word('optional')}}</label><br />
                            <span class="wpcf7-form-control-wrap notes">
                                <input type="text" name="notes" value="{{old('notes')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                            </span>
                        </div>
                        <div class="col-md-12">
                            <label>{{word('transfer_image')}}</label><br />
                            @include('error',['input' => 'image'])
                            <span class="wpcf7-form-control-wrap image">
                                <input type="file" name="image" class="wpcf7-form-control" style="display: block;" aria-required="true" aria-invalid="false"/>
                            </span>
                            <span class=""><small>{{word('description-image-1')}}</small></span><br>

                        </div>
                        <input type="hidden" name="pack_id" id="pack_id" value="">
                        @include('error',['input' => 'pack_id'])
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn send-button">{{word('subscribe')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>
        @if($errors->has('image') || $errors->has('pack_id') || $errors->has('bank_id')|| $errors->has('user_name')|| $errors->has('account_no'))
            $('#btn_{{old('pack_id')}}').click();
        @endif

        function modal_subscribe(pack)
        {
            $('#pack_id').val(pack.id);
            $('#pack_image').prop('src',pack.image);
            $('#pack_name').html(pack.name);
            $('#pack_month').html(pack.month_count);
            @if(user())
                $('#pack_price').html(pack.price);
            @endif
        }
    </script>
@endsection
