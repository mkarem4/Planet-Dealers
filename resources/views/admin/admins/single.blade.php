@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li><a href="/admin/admins/index">{{word('admins')}}</a></li>
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
                <form class="form-horizontal" id="jvalidate" method="post" @if(isset($edit)) action="/admin/admin/update" @else action="/admin/admin/store" @endif enctype="multipart/form-data">
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
                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                <label class="col-md-3 col-xs-12 control-label">{{word('name')}}</label>
                                <div class="col-md-6 col-xs-12">
                                    <input type="text" class="form-control" name="name" @if(isset($edit)) value="{{$edit->name}}" @else value="{{old('name')}}" @endif/>
                                    @include('error', ['input' => 'name'])
                                </div>
                            </div>
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

                            @if(admin_permission('admins','update'))
                                <h2>{{word('permissions')}}</h2>

                                <div class="form-group {{ $errors->has('permissions') ? ' has-error' : '' }}">
                                    <label class="col-md-3 col-xs-12 control-label"></label>
                                    <div class="col-md-6 col-xs-12">
                                        @include('error',['input' => 'permissions'])
                                        <div class="table-responsive">
                                            <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="rtl_th">{{word('section')}}</th>
                                            <th class="rtl_th">{{word('all')}}</th>
                                            <th class="rtl_th">{{word('read')}}</th>
                                            <th class="rtl_th">{{word('create')}}</th>
                                            <th class="rtl_th">{{word('edit')}}</th>
                                            <th class="rtl_th">{{word('delete')}}</th>
                                            <th class="rtl_th">{{word('change_status')}}</th>
                                            <th class="rtl_th">{{word('others')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{word('cities')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[addresses][read]" @if(isset($edit) && isset($edit->permissions->addresses)) {{in_array('read',$edit->permissions->addresses) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[addresses][create]" @if(isset($edit) && isset($edit->permissions->addresses)) {{in_array('create',$edit->permissions->addresses) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[addresses][update]" @if(isset($edit) && isset($edit->permissions->addresses)) {{in_array('update',$edit->permissions->addresses) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[addresses][delete]" @if(isset($edit) && isset($edit->permissions->addresses)) {{in_array('delete',$edit->permissions->addresses) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[addresses][change_status]" @if(isset($edit) && isset($edit->permissions->addresses)) {{in_array('change_status',$edit->permissions->addresses) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{word('admins')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[admins][read]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('read',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[admins][create]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('create',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[admins][update]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('update',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[admins][delete]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('delete',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[admins][change_status]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('change_status',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div>
                                                        <label>{{word('permissions')}}</label>
                                                        <label class="switch switch-small switch-margin">
                                                            <input type="checkbox" value="1" name="permissions[admins][permissions]" @if(isset($edit) && isset($edit->permissions->admins)) {{in_array('permissions',$edit->permissions->admins) ? 'checked' : ''}} @endif/>
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{word('stores')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[stores][read]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('read',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[stores][create]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('create',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[stores][update]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('update',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[stores][delete]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('delete',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[stores][change_status]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('change_status',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div>
                                                        <label>{{word('specialize')}}</label>
                                                        <label class="switch switch-small switch-margin">
                                                            <input type="checkbox" value="1" name="permissions[stores][specialize]" @if(isset($edit) && isset($edit->permissions->stores)) {{in_array('specialize',$edit->permissions->stores) ? 'checked' : ''}} @endif/>
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{word('users')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[users][read]" @if(isset($edit) && isset($edit->permissions->users)) {{in_array('read',$edit->permissions->users) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[users][create]" @if(isset($edit) && isset($edit->permissions->users)) {{in_array('create',$edit->permissions->users) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[users][update]" @if(isset($edit) && isset($edit->permissions->users)) {{in_array('update',$edit->permissions->users) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[users][delete]" @if(isset($edit) && isset($edit->permissions->users)) {{in_array('delete',$edit->permissions->users) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[users][change_status]" @if(isset($edit) && isset($edit->permissions->users)) {{in_array('change_status',$edit->permissions->users) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>{{word('statistics')}}</td>
                                                <td></td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[statistics][read]" @if(isset($edit) && isset($edit->permissions->statistics)) {{in_array('read',$edit->permissions->statistics) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{word('about_us')}}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[about_us][update]" @if(isset($edit) && isset($edit->permissions->about_us)) {{in_array('update',$edit->permissions->about_us) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>{{word('terms')}}</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[terms][update]" @if(isset($edit) && isset($edit->permissions->terms)) {{in_array('update',$edit->permissions->terms) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>{{word('faqs')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[faqs][read]" @if(isset($edit) && isset($edit->permissions->faqs)) {{in_array('read',$edit->permissions->faqs) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[faqs][create]" @if(isset($edit) && isset($edit->permissions->faqs)) {{in_array('create',$edit->permissions->faqs) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[faqs][update]" @if(isset($edit) && isset($edit->permissions->faqs)) {{in_array('update',$edit->permissions->faqs) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[faqs][delete]" @if(isset($edit) && isset($edit->permissions->faqs)) {{in_array('delete',$edit->permissions->faqs) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>{{word('notifications')}}</td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" class="check_all"/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[notifications][read]" @if(isset($edit) && isset($edit->permissions->notifications)) {{in_array('read',$edit->permissions->notifications) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[notifications][create]" @if(isset($edit) && isset($edit->permissions->notifications)) {{in_array('create',$edit->permissions->notifications) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                                <td>
                                                    <label class="switch switch-small switch-margin">
                                                        <input type="checkbox" value="1" name="permissions[notifications][delete]" @if(isset($edit) && isset($edit->permissions->notifications)) {{in_array('delete',$edit->permissions->notifications) ? 'checked' : ''}} @endif/>
                                                        <span></span>
                                                    </label>
                                                </td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

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
                'name': {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                'phone': {
                    required: true,
                    digits : true
                },
                password: {
                    required: bool,
                    minlength: 6
                },
                'password_confirmation': {
                    required: bool,
                    minlength: 6,
                    equalTo: "#password"
                },
                'image': {
                    required : bool
                }
            },
        });
    </script>
@endsection
