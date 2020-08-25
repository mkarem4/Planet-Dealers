<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Pack;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class PackController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $packs = Pack::latest()->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
                else $q->where(lang().'_name as name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','status',lang().'_name as name','price','month_count','image')->paginate(20)->appends($inputs);

        $active_count = Pack::where('status','active')->count();
        $suspended_count = Pack::where('status','suspended')->count();

        return view('admin.packs.index', get_defined_vars());
    }


    public function create()
    {
        return view('admin.packs.single');
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'ar_name' => 'required|unique:packs,ar_name',
                'en_name' => 'required|unique:packs,en_name',
                'price' => 'required|numeric',
                'month_count' => 'required|numeric',
                'image' => 'required|image',
                'status' => 'in:active,suspended'
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'ar_name_exists',
                'price.required' => 'field_required',
                'price.numeric' => 'field_invalid',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
                'status.in' => 'status_invalid'
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/packs/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/packs/'.$info['name']));
            $image->resize(75, 80);
        $image->save(public_path('/uploads/packs/'.$info['name']));

        Pack::create
        (
            [
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
                'month_count' => $request->month_count,
                'price' => $request->price,
                'image' => $info['name'],
            ]
        );

        return redirect('/admin/packs/index')->with('success', 'created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:packs,id,deleted,0'
            ]
        );

        $edit = Pack::find($id);

        return view('admin.packs.single', get_defined_vars());
    }


    public function update(Request $request)
    {

        $this->validate($request,
            [
                'id' => 'required|exists:packs,id,deleted,0',
                'ar_name' => 'required|unique:packs,ar_name,'.$request->id,
                'en_name' => 'required|unique:packs,en_name,'.$request->id,
                'price' => 'required|numeric',
                'month_count' => 'required|numeric',
                'image' => 'sometimes|image',
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'ar_name_exists',
                'price.required' => 'field_required',
                'price.numeric' => 'field_invalid',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
            ]
        );

        $pack = Pack::find($request->id);
            $pack->ar_name = $request->ar_name;
            $pack->en_name = $request->en_name;
            $pack->price = $request->price;
            $pack->month_count = $request->month_count;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/packs/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/packs/'.$info['name']));
                $image->resize(75, 80);
                $image->save(public_path('/uploads/packs/'.$info['name']));

                @unlink(public_path('/uploads/packs/'.$pack->getOriginal('image')));
                $pack->image = $info['name'];
            }
        $pack->save();

        return redirect('/admin/packs/index')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:packs,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Pack::where('id', $request->id)->update(['status' => $request->status]);

        return back()->with('success', 'status_changed');
    }


    public function make_default(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:packs,id,deleted,0,default,0',
            ]
        );

        Pack::where('id', $request->id)->update(['default' => 1]);
        Pack::where('id','1=',$request->id)->update(['default' => 0]);

        return back()->with('success', 'updated');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:packs,id,deleted,0',
            ]
        );

        Pack::where('id', $request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
