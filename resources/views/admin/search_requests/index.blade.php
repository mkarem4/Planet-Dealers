@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('dashboard')}}</a></li>
        <li class="active">{{word('contact_us_msgs')}}</li>
    </ul>
    @include('message')
    <div class="page-content-wrap">
        <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
            <div class="col-md-12 index-widget">
                <div class="col-md-3">
                    <div class="widget widget-primary widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-cog fa-spin"></span>
                        </div>
                        <div class="widget-data" >
                            <div class="widget-int num-count">{{$active_count}}</div>
                            <div class="widget-title">{{word('open_messages')}}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-success widget-item-icon">
                        <div class="widget-item-left">
                            <span class="fa fa-check"></span>
                        </div>
                        <div class="widget-data">
                            <div class="widget-int num-count">{{$closed_count}}</div>
                            <div class="widget-title">{{word('closed_messages')}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="{{word('email_phone')}}" value="{{isset($inputs['keyword']) ? $inputs['keyword'] : ''}}" name="keyword">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="type">
                            <option value="all" selected>{{word('all_types')}}</option>
                            <option value="product" {{isset($inputs['type']) && $inputs['type'] == '0' ? 'selected' : ''}}>{{word('product')}}</option>
                            <option value="seller" {{isset($inputs['type']) && $inputs['type'] == '1' ? 'selected' : ''}}>{{word('seller')}}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="0" {{isset($inputs['status']) && $inputs['status'] == '0' ? 'selected' : ''}}>{{word('open')}}</option>
                            <option value="1" {{isset($inputs['status']) && $inputs['status'] == '1' ? 'selected' : ''}}>{{word('closed')}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row index-widget mt-2">
                <div class="form-group" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}}">
                    <a><button type="submit" class="btn btn-info">{{word('filter')}}</button></a>
                    <a href="?" class="btn btn-danger">{{word('clear')}}</a>
                </div>
            </div>
        </form>
    </div>

    <div class="page-content-wrap" style="padding-top: 5px;">
        <div class="row">
            <div class="col-md-12 col-xs-12">
            @include('message')
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('type')}}</th>
                                    <th class="rtl_th">{{word('from')}}</th>
                                    <th class="rtl_th">{{word('address')}}</th>
                                    <th class="rtl_th">{{word('text')}}</th>
                                    <th class="rtl_th">{{word('date')}}</th>
                                    <th class="rtl_th">{{word('actions')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($searches as $search)
                                    <tr id="{{$search->id}}">
                                        <td>{{$loop->iteration}}</td>
                                        <td><span class="label label-form label-{{$search->type == 'product' ? 'info' : 'success'}}">{{word('a_'.$search->type)}}</span></td>
                                        <td>
                                            <span class="label label-form label-info">{{$search->name}}</span><br/>
                                            <a href="mailTo:{{$search->email}}">{{$search->email}}</a><br/>
                                            <a href="tel:{{$search->phone}}">{{$search->phone}}</a>
                                        </td>
                                        <td><span class="label label-form label-primary">{{$search->address}}</span></td>
                                        <td><span class="label label-form label-danger">{{limit($search->text,50)}}</span></td>
                                        <td>
                                            <span class="label label-primary label-form">{{$search->created_at->toTimeString()}}</span><br/>
                                            <span class="label label-default label-form">{{$search->created_at->toDateString()}}</span>
                                        </td>
                                        <td>
                                            <a href="/admin/search_request/{{$search->id}}/show"><button class="btn btn-info btn-condensed" title="{{word('show')}}"><i class="fa fa-eye"></i></button></a>
                                            @if(! $search->closed)
                                                <button class="btn btn-success btn-condensed" id="request_{{$search->id}}" onclick="modal_close({{$search->id}})" title="{{word('close')}}"><i class="fa fa-check-square"></i></button>
                                            @endif
                                            <button class="btn btn-danger btn-condensed" onclick="modal_delete({{$search->id}})" title="{{word('delete')}}"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$searches->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function modal_close($id)
        {
            var id = $id;

            if (id) {
                $.ajax({
                    url: '/admin/search_request/' + id + '/close',
                    type: "GET",

                    dataType: "json",

                    success: function (data)
                    {
                        if(data.status == 'success')
                        {
                            $('#request_'+id).remove();
                        }
                    }
                });

            }
        }


        function modal_delete($id)
        {
            var id = $id;

            if (id) {
                $.ajax({
                    url: '/admin/search_request/' + id + '/delete',
                    type: "GET",

                    dataType: "json",

                    success: function (data)
                    {
                        if(data.status == 'success')
                        {
                            $('#'+id).remove();
                        }
                    }
                });

            }
        }
    </script>
@endsection
