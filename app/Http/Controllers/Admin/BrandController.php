<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $brands = Brand::where(function($q) use($inputs)
        {
            if(isset($inputs['status'])) $q->where('status',$inputs['status']);
        })->paginate();

        $active_count = Brand::where('status','active')->count();
        $suspended_count = Brand::where('status','suspended')->count();

        return view('admin.settings.brands.index',get_defined_vars());
    }


    public function create()
    {
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        return view('admin.settings.brands.single', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'country_id' => 'required',
                'image' => 'required|image',
                'status' => 'in:active,suspended'
            ],
            [
                'country_id.required' => 'country_required',
                'image.required' => 'image_required',
                'image.image' => 'image_invalid',
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/brands/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/brands/'.$info['name']));
        $image->resize(70, 70);
        $image->save(public_path('/uploads/brands/'.$info['name']));


        Brand::create
        (
            [
                'country_id' => $request->country_id,
                'status' => $status,
                'image' => $info['name'],
            ]
        );

        return redirect('/admin/settings/brands/index')->with('success','created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:brands,id'
            ]
        );

        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Brand::find($request->id);

        return view('admin.settings.brands.single', compact('edit','countries'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:brands,id',
                'country_id' => 'required',
                'image' => 'sometimes|image',
            ],
            [
                'country_id.required' => 'country_required',
                'image.image' => 'image_invalid',
            ]
        );

        $brand = Brand::find($request->id);
            $brand->country_id = $request->country_id;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/brands/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/brands/'.$info['name']));
                $image->resize(70, 70);
                $image->save(public_path('/uploads/brands/'.$info['name']));

                @unlink(public_path('/uploads/brands/'.$brand->getOriginal('image')));

                $brand->image = $info['name'];
            }
        $brand->save();

        return redirect('/admin/settings/brands/index')->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:brands,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        Brand::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:brands,id',
            ]
        );

        Brand::where('id',$request->id)->delete();

        return back()->with('success', 'deleted');
    }
}
