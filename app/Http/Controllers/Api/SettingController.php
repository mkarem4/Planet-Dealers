<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Country;
use App\Models\Pack;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function get_countries()
    {
        change_lang(request()->header('Accept-Language'));

        $countries = Country::where('type','main')->where('status','active')->select('id',lang().'_name as name',lang().'_currency as currency','tax_percentage')->get();
        foreach($countries as $country) $country['cities'] = Country::where('type','sub')->where('status','active')->where('parent_id',$country->id)->select('id',lang().'_name as name')->get();

        return r_json($countries);
    }


    public function get_abouts()
    {
        change_lang(request()->header('Accept-Language'));

        $abouts = About::select(lang().'_text as text')->first();

        return r_json($abouts,200,'object');
    }


    public function get_terms()
    {
        change_lang(request()->header('Accept-Language'));

        $terms = Term::select(lang().'_text as text')->first();

        return r_json($terms,200,'object');
    }
}
