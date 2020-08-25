<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class Buyer
{
    public function handle($request, Closure $next)
    {
        $seller = Auth::guard('user')->user();

        if ($seller->type == 'buyer' )
        {
            return $next($request);
        }
        else
        {
            return back()->with('error','sorry_not_allowed');
        }
    }
}
