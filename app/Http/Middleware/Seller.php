<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class Seller
{
    public function handle($request, Closure $next)
    {
        $seller = Auth::guard('user')->user();

        if ($seller->type == 'seller' && boolval($seller->getOriginal('pack_id')) && $seller->expire_at >= Carbon::today()->toDateString())
        {
            return $next($request);
        }
        else
        {
            return back()->with('error','sorry_pack_required');
        }
    }
}
