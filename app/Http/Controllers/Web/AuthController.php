<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Code;
use App\Models\Country;
use App\Models\Newsletter;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    public function login_view()
    {
        return view('web/auth/login');
    }


    public function login(Request $request)
    {
        $this->validate($request,
            [
                'email' => 'required',
                'password' => 'required'
            ],
            [
                'email.required' => 'email_required',
                'password.required' => 'password_required',
            ]
        );

        $user = User::where('email', $request->email)->select('status')->first();
        if ($user) {
            if ($user->status == 'suspended') return back()->with('error', 'blocked');
            else {
                if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
                    Session::put('user_locale', user()->lang);
                    return redirect('/');
                } else {
                    return back()->with('error', 'invalid_data');
                }
            }
        } else return back()->with('error', 'invalid_email');
    }


    public function register_view()
    {
        return view('web/auth/register');
    }


    public function register(Request $request)
    {
        if ($request->type == 'seller') {
            $rule_1 = 'required';
            $rule_2 = 'sometimes';
        } else {
            $rule_1 = 'sometimes';
            $rule_2 = 'required';
        }

        $this->validate($request,
            [
                'city_id' => 'required|exists:countries,id,type,sub',
                'type' => 'required|in:buyer,seller',
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => $rule_1 . '|nullable|unique:users,company_name',
                'email' => 'required|unique:users,email|email',
                'phone' => 'required|unique:users,phone',
                'password' => 'required|min:6|confirmed',
                'bank_info' => $rule_1,
                'commercial_record' => $rule_1 . '|image',
                'address' => $rule_2,
                'terms' => 'required',
            ],
            [
                'city_id.required' => 'field_required',
                'city_id.exists' => 'field_invalid',
                'type.required' => 'field_required',
                'type.in' => 'field_invalid',
                'first_name.required' => 'field_required',
                'last_name.required' => 'field_required',
                'company_name.required' => 'field_required',
                'company_name.unique' => 'company_exists',
                'email.required' => 'email_required',
                'email.unique' => 'email_exists',
                'email.email' => 'email_email',
                'phone.required' => 'phone_required',
                'phone.unique' => 'phone_exists',
                'password.required' => 'field_required',
                'password.confirmed' => 'password_confirmed',
                'password.min' => 'password_min_6',
                'bank_info.required' => 'field_required',
                'commercial_record.required' => 'field_required',
                'address.required' => 'field_required',
                'terms.required' => 'please_agree_terms',
            ]
        );

        $bank_info = $request->bank_info ? $request->bank_info : '';

        if ($request->type == 'seller') {
            $info = unique_file_folder($request->commercial_record->getClientOriginalExtension());
            $request->commercial_record->move(public_path('/uploads/users/commercial_records/' . $info['month']), $info['image']);
        } else $info['name'] = NULL;

        $country = Country::where('id', $request->city_id)->select('parent_id')->first()->parent;

        $user = User::create
        (
            [
                'jwt' => Str::random(200),
                'type' => $request->type,
                'country_id' => $country->id,
                'city_id' => $request->city_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $country->code . $request->phone,
                'whatsapp' => $request->whatsapp ? $country->code . $request->whatsapp : null,
                'password' => Hash::make($request->password),
                'bank_info' => $bank_info,
                'commercial_record' => $info['name']
            ]
        );

        if ($request->type == 'buyer') {
            Address::create
            (
                [
                    'user_id' => $user->id,
                    'city_id' => $user->city_id,
                    'text' => $request->address
                ]
            );
        }


        Newsletter::firstOrCreate(['email' => $request->email]);

        Auth::loginUsingId($user->id);

        Session::put('user_locale', user()->lang);

        return redirect('/');
    }


    public function reset_view()

    {
        return view('web.auth.reset');
    }


    public function send_reset(Request $request)
    {
        $this->validate($request,
            [
                'email' => 'required|exists:users,email'
            ],
            [
                'email.required' => 'email_required'
            ]
        );

        $code = Code::updateOrcreate
        (
            [
                'email' => $request->email
            ],
            [
                'code' => rand(100000, 999999),
                'expire_at' => Carbon::now()->addHour()
            ]
        );

        $user = User::where('email', $request->email)->select('lang')->first();

        if ($user->lang == 'ar') {
            $subject = 'كود التحقق لتعيين كلمة المرور في تجار الكوكب';
            $title = 'التحقق';
            $name = 'تجار الكوكب';
        } else {
            $subject = 'Verification code for Planet Dealers password reset';
            $title = 'Verification';
            $name = 'Planet Dealers';
        }

        $data =
            [
                'email' => $request->email,
                'subject' => $subject,
                'content' => $code->code,
                'title' => $title,
                'name' => $name
            ];

        Mail::send('emails.template', $data, function ($message) use ($data) {
            $message->from('reset@planet-dealers.com', 'Verification@PlanetDealers')
                ->to($data['email'])
                ->subject($data['title'] . ' | ' . $data['name']);
        });


        return redirect('/password/reset/view')->with('success', 'code_sent');
    }


    public function password_reset(Request $request)
    {
        $this->validate($request,
            [
                'code' => 'required',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'code.required' => 'field_required',
                'password.required' => 'field_required',
                'password.confirmed' => 'password_confirmed',
                'password.min' => 'password_min_6',
            ]
        );

        $check = Code::where('code', $request->code)->where('expire_at', '>', Carbon::now()->toDateTimeString())->first();
        if (!$check) return back()->with('error', 'invalid_code');
        else {
            $user = User::where('email', $check->email)->select('id', 'password')->first();
            if ($user) {
                $user->update(['password' => Hash::make($request->password)]);

                Auth::loginUsingId($user->id);

                return redirect('/');
            } else return back()->with('error', 'invalid_code');
        }
    }


    public function profile()
    {
        return view('web/profile/profile');
    }


    public function orders()
    {
        return view('web/profile/orders');
    }


    public function update(Request $request)
    {
        $rule = boolval(user()->type == 'seller') ? 'required' : 'sometimes';
        $this->validate($request,
            [
                'city_id' => 'required|exists:countries,id,deleted,0,type,sub,parent_id,' . user()->country_id,
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => 'required|unique:users,company_name,' . user()->id,
                'email' => 'required|email|unique:users,email,' . user()->id,
                'phone' => 'required|unique:users,phone,' . user()->id,
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'bank_info' => $rule
            ],
            [
                'city_id.required' => 'city_required',
                'city_id.exists' => 'city_exists',

                'first_name.required' => 'field_required',
                'last_name.required' => 'field_required',
                'company_name.required' => 'field_required',
                'company_name.unique' => 'field_exists',
                'email.required' => 'field_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_exists',
                'phone.required' => 'field_required',
                'phone.unique' => 'field_required',
                'image.required' => 'field_required',
                'image.image' => 'image_image',
                'bank_info.required' => 'field_required',
            ]
        );

        $user = user();
        $user->city_id = $request->city_id;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->company_name = $request->company_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->whatsapp = $request->whatsapp ? $request->whatsapp : null;
        if ($request->image) {
            $info = unique_file_folder($request->image->getClientOriginalExtension());
            $request->image->move(public_path('/uploads/users/' . $info['month']), $info['image']);

            $image = Image::make(public_path('/uploads/users/' . $info['name']));
            $image->resize(200, 200);
            $image->save(public_path('/uploads/users/' . $info['name']));

            @unlink(public_path('/uploads/users/' . $user->getOriginal('image')));
            $user->image = $info['name'];
        }
        if (user()->type == 'seller') $user->bank_info = $request->bank_info;
        $user->save();

        Newsletter::firstOrCreate(['email' => $request->email]);

        return back()->with('success', 'updated');
    }


    public function update_password(Request $request)
    {
        $this->validate($request,
            [
                'old_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ],
            [
                'old_password.required' => 'field_required',
                'password.required' => 'field_required',
                'password.min' => 'password_min_6',
                'password.confirmed' => 'password_confirmed',
            ]
        );

        if (Hash::check($request->old_password, user()->password)) {
            user()->password = Hash::make($request->password);
            user()->save();

            return back()->with('success', 'updated');
        } else {
            return back()->with('error', 'password_invalid');
        }
    }


    public function notifications()
    {
        $nots = Notification::where('user_id', user()->id)->select('id', 'type', 'action_id', lang() . '_text as text', 'created_at')->paginate();
        return view('web.auth.notifications', compact('nots'));
    }


    public function logout()
    {
        Auth::guard('user')->logout();
        return redirect('/');
    }
}
