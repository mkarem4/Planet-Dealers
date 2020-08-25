@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">

            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('profile')}}</nav><!-- .woocommerce-breadcrumb -->
            <div class="container account-details">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                            <li class="title">{{word('settings')}}</li>
                            <li class="nav-item">
                                <a class="nav-link active" href="/profile">{{word('personal_info')}}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/profile/orders">{{word('orders')}}</a>
                            </li>
                            @if(user()->type == 'seller')
                                <li class="nav-item">
                                    <a class="nav-link " href="/profile/products"  aria-selected="false">{{word('my_products')}}</a>
                                </li>
                            @endif
                            @if(user()->type == 'buyer')
                                <li class="nav-item">
                                    <a class="nav-link" href="/profile/addresses"  aria-selected="false">{{word('my_addresses')}}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <!-- /.col-md-4 -->
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active in" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div id="primary" class="content-area">
                                    <div class="wrapper">
                                        <form class="" method="post" action="/profile/update" enctype="multipart/form-data">
                                        <div class="profile-card js-profile-card">
                                            <div class="avatar-upload">
                                                <div class="avatar-edit">
                                                    <input type="file" id="imageUpload" name="image" accept=".png, .jpg, .jpeg"/>
                                                           @include('error',['input' => 'image'])
                                                    <label for="imageUpload"></label>
                                                </div>
                                                <div class="avatar-preview">
                                                    <div id="imagePreview" style="background-image: url({{user()->image}});">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="profile-card__cnt js-profile-cnt">
                                                <div class="page-content page-container" id="page-content">
                                                    <div class="padding">
                                                        <div class="row container d-flex justify-content-center">
                                                            <div class="grid-margin stretch-card">
                                                                <h5>{{word('general_info')}}</h5>
                                                                <!--x-editable starts-->
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                            {{csrf_field()}}

                                                                            <div class="form-group col-lg-6">
                                                                                <label for="first_name">{{word('first_name')}}</label>
                                                                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="{{word('first_name')}}" value="{{user()->first_name}}">
                                                                                @include('error',['input' => 'first_name'])
                                                                            </div>
                                                                            <div class="form-group col-lg-6">
                                                                                <label for="last_name">{{word('last_name')}}</label>
                                                                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="{{word('last_name')}}" value="{{user()->last_name}}">
                                                                                @include('error',['input' => 'last_name'])
                                                                            </div>
                                                                            <div class="form-group col-lg-6">
                                                                                <label for="company_name">{{word('company_name')}}</label>
                                                                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="{{word('company_name')}}" value="{{user()->company_name}}">
                                                                                @include('error',['input' => 'company_name'])
                                                                            </div>
                                                                            <div class="form-group col-lg-6">
                                                                                <label for="email">{{word('email')}}</label>
                                                                                <input type="text" class="form-control" id="email" name="email" placeholder="{{word('email')}}" value="{{user()->email}}">
                                                                                @include('error',['input' => 'email'])
                                                                            </div>
                                                                            <div class="form-group col-lg-6">
                                                                                <label for="phone">{{word('phone')}}</label>
                                                                                <input type="text" class="form-control" id="phone" name="phone" placeholder="{{word('phone')}}" value="{{user()->phone}}">
                                                                                @include('error',['input' => 'phone'])
                                                                            </div>
                                                                            <div class="form-group col-lg-6">
                                                                                <label for="city_id">{{word('city')}}</label>
                                                                                <select class="form-control" id="city_id" name="city_id" placeholder="{{word('city')}}">
                                                                                    <option disabled>{{word('choose_from_below')}}</option>
                                                                                    @foreach(user()->country->children as $city)
                                                                                        <option value="{{$city->id}}" {{user()->city_id == $city->id ? 'selected' : ''}}>{{$city->name}}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @include('error',['input' => 'city_id'])
                                                                            </div>
                                                                            @if(user()->type == 'seller')
                                                                                <div class="form-group col-lg-6">
                                                                                    <label class="">{{word('bank_account_info')}} : </label>
                                                                                    <textarea name="bank_info" rows="5">{{user()->bank_info}}</textarea>
                                                                                    @include('error',['input' => 'bank_info'])

                                                                                </div>
                                                                            @endif
                                                                            <div class="form-group col-lg-12">
                                                                                <button type="submit" class="btn btn-default">{{word('save')}}</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                                <!--x-editable ends-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <div class="security-info">
                                                        <h5>{{word('change_password')}}</h5>
                                                        <form class="" method="post" action="/profile/password/update">
                                                            {{csrf_field()}}
                                                            <div class="form-group col-lg-4">
                                                                <label for="old_password">{{word('old_password')}}</label>
                                                                <input type="password" class="form-control" id="exampleInputEmail2" name="old_password" placeholder="{{word('old_password')}}">
                                                                @include('error',['input' => 'old_password'])
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                <label for="password">{{word('new_password')}}</label>
                                                                <input type="password" class="form-control" id="password" name="password" placeholder="{{word('old_password')}}">
                                                                @include('error',['input' => 'password'])
                                                            </div>
                                                            <div class="form-group col-lg-4">
                                                                <label for="password_confirmation">{{word('new_password_confirmation')}}</label>
                                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{word('new_password_confirmation')}}">
                                                                @include('error',['input' => 'city_id'])
                                                            </div>
                                                            <div class="form-group col-lg-12">
                                                                <button type="submit" class="btn btn-default">{{word('save')}}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- #primary -->

                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /.col-md-8 -->
                </div>

            </div>
        </div><!-- #content -->

@endsection
@section('script')
<script>
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#imagePreview').css('background-image', 'url('+e.target.result +')');
            $('#imagePreview').hide();
            $('#imagePreview').fadeIn(650);
        }
        reader.readAsDataURL(input.files[0]);

        $('#imageUpload').val(input.files[0]);
    }
}
$("#imageUpload").change(function() {
    readURL(this);
});
</script>
@endsection
