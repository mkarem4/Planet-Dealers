<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function change(Request $request)
    {
        $this->validate($request,
            [
                'lang' => 'required|in:ar,en'
            ]
        );

        $segment = request()->segment(1);

        if($segment == 'admin') Session::put('admin_locale',$request->lang);
        elseif($segment == 'user')
        {
            Session::put('user_locale',$request->lang);
            App::setLocale($request->lang);
            Carbon::setLocale($request->lang);
        }

        return back();
    }
}
