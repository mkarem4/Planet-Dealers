@extends('web.layout')



@section('style')

    @if(lang() == 'ar')
        <style>
            [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
                right: -9999px;
            }
        </style>
    @endif


@endsection

@section('content')
    <section class="body">
        <div class="container">
            <div class="block">
                <a href="#">
                    <img src="{{asset('web_assets/images/logo.png')}}"/>
                </a>
                <div class="titlepage">
                    <h2>{{word('register')}}</h2>
                </div>
                <div class="login">
                    <form action="/register" id="form-id" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="radio-block">
                            <div class="radio-content  col-xs-6 col-lg-6">
                                <input id="seller" type="radio" name="type" value="seller" checked/>
                                <label for="seller"><span></span> {{word('seller')}}</label>
                            </div>
                            <div class="radio-content  col-xs-6 col-lg-6">
                                <input id="buyer" type="radio" name="type"
                                       value="buyer" {{old('type') == 'buyer' ? 'checked' : ''}}/>
                                <label for="buyer"><span></span> {{word('buyer')}}</label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom: 15px;">
                            <label class="">{{word('country_and_city')}} : </label>
                            <select class="mdb-select md-form" searchable="{{word('search_here')}}" name="city_id"
                                    id="city_id">
                                <option disabled selected>{{word('choose_from_below')}}</option>
                                @foreach($all_countries as $country)
                                    <optgroup label="{{lang() == 'ar' ? $country->ar_name : $country->en_name}}"
                                              data-code="{{$country->code}}">
                                        @foreach($country->children as $city)
                                            <option value="{{$city->id}}" {{$city->id == old('city_id') ? 'selected' : ''}}>{{$city->name}}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @include('error',['input' => 'city_id'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label>{{word('first_name')}} :</label>
                            <input type="text" name="first_name" value="{{old('first_name')}}">
                            @include('error',['input' => 'first_name'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('last_name')}} : </label>
                            <input type="text" name="last_name" value="{{old('last_name')}}">
                            @include('error',['input' => 'last_name'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('email')}} : </label>
                            <input type="text" name="email" value="{{old('email')}}">
                            @include('error',['input' => 'email'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('phone')}} : </label>
                            <div class="row col-md-12">
                                <input class="col-md-1 country_code" disabled value=""
                                       style="width: 15% !important; background-color: #F96814; color: white;"/>
                                <input class="col-md-10" type="text" name="phone" value="{{old('phone')}}"
                                       style="width: 85% !important;">
                                @include('error',['input' => 'phone'])
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('whatsapp')}} : </label>
                            <div class="row col-md-12">
                                <input class="col-md-1 country_code" disabled value=""
                                       style="width: 15% !important; background-color: #F96814; color: white;"/>
                                <input class="col-md-10" type="text" name="whatsapp" value="{{old('whatsapp')}}"
                                       style="width: 85% !important;">
                                @include('error',['input' => 'whatsapp'])
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 seller_div">
                            <label>{{word('company_name')}} :</label>
                            <input type="text" name="company_name" value="{{old('company_name')}}">
                            @include('error',['input' => 'company_name'])
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 seller_div">
                            <label class="">{{word('bank_account_info')}} : </label>
                            <textarea name="bank_info" rows="5">{{old('bank_info')}}</textarea>
                            @include('error',['input' => 'bank_info'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" id="address_div" style="display: none;">
                            <label class="">{{word('address')}} : </label>
                            <input type="text" name="address" value="{{old('address')}}">
                            @include('error',['input' => 'address'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('password')}} : </label>
                            <input type="password" name="password">
                            @include('error',['input' => 'password'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <label class="">{{word('password_confirmation')}} : </label>
                            <input type="password" name="password_confirmation">
                            @include('error',['input' => 'password_confirmation'])
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 seller_div" style="margin-bottom: 15px;">
                            <button type="button" class="btn btn-success"
                                    style="float: {{lang() == 'ar' ? 'right' : 'left'}};"><label class=""
                                                                                                 for="commercial_record">{{word('attach_commercial_record')}} </label>
                            </button>
                            <input type="file" id="commercial_record" name="commercial_record">
                            @include('error',['input' => 'commercial_record'])

                            <div>
                                <span style="padding: 15px;"><small>{{word('description-commercial-1')}}</small></span><br>
                                <span style="padding: 15px;"><small>{{word('description-commercial-2')}}</small></span>
                            </div>


                        </div>
                        <br/>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <input type="checkbox" id="test1" name="terms"/>
                            <label for="test1">
                                {{word('i_accepts')}} <a href="/terms">{{word('terms')}}</a>
                            </label>
                            @include('error',['input' => 'terms'])
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <a href="/login" class="login-link">{{word('have_an_account')}}</a>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-12">
                            <button type="submit"
                                    class="waves-effect waves-light btn submit">{{word('register_now')}}</button>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            if ($('#seller').attr('checked')) {
                $('.seller_div').show();
                $('#address_div').hide();
            }
            if ($('#buyer').attr('checked')) {
                $('#address_div').show();
                $('.seller_div').hide();
            }
        });

        $('#city_id').on('change', function () {
            $code = $(':selected', this).closest('optgroup').data('code');
            $('.country_code').val($code);
        });


        $('#seller').on('click', function () {
            $('.seller_div').show();
            $('#address_div').hide();
        });

        $('#buyer').on('click', function () {
            $('#address_div').show();
            $('.seller_div').hide();
        });
    </script>
@endsection
