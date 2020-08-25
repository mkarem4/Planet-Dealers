<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Country;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $banners = Banner::where(function($q) use($inputs)
        {
            if(isset($inputs['url'])) $q->where('url','like','%'.$inputs['url'].'%');
            if(isset($inputs['status']) && $inputs['status'] != 'expired') $q->where('status',$inputs['status']);
            if(isset($inputs['status']) && $inputs['status'] == 'expired') $q->where('expire_at','<',Carbon::today()->toDateString());
        })->paginate();

        $active_count = Banner::where('status','active')->count();
        $suspended_count = Banner::where('status','suspended')->count();
        $expired_count = Banner::where('expire_at','<',Carbon::today()->toDateString())->count();

        return view('admin.settings.banners.index',get_defined_vars());
    }


    public function create()
    {
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        return view('admin.settings.banners.single', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'country_id' => 'required',
                'url' => 'required',
                'expire_at' => 'required|date',
                'image' => 'required|image',
                'status' => 'in:active,suspended'
            ],
            [
                'country_id.required' => 'country_required',
                'url.required' => 'url_required',
                'expire_at.required' => 'expire_at_required',
                'expire_at.date' => 'expire_at_date',
                'image.required' => 'image_required',
                'image.image' => 'image_invalid',
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/banners/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/banners/'.$info['name']));
        $image->resize(1170, 172);
        $image->save(public_path('/uploads/banners/'.$info['name']));


        Banner::create
        (
            [
                'country_id' => $request->country_id,
                'status' => $status,
                'url' => $request->url,
                'expire_at' => $request->expire_at,
                'image' => $info['name'],
            ]
        );

        return redirect('/admin/settings/banners/index')->with('success','created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:banners,id'
            ]
        );

        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Banner::find($request->id);

        return view('admin.settings.banners.single', compact('edit','countries'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:banners,id',
                'country_id' => 'required',
                'url' => 'required',
                'expire_at' => 'required|date',
                'image' => 'sometimes|image',
            ],
            [
                'country_id.required' => 'country_required',
                'url.required' => 'url_required',
                'expire_at.required' => 'expire_at_required',
                'expire_at.date' => 'expire_at_date',
                'image.image' => 'image_invalid',
            ]
        );

        $banner = Banner::find($request->id);
        $banner->country_id = $request->country_id;
        $banner->url = $request->url;
        $banner->expire_at = $request->expire_at;
        if($request->image)
        {
            $info = unique_file_folder($request->image->getClientOriginalExtension());
            $request->image->move(public_path('/uploads/banners/'.$info['month']),$info['image']);

            $image = Image::make(public_path('/uploads/banners/'.$info['name']));
            $image->resize(1920, 466);
            $image->save(public_path('/uploads/banners/'.$info['name']));

            @unlink(public_path('/uploads/banners/'.$banner->getOriginal('image')));

            $banner->image = $info['name'];
        }
        $banner->save();

        return redirect('/admin/settings/banners/index')->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:banners,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        Banner::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:banners,id',
            ]
        );

        Banner::where('id',$request->id)->delete();

        return back()->with('success', 'deleted');
    }
}
