@if ($paginator->hasPages())
    <ul class="pagination pagination-{{lang()}}">
        <li class="@if($paginator->previousPageUrl() == NULL) disabled @endif"><a href="@if($paginator->previousPageUrl() != NULL){{$paginator->previousPageUrl()}}@endif"><span aria-hidden="true"><i class="fa fa-chevron-{{lang() == 'ar' ? 'right' : 'left'}}" aria-hidden="true"></i></span></a></li>
        @for($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="@if($i == $paginator->currentPage()) active @endif">
                @php
                    $params = '?';
                    unset($_GET['page']);
                    foreach($_GET as $key => $value)
                    {

                        $params .= $key.'='.$value.'&';
                    }
                @endphp
                <a href="{{$params}}{{'page='.$i}}">{{$i}}</a>
            </li>
        @endfor
        <li class="@if($paginator->nextPageUrl() == NULL) disabled @endif"><a href="@if($paginator->nextPageUrl() != NULL){{$paginator->nextPageUrl()}}@endif"><span aria-hidden="true"><i class="fa fa-chevron-{{lang() == 'ar' ? 'left' : 'right'}}" aria-hidden="true"></i></span></a></li>
    </ul>
@endif
