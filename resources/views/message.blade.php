<div>
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
            {{ word(Session::get('success')) }}
            <button type="button" class="close" data-dismiss="alert" style="float: {{lang() == 'ar' ? 'left' : 'right'}};"><span aria-hidden="true">&times;</span><span class="sr-only" >{{trans('trans.close')}}</span></button>
        </div>
    @endif


    @if(Session::has('error'))
        <div class="alert alert-danger" role="alert" style="text-align: {{lang() == 'ar' ? 'right' : 'left'}};">
            <button type="button" class="close" data-dismiss="alert" style="float: {{lang() == 'ar' ? 'left' : 'right'}};"><span aria-hidden="true">&times;</span><span class="sr-only">{{trans('trans.close')}}</span></button>
            {{ word(Session::get('error')) }}
        </div>
    @endif
</div>

<script>
    setTimeout('$(".alert").hide()',4000);
</script>
