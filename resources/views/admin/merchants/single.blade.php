@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/merchants/index">{{word('merchants')}}</a></li>
        @if(isset($edit))
            <li>{{$edit->name}}</li>
            <li class="active">{{word('edit')}}</li>
        @else
            <li class="active">{{word('create')}}</li>
        @endif
    </ul>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/merchant/update" @else action="/admin/merchant/store" @endif enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <strong>
                                    @if(isset($edit))
                                        {{word('edit')}}
                                    @else
                                        {{word('create')}}
                                    @endif
                                </strong>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('city')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <select class="form-control select" name="city_id">
                                        <option selected disabled>{{word('select_below')}}</option>
                                        @foreach($countries as $country)
                                            <option disabled>{{$country->name}}</option>
                                                @forelse($country->children as $city)
                                                    <option style="text-indent: 10px;" value="{{$city->id}}" @if(isset($edit) && $edit->city_id == $city->id) selected @elseif(old('city_id') == $city->id) selected @endif>{{$city->name}}</option>
                                                @empty
                                                    <option style="text-indent: 10px;" disabled>{{word('add_first')}}</option>
                                                @endforelse
                                        @endforeach
                                    </select>
                                    @include('error', ['input' => 'city'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('type')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <select class="form-control select" id="type" name="type">
                                        <option selected disabled>{{word('select_below')}}</option>
                                        <option value="buyer" @if(isset($edit) && $edit->type == 'buyer') selected @elseif(old('type') == 'buyer') selected @endif>{{word('buyer')}}</option>
                                        <option value="seller" @if(isset($edit) && $edit->type == 'seller') selected @elseif(old('type') == 'seller') selected @endif>{{word('seller')}}</option>
                                    </select>
                                    @include('error', ['input' => 'type'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('first_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="first_name" @if(isset($edit)) value="{{$edit->first_name}}" @else value="{{old('first_name')}}" @endif/>
                                    @include('error', ['input' => 'first_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('last_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="last_name" @if(isset($edit)) value="{{$edit->last_name}}" @else value="{{old('last_name')}}" @endif/>
                                    @include('error', ['input' => 'last_name'])
                                </div>
                            </div>
                            @if(isset($edit))
                                <div class="form-group {{ $errors->has('expire_at') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{word('expire_at')}}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <input type="date" class="form-control" name="expire_at" @if(isset($edit)) value="{{$edit->expire_at}}" @else value="{{old('expire_at')}}" @endif/>
                                        @include('error', ['input' => 'expire_at'])
                                    </div>
                                </div>
                            @endif


                            <div class="form-group {{ $errors->has('bank_info') ? ' has-error' : '' }} type-div seller_div">
                                <label class="col-md-3 col-xs-12 control-label">{{word('bank_account_info')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <textarea name="bank_info" class="form-control" rows="4">{{isset($edit) ? $edit->bank_info : old('bank_info')}}</textarea>
                                    @include('error', ['input' => 'bank_info'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('company_name') ? ' has-error' : '' }} type-div seller_div">
                                <label class="col-md-3 col-xs-12 control-label">{{word('company_name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="company_name" @if(isset($edit)) value="{{$edit->company_name}}" @else value="{{old('company_name')}}" @endif/>
                                    @include('error', ['input' => 'company_name'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('commercial_record') ? ' has-error' : '' }} type-div seller_div">
                                <label class="col-md-3 col-xs-12 control-label">{{word('commercial_record')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    @if(! isset($edit))
                                        <button type="button" class="btn btn-info btn-file">{{word('browse')}}</button>
                                        <input type="file" name="commercial_record" class="input-file"/>
                                    @else
                                        @if($edit->getOriginal('commercial_record'))
                                            <div class="gallery">
                                                <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}" href="{{$edit->commercial_record}}" title="{{$edit->commercial_record}}" data-gallery style="padding : 10px 0px 0px 0px;">
                                                    <div class="image">
                                                        <img src="{{$edit->commercial_record}}" alt="{{$edit->commercial_record}}" style="width: 200px; height: 200px;"/>
                                                    </div>
                                                </a>
                                            </div>
                                        @else
                                            <span class="label label-form label-primary">{{word('none')}}</span>
                                        @endif
                                    @endif
                                    @include('error', ['input' => 'commercial_record'])
                                </div>
                            </div>
                            @if(! isset($edit))
                                <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }} type-div" id="address_div">
                                    <label class="col-md-3 col-xs-12 control-label">{{word('address')}}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <input type="text" class="form-control" name="address" value="{{old('address')}}"/>
                                        @include('error', ['input' => 'address'])
                                    </div>
                                </div>
                            @endif
                            <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('email')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="email" class="form-control" id="email" name="email" @if(isset($edit)) value="{{$edit->email}}" @else value="{{old('email')}}" @endif/>
                                    @include('error', ['input' => 'email'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('phone')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="phone" @if(isset($edit)) value="{{$edit->phone}}" @else value="{{old('phone')}}" @endif/>
                                    @include('error', ['input' => 'phone'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('whatsapp') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('whatsapp')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="whatsapp" @if(isset($edit)) value="{{$edit->whatsapp}}" @else value="{{old('whatsapp')}}" @endif/>
                                    @if(isset($edit))
                                        <span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                    @endif
                                    @include('error', ['input' => 'whatsapp'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('password')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="password" class="form-control" name="password" id="password"/>
                                    @if(isset($edit))
                                        <span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                    @endif
                                    @include('error', ['input' => 'password'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('password_confirmation')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="password" class="form-control" name="password_confirmation"/>
                                    @if(isset($edit))
                                        <span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                    @endif
                                    @include('error', ['input' => 'password'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('featured') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('featured')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <label class="switch">
                                        <input type="checkbox" id="featured" name="featured" @if(isset($edit) && $edit->featured && $edit->featured_till) checked @endif value="1">
                                        <span></span>
                                    </label>
                                    @include('error', ['input' => 'featured'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('featured_till') ? ' has-error' : '' }}" id="featured_till" style="display: {{(isset($edit) && $edit->featured && $edit->featured_till) ? 'block' : 'none'}}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('featured_till')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="date" class="form-control" name="featured_till" @if(isset($edit)) value="{{$edit->featured_till}}" @else value="{{old('featured_till')}}" @endif/>
                                    @include('error', ['input' => 'featured_till'])
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('image') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('image')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <button type="button" class="btn btn-info btn-file">{{word('browse')}}</button>
                                    @if(isset($edit))
                                        <br/><span class="label label-warning" style="padding: 2px;"> {{word('leave_it')}} </span>
                                        <br/>
                                        <div class="gallery">
                                            <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}" href="{{$edit->image}}" title="{{$edit->image}}" data-gallery style="padding : 10px 0px 0px 0px;">
                                                <div class="image">
                                                    <img src="{{$edit->image}}" alt="{{$edit->image}}"/>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" name="image" class="input-file"/>
                                    @include('error', ['input' => 'image'])
                                </div>
                            </div>
                            @if(isset($edit))
                                <input type="hidden" name="id" id="edit_id" value="{{$edit->id}}">
                            @else
                                <div class="form-group {{ $errors->has('status') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label">{{word('do_activate')}}</label>
                                    <div class="col-md-6 col-xs-12">
                                        <label class="switch">
                                            <input type="checkbox" name="status" value="active">
                                            <span></span>
                                        </label>
                                        @include('error', ['input' => 'status'])
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary pull-right">
                                @if(isset($edit))
                                    {{word('edit')}}
                                @else
                                    {{word('create')}}
                                @endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        @if(! isset($edit))
            $('.type-div').hide();
        @endif

        $('#type').on('change',function()
        {
            if($('#type').val() === 'seller')
            {
                $('.seller_div').show();
                $('#address_div').hide();
            }
            else
            {
                $('#address_div').show();
                $('.seller_div').hide();
            }

        });
    </script>
    <script>
        var bool = $('#edit_id').length === 0;
        if(bool)
        {
            setTimeout(
                function() { $('#email').val(''); }, 1000
            );
        }
        var jvalidate = $("#jvalidate").validate({
            ignore: [],
            rules: {
                'city_id': {
                    required: true,
                },
                'type': {
                    required: true,
                },
                'first_name': {
                    required: true,
                },
                'last_name': {
                    required: true,
                },
                'company_name': {
                    required: true,
                },
                'email': {
                    required: true,
                    email: true
                },
                'phone': {
                    required: true,
                    digits : true
                },
                'password': {
                    required: bool,
                    minlength: 6
                },
                'password_confirmation': {
                    required: bool,
                    minlength: 6,
                    equalTo: "#password"
                }
            },
        });
    </script>
@endsection
