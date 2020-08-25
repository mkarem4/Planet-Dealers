<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class Admin
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->user() && Auth::guard('admin')->user()->status != 'suspended')
        {
            Session::get('admin_locale') ? true : Session::put('admin_locale','ar');

            $locale = admin_locale();

            admin()->update(['lang' => $locale]);

            App::setLocale($locale);
            Carbon::setLocale($locale);

            return $next($request);
        }
        else
        {
            return redirect('/admin/login');
        }
    }
}
