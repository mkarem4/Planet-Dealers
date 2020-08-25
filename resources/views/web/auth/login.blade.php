@extends('web.layout')
@section('content')
    <section class="body">
        <div class="container">
            <div class="block">
                <a href="#">
                    <img src="{{asset('web_assets/images/logo.png')}}"/>
                </a>
                <div class="titlepage">
                    <h2>{{word('login')}}</h2>
                </div>
                <div class="login">
                    <form action="/login" method="post">
                        {{csrf_field()}}
                        <div class="col-lg-12">
                            <label>{{word('email')}} :</label>
                            <input type="text" name="email">
                        </div>
                        <div class="col-lg-12">
                            <label class="">{{word('password')}} : </label>
                            <input type="password" name="password">
                        </div>
                          <div class="col-xs-12 col-sm-8 col-md-9 col-lg-6">
                            <a href="/register" class="newacc">{{word('register')}}</a>
                        </div>
                          <div class="col-xs-12 col-sm-4 col-md-3 col-lg-6">
                            <!-- Button trigger modal -->
                                <a href="" class="forget" data-toggle="modal" data-target="#exampleModal">
                                    {{word('forgot_password?')}}
                                </a>
                            <!-- Modal -->
                        </div>
                         <div class="clearfix"></div>
                        <div class="col-lg-12">
                            <button type="submit" class="waves-effect waves-light btn submit">{{word('login')}}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{word('forgot_password')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/password/code/send" method="post">
                    <div class="modal-body">

                            {{csrf_field()}}
                            <div>
                                <label>{{word('email')}} :</label>
                                <input type="text" name="email">
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn send-button">{{word('send')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
