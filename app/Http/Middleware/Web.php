<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;


class Web
{
    public function handle($request, Closure $next)
    {
            $lang = Session::get('user_locale') ? Session::get('user_locale') : 'en';

            App::setLocale($lang);
            Carbon::setLocale($lang);
            Session::put('user_locale',$lang);

            return $next($request);
    }
}
