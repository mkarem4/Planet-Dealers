@extends('admin.layout')
@section('content')
    <ul class="breadcrumb">
        <li><a href="/admin/dashboard">{{word('home')}}</a></li>
        <li class="active">{{word('bank_transfers')}}</li>
        @if(count($inputs))
            <li class="active">{{word('search_result')}}</li>
        @endif
    </ul>
    @include('message')
    <div class="page-content-wrap">
        @if(! isset($parent))
            <div class="row widget-custom" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-12 index-widget">
                    <div class="col-md-3">
                        <div class="widget widget-primary widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-cog fa-spin"></span>
                            </div>
                            <div class="widget-data" >
                                <div class="widget-int num-count">{{$pending_count}}</div>
                                <div class="widget-title">{{word('pending_transfers')}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-success widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-check fa-small"></span>
                            </div>
                            <div class="widget-data">
                                <div class="widget-int num-count">{{$confirmed_count}}</div>
                                <div class="widget-title">{{word('confirmed_transfers')}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-danger widget-item-icon">
                            <div class="widget-item-left">
                                <span class="fa fa-times"></span>
                            </div>
                            <div class="widget-data">
                                <div class="widget-int num-count">{{$declined_count}}</div>
                                <div class="widget-title">{{word('declined_transfers')}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form action="" method="get">
            <div class="row index-widget" style="direction: {{lang() == 'ar' ? 'rtl' : 'ltr'}};">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="{{word('name')}}" value="{{isset($inputs['name']) ? $inputs['name'] : ''}}" name="name">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="bank">
                            <option value="all" selected>{{word('all_banks')}}</option>
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}" {{isset($inputs['bank']) && $inputs['bank'] == $bank->id ? 'selected' : ''}}>{{$bank->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select class="form-control select" name="status">
                            <option value="all" selected>{{word('all_status')}}</option>
                            <option value="pending" {{isset($inputs['status']) && $inputs['status'] == 'pending' ? 'selected' : ''}}>{{word('pending')}}</option>
                            <option value="confirmed" {{isset($inputs['status']) && $inputs['status'] == 'confirmed' ? 'selected' : ''}}>{{word('suspended')}}</option>
                            <option value="declined" {{isset($inputs['status']) && $inputs['status'] == 'declined' ? 'selected' : ''}}>{{word('declined')}}</option>
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
                <!-- START BASIC TABLE SAMPLE -->
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="rtl_th">#</th>
                                    <th class="rtl_th">{{word('bank')}}</th>
                                    <th class="rtl_th">{{word('name')}}</th>
                                    <th class="rtl_th">{{word('pack')}}</th>
                                    <th class="rtl_th">{{word('transfer_image')}}</th>
                                    <th class="'rtl_th">{{word('date')}}</th>
                                    <th class="'rtl_th">{{word('status')}}</th>
                                    <th class="rtl_th">{{word('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($transfers as $transfer)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <span class="label label-form label-info">{{$transfer->bank->name}}</span><br/>
                                        </td>
                                        <td>
                                            <span class="label label-form label-primary">{{$transfer->pack->name}}</span><br/>
                                            <span class="label label-form label-primary">{{$transfer->pack->price}} {{word('sar')}}</span>
                                        </td>
                                        <td>
                                            <span class="label label-form label-success">{{$transfer->user_name}}</span><br/>
                                            <span class="label label-form label-success">{{$transfer->account_no}}</span>
                                        </td>
                                        <td>
                                            <a class="gallery-item-{{lang() == 'ar' ? 'right' : 'left'}}" href="{{$transfer->image}}" title="{{$transfer->image}}" data-gallery style="padding : 10px 0px 0px 0px;">
                                                <div class="image">
                                                    <img class="table-image" src="{{$transfer->image}}">
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="label label-form label-default">{{$transfer->created_at->toTimeString()}}</span><br/>
                                            <span class="label label-form label-default">{{$transfer->created_at->toDateString()}}</span>
                                        </td>
                                        <td>
                                            <span class="label @if($transfer->status == 'pending') label-primary @elseif($transfer->status == 'confirmed') label-success @else label-danger @endif label-form">{{word($transfer->status)}}</span>
                                        </td>

                                        <td>
                                            @if($transfer->status == 'pending')
                                                <button class="btn btn-success btn-condensed mb-control" onclick="modal_activate({{$transfer->id}})" data-box="#message-box-success" title="{{word('confirm')}}"><i class="fa fa-check-square"></i></button>
                                                <button class="btn btn-danger btn-condensed mb-control" onclick="modal_destroy({{$transfer->id}})" data-box="#message-box-danger" title="{{word('decline')}}"><i class="fa fa-times"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">{{word('sorry_no_data')}}</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                            <div class="panel-body">
                               {!! $transfers->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- success with sound -->
    <div class="message-box message-box-success animated fadeIn" data-sound="alert" id="message-box-success">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-check-square"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('confirm_transfer')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/transfer/change_status" class="buttons">
                        {{csrf_field()}}
                        <input class="form-control" name="admin_notes" placeholder="{{word('notes')}}"><br/>
                        <input type="hidden" name="id" id="id_activate" value="">
                        <input type="hidden" name="status" value="confirmed">
                        <button class="btn btn-success btn-lg pull-right">{{word('confirm')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end success with sound -->

    <!-- danger with sound -->
    <div class="message-box message-box-danger animated fadeIn" data-sound="alert" id="message-box-danger">
        <div class="mb-container">
            <div class="mb-middle warning-msg alert-msg">
                <div class="mb-title"><span class="fa fa-trash"></span>{{word('alert')}}</div>
                <div class="mb-content">
                    <p>{{word('decline_transfer')}}</p>
                </div>
                <div class="mb-footer buttons">
                    <form method="post" action="/admin/transfer/change_status" class="buttons">
                        {{csrf_field()}}
                        <input class="form-control" name="admin_notes" placeholder="{{word('notes')}}"><br/>
                        <input type="hidden" name="id" id="id_destroy" value="">
                        <input type="hidden" name="status" value="declined">
                        <button class="btn btn-danger btn-lg pull-right">{{word('decline')}}</button>
                    </form>
                    <button class="btn btn-default btn-lg pull-right mb-control-close" style="margin-right: 5px;">{{word('close')}}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end danger with sound -->
@endsection
