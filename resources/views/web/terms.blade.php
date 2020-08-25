@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
             <nav class="woocommerce-breadcrumb"><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('terms')}}</nav>
            <div id="primary" class="content-area">
                <main id="main" class="site-main">
                    <article class="has-post-thumbnail hentry">
                            <div class="caption">
                                <h1 class="entry-title" itemprop="name">{{word('terms')}}</h1>
                            </div>
                        <div class="entry-content">
                            <div class="row about-features inner-top-md inner-bottom-sm">
                                <div class="col-xs-12 col-md-12">
                                    <div class="text-content">
                                        {!! $text->text !!}
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
