<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class User
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('user')->user() && Auth::guard('user')->user()->status != 'suspended')
        {
            $lang = Session::get('user_locale') ? Session::get('user_locale') : 'en';
            user()->update(['lang' => $lang]);

            App::setLocale($lang);
            Carbon::setLocale($lang);

            return $next($request);
        }
        else
        {
            return redirect('/login');
        }
    }
}
