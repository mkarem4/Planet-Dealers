<?php

namespace App\Http\Controllers\Admin;

use App\Models\Social;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class SocialController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $socials = Social::where(function($q) use($inputs)
        {
            if(isset($inputs['url'])) $q->where('url','like','%'.$inputs['url'].'%');
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->latest()->paginate(20)->appends($inputs);

        $active_count = Social::where('status','active')->count();
        $suspended_count = Social::where('status','suspended')->count();

        return view('admin.settings.socials.index', get_defined_vars());
    }


    public function create()
    {
        return view('admin.settings.socials.single');
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'status' => 'in:active,suspended',
                'url' => 'required',
                'image' => 'required|image',
            ],
            [
                'url.required' => 'field_required',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/socials/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/socials/'.$info['name']));
        $image->resize(40, 40);
        $image->save(public_path('/uploads/socials/'.$info['name']));

        $status = $request->status ? 'active' : 'suspended';

        Social::create
        (
            [
                'status' => $status,
                'url' => $request->url,
                'image' => $info['name']
            ]
        );

        return redirect('/admin/settings/socials/index')->with('success', 'created');
    }


    public function edit($id)
    {
        $edit = Social::find($id);
        return view('admin.settings.socials.single', compact('edit'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:socials,id',
                'url' => 'required',
                'image' => 'sometimes|image',
            ],
            [
                'url.required' => 'field_required',
                'image.image' => 'image_image'
            ]
        );


        $edit = Social::find($request->id);
            $edit->url = $request->url;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/socials/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/socials/'.$info['name']));
                    $image->resize(40, 40);
                $image->save(public_path('/uploads/socials/'.$info['name']));

                @unlink(public_path('/uploads/socials/'.$edit->getOriginal('image')));

                $edit->image = $info['name'];
            }
        $edit->save();

        return redirect('/admin/settings/socials/index')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:socials,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        Social::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:socials,id',
            ]
        );

        Social::find($request->id)->delete();

        return redirect('/admin/settings/socials/index')->with('success', 'deleted');
    }
}
