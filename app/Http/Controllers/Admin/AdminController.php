<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $admins = Admin::whereNotIn('id',[1,admin()->id])->where(function($q) use($inputs)
        {
            if(isset($inputs['keyword']))
            {
                $q->where('name','like','%'.$inputs['keyword'].'%');
                $q->orWhere('email',$inputs['keyword']);
                $q->orWhere('phone',$inputs['keyword']);
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->latest()->paginate(20)->appends($inputs);

        $active_count = Admin::where('status','active')->count();
        $suspended_count = Admin::where('status','suspended')->count();

        return view('admin.admins.index', get_defined_vars());
    }


    public function create()
    {
        return view('admin.admins.single');
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|email|unique:admins',
                'phone' => 'required|unique:admins',
                'password' => 'required|min:6|confirmed',
                'image' => 'required|image',
                'status' => 'in:active,suspended',
//                'permissions' => 'required|array|min:1',
            ],
            [
                'name.required' => 'name_required',
                'email.required' => 'email_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_exists',
                'phone.required' => 'phone_required',
                'phone.unique' => 'phone_exists',
                'password.required' => 'password_required',
                'password.min' => 'password_min_6',
                'password.confirmed' => 'password_confirmed',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
                'status.in' => 'status_invalid',
//                'permissions.required' => 'permissions_required',
//                'permissions.array' => 'permissions_invalid',
//                'permissions.min' => 'permissions_required',
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/users/'.$info['name']));
            $image->resize(200, 200);
        $image->save(public_path('/uploads/users/'.$info['name']));

        $status = $request->status ? 'active' : 'suspended';

        Admin::create
        (
            [
                'status' => $status,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'image' => $info['name']
            ]
        );

        return redirect('/admin/admins/index')->with('success','created');
    }


    public function edit($id, Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'exists:admins,id|not_in:1'
            ]
        );

        $edit = Admin::find($id);

        return view('admin.admins.single', get_defined_vars());
    }


    public function update(Request $request)
    {

        $this->validate($request,
            [
                'id' => 'required|exists:admins,id',
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,' . $request->id,
                'phone' => 'required|unique:admins,phone,' . $request->id,
                'password' => 'nullable|min:6|confirmed',
                'image' => 'sometimes|image',
            ],
            [
                'name.required' => 'name_required',
                'email.required' => 'email_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_exists',
                'phone.required' => 'phone_required',
                'phone.unique' => 'phone_exists',
                'password.min' => 'password_min_6',
                'password.confirmed' => 'password_confirmed',
                'image.image' => 'image_image',
            ]
        );

        $admin = Admin::find($request->id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            $request->password ? $admin->password = Hash::make($request->password) : false;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/users/'.$info['name']));
                    $image->resize(200, 200);
                $image->save(public_path('/uploads/users/'.$info['name']));

                @unlink(public_path('/uploads/users/'.$admin->getOriginal('image')));

                $admin->image = $info['name'];
            }
        $admin->save();

        return redirect('/admin/admins/index')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:admins,id|not_in:1',
                'status' => 'required|in:active,suspended',
            ]
        );

        Admin::find($request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:admins,id|not_in:1',
            ]
        );

        Admin::where('id',$request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
