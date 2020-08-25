<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BankController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $banks = Bank::latest()->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','country_id','status',lang().'_name as name',lang().'_desc as desc')->paginate(20)->appends($inputs);

        $active_count = Bank::where('status','active')->count();
        $suspended_count = Bank::where('status','suspended')->count();

        return view('admin.banks.index', get_defined_vars());
    }


    public function create()
    {
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        return view('admin.banks.single',get_defined_vars());
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'country_id' => 'required|exists:countries,id,deleted,0',
                'ar_name' => 'required|unique:variations,ar_name',
                'ar_desc' => 'required',
                'en_name' => 'required|unique:variations,en_name',
                'en_desc' => 'required',
                'status' => 'in:active,suspended'
            ],
            [
                'country_id.required' => 'country_parent_required',
                'country_id.exists' => 'country_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'ar_desc.required' => 'ar_desc_required',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'ar_name_exists',
                'en_desc.required' => 'en_desc_required',
                'status.in' => 'status_invalid'
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Bank::create
        (
            [
                'status' => $status,
                'country_id' => $request->country_id,
                'ar_name' => $request->ar_name,
                'ar_desc' => $request->ar_desc,
                'en_name' => $request->en_name,
                'en_desc' => $request->en_desc,
            ]
        );

        return redirect('/admin/banks/index')->with('success', 'created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:banks,id,deleted,0'
            ]
        );

        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Bank::find($id);

        return view('admin.banks.single', get_defined_vars());
    }


    public function update(Request $request)
    {

        $this->validate($request,
            [
                'id' => 'required|exists:banks,id,deleted,0',
                'country_id' => 'required|exists:countries,id,deleted,0',
                'ar_name' => 'required|unique:variations,ar_name,'.$request->id,
                'ar_desc' => 'required',
                'en_name' => 'required|unique:variations,en_name,'.$request->id,
                'en_desc' => 'required',
            ],
            [
                'country_id.required' => 'country_parent_required',
                'country_id.exists' => 'country_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'ar_desc.required' => 'ar_desc_required',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'ar_name_exists',
                'en_desc.required' => 'en_desc_required',
            ]
        );

        $bank = Bank::find($request->id);
            $bank->country_id = $request->country_id;
            $bank->ar_name = $request->ar_name;
            $bank->ar_desc = $request->ar_desc;
            $bank->en_name = $request->en_name;
            $bank->en_desc = $request->en_desc;
        $bank->save();

        return redirect('/admin/banks/index')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:banks,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Bank::where('id', $request->id)->update(['status' => $request->status]);

        return back()->with('success', 'status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:banks,id,deleted,0',
            ]
        );

        Bank::where('id', $request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
