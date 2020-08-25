<?php

namespace App\Http\Controllers\Admin;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $countries = Country::where('type','main')->latest()->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','type','status',lang().'_name as name')->paginate(20)->appends($inputs);

        $active_count = Country::where('type','main')->where('status','active')->count();
        $suspended_count = Country::where('type','main')->where('status','suspended')->count();

        return view('admin.countries.index', get_defined_vars());
    }


    public function subs($parent_id,Request $request)
    {
        $request->merge(['parent_id' => $parent_id]);
        $this->validate($request,
            [
                'parent_id' => 'required|exists:countries,id,type,main,deleted,0'
            ]
        );

        $parent = Country::where('id',$parent_id)->select('id',lang().'_name as name')->first();
        $inputs = request()->except('parent_id','page');

        $countries = Country::where('type','sub')->where('parent_id',$parent_id)->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','type','status',lang().'_name as name')->paginate(20);

        $active_count = $countries->where('status','active')->count();
        $suspended_count = $countries->where('status','suspended')->count();

        return view('admin.countries.index',get_defined_vars());
    }


    public function create()
    {
        return view('admin.countries.single');
    }


    public function create_sub()
    {
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        return view('admin.countries.sub_single',get_defined_vars());
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'ar_name' => 'required|unique:countries',
                'en_name' => 'required|unique:countries',
                'ar_currency' => 'required|unique:countries',
                'en_currency' => 'required|unique:countries',
                'status' => 'in:active,suspended',
                'tax_percentage' => 'required|numeric|min:0',
                'code' => 'required|numeric',
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
                'ar_currency.required' => 'ar_currency_required',
                'ar_currency.unique' => 'ar_currency_exists',
                'en_currency.required' => 'en_currency_required',
                'en_currency.unique' => 'en_currency_exists',
                'status.in' => 'status_invalid',
                'tax_percentage.required' => 'tax_percentage_required',
                'tax_percentage.numeric' => 'tax_percentage_invalid',
                'tax_percentage.min' => 'tax_percentage_invalid',
                'code.required' => 'field_required',
                'code.numeric' => 'field_invalid',
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Country::create
        (
            [
                'type' => 'main',
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
                'ar_currency' => $request->ar_currency,
                'en_currency' => $request->en_currency,
                'tax_percentage' => $request->tax_percentage,
                'code' => $request->code,
            ]
        );

        return redirect('/admin/countries/index')->with('success', 'created');
    }


    public function store_sub(Request $request)
    {
        $this->validate($request,
            [
                'parent_id' => 'required|exists:countries,id,type,main,deleted,0',
                'ar_name' => 'required|unique:countries',
                'en_name' => 'required|unique:countries',
                'status' => 'in:active,suspended'
            ],
            [
                'parent_id.required' => 'govern_parent_required',
                'parent_id.exists' => 'govern_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
                'status.in' => 'phone_key_invalid',
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Country::create
        (
            [
                'type' => 'sub',
                'status' => $status,
                'parent_id' => $request->parent_id,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
            ]
        );

        return redirect('/admin/country/'. $request->parent_id .'/cities')->with('success', 'created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0,type,main'
            ]
        );

        $edit = Country::find($id);

        return view('admin.countries.single', get_defined_vars());
    }


    public function edit_sub($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0,type,sub'
            ]
        );

        $edit = Country::find($id);
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();

        return view('admin.countries.sub_single', get_defined_vars());
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0,type,main',
                'ar_name' => 'required|unique:countries,ar_name,'.$request->id,
                'en_name' => 'required|unique:countries,en_name,'.$request->id,
                'ar_currency' => 'required|unique:countries,ar_currency,'.$request->id,
                'en_currency' => 'required|unique:countries,en_currency,'.$request->id,
                'tax_percentage' => 'required|numeric|min:0',
                'code' => 'required|numeric'
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
                'ar_currency.required' => 'ar_currency_required',
                'ar_currency.unique' => 'ar_currency_exists',
                'en_currency.required' => 'en_currency_required',
                'en_currency.unique' => 'en_currency_exists',
                'tax_percentage.required' => 'tax_percentage_required',
                'tax_percentage.numeric' => 'tax_percentage_invalid',
                'tax_percentage.min' => 'tax_percentage_invalid',
                'code.required' => 'field_required',
                'code.numeric' => 'phone_key_invalid',
            ]
        );

        $country = Country::find($request->id);
            $country->ar_name = $request->ar_name;
            $country->en_name = $request->en_name;
            $country->ar_currency = $request->ar_currency;
            $country->en_currency = $request->en_currency;
            $country->tax_percentage = $request->tax_percentage;
            $country->code = $request->code;
        $country->save();

        return redirect('/admin/countries/index')->with('success', 'updated');
    }


    public function update_sub(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0,type,sub',
                'parent_id' => 'required|exists:countries,id,deleted,0,type,main',
                'ar_name' => 'required|unique:countries,ar_name,'.$request->id,
                'en_name' => 'required|unique:countries,en_name,'.$request->id,
            ],
            [
                'parent_id.required' => 'country_parent_required',
                'parent_id.exists' => 'country_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
            ]
        );

        $city = Country::find($request->id);
            $city->parent_id = $request->parent_id;
            $city->ar_name = $request->ar_name;
            $city->en_name = $request->en_name;
        $city->save();

        return redirect('/admin/country/'. $request->parent_id .'/cities')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Country::where('id', $request->id)->orWhere('parent_id',$request->id)->update(['status' => $request->status]);

        return back()->with('success', 'status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:countries,id,deleted,0',
            ]
        );

        Country::where('id', $request->id)->orWhere('parent_id',$request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
