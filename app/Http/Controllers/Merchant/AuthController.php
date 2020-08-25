<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Merchant;
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
        if(merchant()) return redirect('/merchant/dashboard');
        return view('merchant.auth.login');
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

        $admin = Merchant::where($field,$request->$field)->first();

        if($admin && $admin->status == 'suspended') return back()->with('error','blocked');

        if(Auth::guard('merchant')->attempt([$field => $request->login,'password' => $request->password]))
        {
            Session::put('merchant_locale',merchant()->lang);
            return redirect('/merchant/dashboard');
        }
        else
        {
            return back()->with('error', 'invalid_data');
        }
    }


    public function show()
    {
        return view('merchant.auth.profile');
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'name' => 'required',
                'email' => 'required|email|unique:merchants,email,'.merchant()->id,
                'phone' => 'required|numeric|unique:merchants,phone,'.merchant()->id,
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

        $admin = Merchant::find(merchant()->id);
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
        Auth::guard('merchant')->logout();
        return redirect('/merchant/login');
    }
}
