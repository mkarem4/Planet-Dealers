<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variation;
use App\Models\VariationOption;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $variations = Variation::where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->latest()->paginate(20)->appends($inputs);

        $active_count = Variation::where('status','active')->count();
        $suspended_count = Variation::where('status','suspended')->count();

        $all_variations = Variation::latest()->select('id',lang().'_name as name')->get();

        return view('admin.variations.index', get_defined_vars());
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'status' => 'in:active,suspended',
                'ar_name' => 'required|unique:variations,ar_name',
                'en_name' => 'required|unique:variations,en_name'
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'ar_name_exists',
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Variation::create
        (
            [
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name
            ]
        );

        return back()->with('success','created');
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variations,id,deleted,0',
                'edit_ar_name' => 'required|unique:variations,ar_name,'.$request->id,
                'edit_en_name' => 'required|unique:variations,en_name,'.$request->id
            ],
            [
                'edit_ar_name.required' => 'ar_name_required',
                'edit_ar_name.unique' => 'ar_name_exists',
                'edit_en_name.required' => 'en_name_required',
                'edit_en_name.unique' => 'ar_name_exists',
            ]
        );

        Variation::find($request->id)->update
        (
            [
                'ar_name' => $request->edit_ar_name,
                'en_name' => $request->edit_en_name,
            ]
        );

        return back()->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variations,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Variation::where('id', $request->id)->update(['status' => $request->status]);

        return back()->with('success', 'status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variations,id,deleted,0'
            ]
        );

        Variation::find($request->id)->update(['deleted' => 1]);

        return back()->with('success','deleted');
    }


    public function options($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:variations,id,deleted,0'
            ]
        );

        $inputs = request()->except('page','id');
        $variation = Variation::where('id',$id)->select('id',lang().'_name as name')->first();

        $options = VariationOption::where('parent_id',$id)->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->latest()->paginate(20)->appends($inputs);

        $active_count = VariationOption::where('parent_id',$id)->where('status','active')->count();
        $suspended_count = VariationOption::where('parent_id',$id)->where('status','suspended')->count();

        $all_variations = Variation::latest()->select('id',lang().'_name as name')->get();

        return view('admin.variations.options',get_defined_vars());
    }


    public function option_store(Request $request)
    {
        $this->validate($request,
            [
                'parent_id_option' => 'required|exists:variations,id,deleted,0',
                'status_option' => 'in:active,suspended',
                'ar_name_option' => 'required|unique:variations,ar_name',
                'en_name_option' => 'required|unique:variations,en_name'
            ],
            [
                'parent_id_option.required' => 'parent_variation_required',
                'parent_id_option.exists' => 'parent_variation_invalid',
                'ar_name_option.required' => 'ar_name_required',
                'ar_name_option.unique' => 'ar_name_exists',
                'en_name_option.required' => 'en_name_required',
                'en_name_option.unique' => 'ar_name_exists',
            ]
        );

        $status = $request->status_option ? 'active' : 'suspended';

        VariationOption::create
        (
            [
                'status' => $status,
                'parent_id' => $request->parent_id_option,
                'ar_name' => $request->ar_name_option,
                'en_name' => $request->en_name_option
            ]
        );

        return redirect('/admin/variation/'.$request->parent_id_option.'/options')->with('success','created');
    }


    public function option_update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variation_options,id,deleted,0',
                'edit_parent_id' => 'required|exists:variations,id,deleted,0',
                'edit_ar_name' => 'required|unique:variation_options,ar_name,'.$request->id,
                'edit_en_name' => 'required|unique:variation_options,en_name,'.$request->id
            ],
            [
                'edit_parent_id.required' => 'parent_variation_required',
                'edit_parent_id.exists' => 'parent_variation_invalid',
                'edit_ar_name.required' => 'ar_name_required',
                'edit_ar_name.unique' => 'ar_name_exists',
                'edit_en_name.required' => 'en_name_required',
                'edit_en_name.unique' => 'ar_name_exists',
            ]
        );

        VariationOption::find($request->id)->update
        (
            [
                'parent_id' => $request->edit_parent_id,
                'ar_name' => $request->edit_ar_name,
                'en_name' => $request->edit_en_name,
            ]
        );

        return back()->with('success','updated');
    }


    public function option_change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variation_options,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        VariationOption::where('id', $request->id)->update(['status' => $request->status]);

        return back()->with('success', 'status_changed');
    }


    public function option_destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:variation_options,id,deleted,0'
            ]
        );

        VariationOption::find($request->id)->update(['deleted' => 1]);

        return back()->with('success','deleted');
    }
}
