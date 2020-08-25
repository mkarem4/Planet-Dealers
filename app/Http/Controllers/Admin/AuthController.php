<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    public function view()
    {
        change_lang(admin_locale());

        if(admin()) return redirect('/admin/dashboard');
        return view('admin.auth.login');
    }


    public function login(Request $request)
    {
        $this->validate($request,
            [
                'login' => 'required',
                'password' => 'required'
            ],
            [
                'login.required' => 'login_required',
                'password.required' => 'password_required',
            ]
        );

        $field = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        $request->merge([$field => $request->input('login')]);

        $admin = Admin::where($field,$request->$field)->first();

        if($admin && $admin->status == 'suspended') return back()->with('error','blocked');

        if(Auth::guard('admin')->attempt([$field => $request->login,'password' => $request->password]))
        {
            Session::put('admin_locale',admin()->lang);
            return redirect('/admin/dashboard');
        }
        else
        {
            return back()->with('error', 'invalid_data');
        }
    }


    public function show()
    {
        return view('admin.auth.profile');
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|email|unique:admins,email,'.admin()->id,
                'phone' => 'required|numeric|unique:admins,phone,'.admin()->id,
                'image' => 'sometimes|image',
                'password' => 'nullable|confirmed|min:6'
            ],
            [
                'name.required' => 'name_required',
                'email.required' => 'email_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_unique',
                'phone.required' => 'phone_required',
                'phone.numeric' => 'phone_numeric',
                'phone.unique' => 'phone_required',
                'image.image' => 'image_image',
                'password.min' => 'password_min',
                'password.confirmed' => 'password_confirmed',
            ]
        );

        $admin = Admin::find(admin()->id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            if($request->password) $admin->password = Hash::make($request->password);
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/users/'.$info['name']));
                $image->resize(200, 200);
                $image->save(public_path('/uploads/users/'.$info['name']));

                if($admin->image != 'default.png')
                {
                    @unlink(public_path('/uploads/users/'.$admin->getOriginal('image')));
                }
                $admin->image = $info['name'];
            }
        $admin->save();

        return back()->with('success', 'updated');
    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }
}
