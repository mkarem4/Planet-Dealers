@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('addresses')}}</nav><!-- .woocommerce-breadcrumb -->
            <div class="titlepage">
                <button data-toggle="modal" data-target="#create-address" id="btn_create" title="{{word('create')}}"><i class="fa fa-plus"></i></button>
            </div>
            <div class="container account-details">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                        <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                            <li class="title">{{word('settings')}}</li>
                            <li class="nav-item">
                                <a class="nav-link " href="/profile">{{word('personal_info')}}</a>
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
                                    <a class="nav-link active" href="/profile/addresses"  aria-selected="false">{{word('my_addresses')}}</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active in"  role="tabpanel" aria-labelledby="contact-tab">
                                <div class="profile-card">
                                    <h5 style="margin-bottom:0;">{{word('my_addresses')}} ( {{$count}} )</h5>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{word('city')}}</th>
                                                <th scope="col">{{word('text')}}</th>
                                                <th scope="col">{{word('number')}}</th>
                                                <th scope="col">{{word('close_to')}}</th>
                                                <th scope="col">{{word('notes')}}</th>
                                                <th scope="col">{{word('actions')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($addresses as $address)
                                                <tr>
                                                    <th scope="row">{{$loop->iteration}}</th>
                                                    <td>{{$address->city->name}}</td>
                                                    <td>{{$address->text}}</td>
                                                    <td>{{$address->number}}</td>
                                                    <td>{{$address->close_to}}</td>
                                                    <td>{{$address->notes != '' ? $address->notes : word('none')}}</td>
                                                    <td>
                                                        <button data-toggle="modal" onclick="modal_edit({{$address}})" data-target="#update-address" id="btn_{{$address->id}}" title="{{word('edit')}}"><i class="fa fa-edit"></i></button>
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal" onclick="modal_destroy({{$address->id}})" data-target="#delete-address" title="{{word('delete')}}"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div class="pagination-{{lang()}}">
                                            {{$addresses->links('vendor.pagination.bootstrap-4')}}<br/><br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="create-address" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('create')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/address/store" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <div class="col-md-12">
                                <label>{{word('city')}}</label><br/>
                                @include('error',['input' => 'city_id'])
                                <span class="wpcf7-form-control-wrap">
                                    <select class="form-control wpcf7-form-control" name="city_id" aria-required="true" aria-invalid="false">
                                        <option disabled selected>{{word('choose_from_below')}}</option>
                                         @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('text')}}</label><br />
                                @include('error',['input' => 'text'])
                                <span class="wpcf7-form-control-wrap text">
                                    <input type="text" name="text" value="{{old('text')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('number')}}</label><br />
                                @include('error',['input' => 'number'])
                                <span class="wpcf7-form-control-wrap number">
                                <input type="text" name="number" value="{{old('number')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('close_to')}}</label><br />
                                @include('error',['input' => 'close_to'])
                                <span class="wpcf7-form-control-wrap close_to">
                                <input type="text" name="close_to" value="{{old('close_to')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('notes')}} {{word('optional')}}</label><br />
                                <span class="wpcf7-form-control-wrap notes">
                                <input type="text" name="notes" value="{{old('notes')}}" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                            </span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn send-button">{{word('create')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="update-address" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('update')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/address/update" method="post" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <div class="col-md-12">
                                <label>{{word('city')}}</label><br/>
                                @include('error',['input' => 'edit_city_id'])
                                <span class="wpcf7-form-control-wrap">
                                    <select class="form-control wpcf7-form-control" name="edit_city_id" id="city_id" aria-required="true" aria-invalid="false">
                                        <option disabled selected>{{word('choose_from_below')}}</option>
                                         @foreach($cities as $city)
                                            <option value="{{$city->id}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('text')}}</label><br />
                                @include('error',['input' => 'edit_text'])
                                <span class="wpcf7-form-control-wrap text">
                                    <input type="text" name="edit_text" value="" id="text" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('number')}}</label><br />
                                @include('error',['input' => 'edit_number'])
                                <span class="wpcf7-form-control-wrap number">
                                <input type="text" name="edit_number" value="" id="number" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('close_to')}}</label><br />
                                @include('error',['input' => 'edit_close_to'])
                                <span class="wpcf7-form-control-wrap close_to">
                                <input type="text" name="edit_close_to" value="" id="close_to" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                                </span>
                            </div>
                            <div class="col-md-12">
                                <label>{{word('notes')}} {{word('optional')}}</label><br />
                                <span class="wpcf7-form-control-wrap notes">
                                <input type="text" name="edit_notes" value="" id="notes" size="40" class="wpcf7-form-control input-text" aria-required="true" aria-invalid="false" />
                            </span>
                            </div>
                            <input type="hidden" name="edit_id" id="address_id">
                            @include('error',['input' => 'edit_id'])
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn send-button">{{word('update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delete-address" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('alert')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/address/delete" method="post">
                        <div class="modal-body">
                            <p>{{word('are_you_sure')}}</p>
                            {{csrf_field()}}
                            <input type="hidden" name="id" id="id_destroy" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn send-button">{{word('delete')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endsection
@section('script')
    <script>
        @if($errors->has('city_id') || $errors->has('text')|| $errors->has('number')|| $errors->has('close_to'))
            $('#btn_create').click();
        @endif

        @if($errors->has('edit_city_id') || $errors->has('edit_id') || $errors->has('edit_text')|| $errors->has('edit_number')|| $errors->has('edit_close_to'))
            $('#btn_{{old('address_id')}}').click();
        @endif

        function modal_edit(address)
        {
            console.log(address,'{{$errors}}');
            $('#address_id').val(address.id);
            $('#city_id').prop('value',address.city_id);
            $('#text').val(address.text);
            $('#number').val(address.number);
            $('#close_to').val(address.close_to);
            $('#notes').val(address.notes);
        }

        function modal_destroy(id)
        {
            $('#id_destroy').val(id);
        }
    </script>
@endsection
