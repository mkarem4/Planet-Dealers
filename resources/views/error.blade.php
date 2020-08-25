@if ($errors->has($input))
    <span class="help-block" style="float:{{lang() == 'ar' ? 'right' : 'left'}}; padding{{lang() == 'ar' ? '-right' : '-left'}}: 35px;">
            <strong style="color: red;">{{word($errors->first($input))}}</strong>
    </span>
@endif
