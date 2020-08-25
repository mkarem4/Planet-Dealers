@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a>
                <span class="delimiter"><i class="fa fa-angle-right"></i></span>
                {{word('notifications')}}
            </nav>
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <header class="page-header">
                        <h1 class="page-title">{{word('notifications')}}</h1>
                    </header>
                    <div class="tab-content">

                        <div role="tabpanel" class="tab-pane active" id="grid" aria-expanded="true">
                            <ul class="products columns-12">
                                @foreach($nots as $not)
                                    <li class="notification-box">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-3 col-xs-3 text-center" style="text-align: center; margin-top: 10px;">
                                                <i class="fa fa-bell fa-3x" style="text-align: center;"></i>
                                            </div>
                                            <div class="col-lg-9 col-sm-9 col-xs-9">
                                                <strong class="text-info name">
                                                    @if($not->type == 'global')
                                                        {{word('global_not')}}
                                                    @elseif($not->type == 'order')
                                                        {{word('order_not')}}
                                                    @else
                                                        {{word('message_not')}}
                                                    @endif
                                                </strong>
                                                <div>
                                                    <a @if($not->type == 'order') href="/profile/order/{{$not->action_id}}" @elseif($not->type == 'message') href="/profile/inbox" @endif>
                                                        {{$not->text}}
                                                    </a>
                                                </div>
                                                <small class="text-warning">{{$not->created_at->diffForHumans()}}</small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                {{$nots->links('vendor.pagination.bootstrap-4')}}
                            </ul>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
@endsection
