@extends('web.layout')
@section('content')
    <section class="body">
        <div class="container">
            <div class="block">
                <a href="#">
                    <img src="{{asset('web_assets/images/logo.png')}}"/>
                </a>
                <div class="titlepage">
                    <h2>{{word('reset_password')}}</h2>
                </div>
                <div class="login">
                    <form action="/password/reset" id="form-id" method="post">
                        {{csrf_field()}}
                        <div class="col-lg-12">
                            <label>{{word('reset_code')}} :</label>
                            <input type="text" name="code" value="{{old('code')}}">
                            @include('error',['input' => 'code'])
                        </div>

                        <div class="col-lg-6">
                            <label class="">{{word('password')}} : </label>
                            <input type="password" name="password">
                            @include('error',['input' => 'password'])
                        </div>
                        <div class="col-lg-6">
                            <label class="">{{word('password_confirmation')}} : </label>
                            <input type="password" name="password_confirmation">
                            @include('error',['input' => 'password_confirmation'])
                        </div>
                        <div class="col-lg-12">
                            <button type="submit" class="waves-effect waves-light btn submit">{{word('reset_now')}}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
@endsection
