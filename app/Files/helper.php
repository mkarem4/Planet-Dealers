<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Models\Country;
use Illuminate\Support\Facades\Session;


function admin()
{
    return Auth::guard('admin')->user();
}


function user()
{
    return Auth::guard('user')->user();
}


function admin_permission($model,$role)
{
    return (admin()->permissions != [] && admin()->permissions != NULL && isset(admin()->permissions->$model) && in_array($role,admin()->permissions->$model));
}


function unique_file_folder($extension)
{
    $arr['month'] = Carbon::now()->format('M-Y');
    $arr['image'] = time() . uniqid().'.'.$extension;
    $arr['name'] = $arr['month'].'/'.$arr['image'];
    $arr['thumb_name'] = $arr['month'].'/thumb-'.$arr['image'];

    return $arr;
}


function quoted($value)
{
    return '"'.$value.'"';
}


function check_word($key)
{
    $exist = Translation::where('group','trans')->where('key',$key)->first();
    if(! $exist)
    {
        foreach(langs() as $lang)
        {
            Translation::create
            (
                [
                    'locale' => $lang,
                    'group' => 'trans',
                    'key' => $key,
                ]
            );
        }
    }

    return true;
}


function word($key)
{
    check_word($key);
    return trans('trans.'.$key);
}


function lang()
{
    return App::getLocale();
}


function langs()
{
    return Config::get('languages');
}


function langs_str()
{
    return implode(',',Config::get('languages'));
}


function langs_count()
{
    return count(Config::get('languages'));
}


function change_lang($lang)
{
    App::setLocale($lang);
    Carbon::setLocale($lang);

    return true;
}


function jwt()
{
    return request()->header('jwt');
}


function colors()
{
    return ["#33414E","#0074D9","#B70004","#840003","#FF4136","#2ECC40","#FF851B","#358E33","#7FDBFF","#B10DC9","#FFDC00","#001f3f","#39CCCC","#01FF70","#85144b","#F012BE","#3D9970","#111111","#921880"];
}


function limit($str,$limit)
{
    return Str::limit($str,$limit,'...');
}


function r_json($arr = [],$status = 200,$type = 'array')
{
//
    if($type == 'array')
    {
        if(count($arr)) return response()->json($arr,$status,[],JSON_NUMERIC_CHECK);
        elseif(count($arr) && $status == 204) return response('',$status,[],JSON_NUMERIC_CHECK);
        elseif($status == 200) return response($arr,$status,[],JSON_NUMERIC_CHECK);
        else return response([],$status,[],JSON_NUMERIC_CHECK);
    }
    else
    {
        return response()->json($arr,$status,[],JSON_NUMERIC_CHECK);
    }

//    return response()->json($arr,200,[],JSON_NUMERIC_CHECK);
}


function ar_search($text)
{

    $new_text = str_replace('أ','ا',$text);
    $new_text = str_replace('إ','ا',$new_text);
    $new_text = str_replace('آ','ا',$new_text);
    $new_text = str_replace('ة','ه',$new_text);
    $new_text = str_replace('ئ','ي',$new_text);
    $new_text = str_replace('َ','',$new_text);
    $new_text = str_replace('ً','',$new_text);
    $new_text = str_replace('ُ','',$new_text);
    $new_text = str_replace('ٌ','',$new_text);
    $new_text = str_replace('ِ','',$new_text);
    $new_text = str_replace('ٍ','',$new_text);
    $new_text = str_replace('‘','',$new_text);
    $new_text = str_replace(',','',$new_text);
    $new_text = str_replace('.','',$new_text);
    $new_text = str_replace('~','',$new_text);
    $new_text = str_replace('ْ','',$new_text);

    return $new_text;
}


function get_rand($n)
{
    $pot[] = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    $pot[] = [0,1,2,3,4,5,6,7,8,9];

    $x = '';

    for($i = 0; $i < $n; $i ++)
    {
        $index = array_rand($pot);
        $index_index = array_rand($pot[$index]);

        $x .= $pot[$index][$index_index];
    }

    return $x;
}


function country()
{
    $country_id = Session::get('country_id');
    return Country::where('id',$country_id)->select('id',lang().'_name as name',lang().'_currency as currency')->first();
}


function currency()
{

    if(! Session::get('currency'))
    {
        $currency = Country::where('id',country()->id)->select('ar_currency','en_currency')->first();
        Session::put('currency',lang() == 'ar' ? $currency->ar_currency : $currency->en_currency);
    }

    return Session::get('currency');
}


function city()
{
    $city_id = Session::get('city_id');
    return Country::where('id',$city_id)->select('id',lang().'_name as name')->first();
}


function admin_locale()
{
    return Session::get('admin_locale');
}

