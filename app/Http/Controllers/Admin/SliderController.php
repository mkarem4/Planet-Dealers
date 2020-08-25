<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Slide;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $slides = Slide::where(function($q) use($inputs)
        {
            if(isset($inputs['url'])) $q->where('url','like','%'.$inputs['url'].'%');
            if(isset($inputs['status']) && $inputs['status'] != 'expired') $q->where('status',$inputs['status']);
            if(isset($inputs['status']) && $inputs['status'] == 'expired') $q->where('expire_at','<',Carbon::today()->toDateString());
        })->paginate();

        $active_count = Slide::where('status','active')->count();
        $suspended_count = Slide::where('status','suspended')->count();
        $expired_count = Slide::where('expire_at','<',Carbon::today()->toDateString())->count();

        return view('admin.settings.slides.index',get_defined_vars());
    }


    public function create()
    {
        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        return view('admin.settings.slides.single', compact('countries'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'country_id' => 'required',
                'url' => 'required',
                'expire_at' => 'required|date',
                'image' => 'required|image',
                'image_mobile' => 'required|image',
                'status' => 'in:active,suspended'
            ],
            [
                'country_id.required' => 'country_required',
                'url.required' => 'url_required',
                'expire_at.required' => 'expire_at_required',
                'expire_at.date' => 'expire_at_date',
                'image.required' => 'image_required',
                'image.image' => 'image_invalid',
                'image_mobile.required' => 'image_required',
                'image_mobile.image' => 'image_invalid',
            ]
        );


        $status = $request->status ? 'active' : 'suspended';

        $info = unique_file_folder($request->image->getClientOriginalExtension());



        $request->image->move(public_path('/uploads/slides/'.$info['month']),$info['image']);

        $image = Image::make(public_path('uploads/slides/'.$info['name']));
        $image->resize(1920, 466);
        $image->save(public_path('uploads/slides/'.$info['name']));

        $info_1 = unique_file_folder($request->image_mobile->getClientOriginalExtension());
        $request->image_mobile->move(public_path('/uploads/slides/'.$info_1['month']),$info_1['image']);

        $image = Image::make(public_path('uploads/slides/'.$info_1['name']));

        $image->resize(1190, 375);

        $image->save(public_path('uploads/slides/'.$info_1['thumb_name']));
//        return $info_1 ;

        Slide::create
        (
            [
                'country_id' => $request->country_id,
                'status' => $status,
                'url' => $request->url,
                'expire_at' => $request->expire_at,
                'image' => $info['name'],
                'image_mobile' => $info_1['name']
            ]
        );

        return redirect('/admin/settings/slides/index')->with('success','created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:slides,id'
            ]
        );

        $countries = Country::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Slide::find($request->id);

        return view('admin.settings.slides.single', compact('edit','countries'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:slides,id',
                'country_id' => 'required',
                'url' => 'required',
                'expire_at' => 'required|date',
                'image' => 'sometimes|image',
                'image_mobile' => 'sometimes|image',
            ],
            [
                'country_id.required' => 'country_required',
                'url.required' => 'url_required',
                'expire_at.required' => 'expire_at_required',
                'expire_at.date' => 'expire_at_date',
                'image.image' => 'image_invalid',
                'image_mobile.image' => 'image_invalid',
            ]
        );

        $slider = Slide::find($request->id);
            $slider->country_id = $request->country_id;
            $slider->url = $request->url;
            $slider->expire_at = $request->expire_at;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/slides/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/slides/'.$info['name']));
                    $image->resize(1920, 466);
                $image->save(public_path('/uploads/slides/'.$info['name']));

                @unlink(public_path('/uploads/slides/'.$slider->getOriginal('image')));

                $slider->image = $info['name'];
            }
            if($request->image_mobile)
            {
                $info = unique_file_folder($request->image_mobile->getClientOriginalExtension());
                $request->image_mobile->move(public_path('/uploads/slides/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/slides/'.$info['name']));
                    $image->resize(1190, 375);
                $image->save(public_path('/uploads/slides/'.$info['name']));

                @unlink(public_path('/uploads/slides/'.$slider->getOriginal('image_mobile')));

                $slider->image_mobile = $info['name'];
            }
        $slider->save();

        return redirect('/admin/settings/slides/index')->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:slides,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        Slide::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:slides,id',
            ]
        );

        Slide::where('id',$request->id)->delete();

        return back()->with('success', 'deleted');
    }
}
