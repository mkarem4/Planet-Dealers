@extends('web.layout')
@section('content')
    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <nav class="woocommerce-breadcrumb" ><a href="/">{{word('home')}}</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>{{word('profile')}}</nav><!-- .woocommerce-breadcrumb -->
            <div class="titlepage">
                <a href="/profile/product/create"><button class="btn btn-info">{{word('add_product')}}</button></a>
            </div>
            <br/>
            <br/>
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
                                    <a class="nav-link active" href="/profile/products"  aria-selected="false">{{word('my_products')}}</a>
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
                            <div class="tab-pane fade show active in"  role="tabpanel" aria-labelledby="contact-tab">
                                <div class="profile-card">
                                    <h5 style="margin-bottom:0;">{{word('your_products')}}</h5>
                                    <div class="table-responsive" style="{{lang() == 'ar' ? 'margin-right: 5px;' : 'margin-left: 5px;'}}">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{word('status')}}</th>
                                                <th scope="col">{{word('name')}}</th>
                                                <th scope="col">{{word('category')}}</th>
                                                <th scope="col">{{word('price')}}</th>
                                                <th scope="col">{{word('actions')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($products as $product)
                                                <tr>
                                                    <th scope="row">{{$loop->iteration}}</th>
                                                    <td>{{$product->status}}</td>
                                                    <td>{{$product->name}}</td>
                                                    <td>{{$product->sec_cat->name}}</td>
                                                    <td>
                                                        {{$product->price_meta->price.' '.currency()}}
                                                        <br/>{{$product->price_meta->count}} {{word('unit')}}
                                                    </td>
                                                    <td>
                                                        <a href="/profile/product/{{$product->id}}/edit"><button class="btn btn-condensed btn-warning" title="{{word('edit')}}"><i class="fa fa-edit"></i></button></a>
                                                        @if($product->status == 'active')
                                                            <button class="btn btn-primary btn-sm" data-toggle="modal" onclick="modal_suspend({{$product->id}})" data-target="#suspend-product" title="{{word('suspend')}}"><i class="fa fa-minus-circle"></i></button>
                                                        @else
                                                            <button class="btn btn-success btn-sm" data-toggle="modal" onclick="modal_activate({{$product->id}})" data-target="#activate-product" title="{{word('activate')}}"><i class="fa fa-check-square"></i></button>
                                                        @endif
                                                        <button class="btn btn-danger btn-sm" data-toggle="modal" onclick="modal_destroy({{$product->id}})" data-target="#delete-product" title="{{word('delete')}}"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                        <div class="pagination-{{lang()}}">
                                            {{$products->links('vendor.pagination.bootstrap-4')}}<br/><br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="activate-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('alert')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/product/change_status" method="post">
                        <div class="modal-body">
                            <p>{{word('activate_product')}}</p>
                            {{csrf_field()}}
                            <input type="hidden" name="id" id="id_activate" value="">
                            <input type="hidden" name="status" value="active">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn send-button">{{word('activate')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="suspend-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('alert')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/product/change_status" method="post">
                        <div class="modal-body">
                            <p>{{word('suspend_product')}}</p>
                            {{csrf_field()}}
                            <input type="hidden" name="id" id="id_suspend" value="">
                            <input type="hidden" name="status" value="suspended">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn send-button">{{word('suspend')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delete-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{word('alert')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/profile/product/delete" method="post">
                        <div class="modal-body">
                            <p>{{word('delete_product')}}</p>
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
    function modal_activate(id)
    {
        $('#id_activate').val(id);
    }


    function modal_suspend(id)
    {
        $('#id_suspend').val(id);
    }


    function modal_destroy(id)
    {
        $('#id_destroy').val(id);
    }
</script>
@endsection
