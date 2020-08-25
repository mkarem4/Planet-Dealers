<?php

namespace App\Http\Controllers\Admin;

use App\Exports\MerchantExport;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Country;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
//use Maatwebsite\Excel\Excel;


class MerchantController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');
        $merchants = User::where(function($q) use($inputs)
        {
            if(isset($inputs['keyword']))
            {
                $q->where('first_name','like','%'.$inputs['keyword'].'%');
                $q->orWhere('last_name','like','%'.$inputs['keyword'].'%');
                $q->orwhere('email','like','%'.$inputs['keyword'].'%');
                $q->orwhere('phone','like','%'.$inputs['keyword'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all')
            {
                if($inputs['status'] == 'pending') $q->where('type','seller')->where('pack_id',NULL);
                else $q->where('status',$inputs['status']);
            }
            if(isset($inputs['featured']) && $inputs['featured'] != 'all') $q->where('featured',$inputs['featured']);
            if(isset($inputs['type']) && $inputs['type'] != 'all') $q->where('type',$inputs['type']);
        })->latest()->paginate(20)->appends($inputs);

        $pending_count = User::where('type','seller')->where('pack_id',NULL)->count();
        $active_count = User::where('status','active')->select('type')->get();
        $suspended_count = User::where('status','suspended')->select('type')->get();
        $deleted_count = User::withoutGlobalScopes()->where('deleted','1')->count();

        return view('admin.merchants.index', get_defined_vars());
    }


    public function create()
    {
        $countries = Country::where('type','main')->where('status','active')->select('id',lang().'_name as name')->get();
        return view('admin.merchants.single',get_defined_vars());
    }


    public function store(Request $request)
    {
        if($request->type == 'seller')
        {
            $rule_1 = 'required';
            $rule_2 = 'sometimes';
        }
        else
        {
            $rule_1 = 'sometimes';
            $rule_2 = 'required';
        }

        $this->validate($request,
            [
                'city_id' => 'required|exists:countries,id,deleted,0,type,sub',
                'type' => 'required|in:seller,buyer',
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => $rule_1.'|unique:users,company_name',
                'email' => 'required|email|unique:users',
                'phone' => 'required|unique:users',
                'password' => 'required|min:6|confirmed',
                'image' => 'required|image',
                'bank_info' => $rule_1,
                'commercial_record' => $rule_1.'|image',
                'address' => $rule_2,
                'featured' => 'sometimes',
                'featured_till' => 'sometimes|date',
                'status' => 'in:active,suspended'
            ],
            [
                'city_id.required' => 'city_required',
                'city_id.exists' => 'city_exists',
                'type.required' => 'type_required',
                'type.in' => 'type_invalid',
                'first_name.required' => 'first_name_required',
                'last_name.required' => 'last_name_required',
                'company_name.required' => 'field_required',
                'company_name.unique' => 'company_exists',
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
                'featured_till.date' => 'field_invalid',
                'bank_info.required' => 'field_required',
                'commercial_record.required' => 'field_required',
                'address.required' => 'field_required',
                'status.in' => 'status_invalid'
            ]
        );

        $city = Country::where('id',$request->city_id)->select('parent_id')->first();
        $status = $request->status ? 'active' : 'suspended';
        $featured = $request->featured ? 1 : 0;

        $bank_info = $request->bank_info  ? $request->bank_info : '';

        if($request->type == 'seller')
        {
            $commercial_info = unique_file_folder($request->commercial_record->getClientOriginalExtension());
            $request->commercial_record->move(public_path('/uploads/users/commercial_records/'.$commercial_info['month']),$commercial_info['image']);
        }
        else $commercial_info['name'] = NULL;

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/users/'.$info['name']));
            $image->resize(200, 200);
        $image->save(public_path('/uploads/users/'.$info['name']));


        $user = User::create
        (
            [
                'jwt' => Str::random(300),
                'status' => $status,
                'type' => $request->type,
                'country_id' => $city->parent_id,
                'city_id' => $request->city_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'bank_info' => $bank_info,
                'commercial_record' => $commercial_info['name'],
                'image' => $info['name'],
                'lang' => 'en',
                'featured' => $featured,
                'featured_till' => $request->featured_at
            ]
        );

        if($request->type == 'buyer')
        {
            Address::create
            (
                [
                    'user_id' => $user->id,
                    'city_id' => $user->city_id,
                    'text' => $request->address
                ]
            );
        }

        return redirect('/admin/merchants/index')->with('success','created');
    }


    public function edit($id, Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'exists:users,id,deleted,0'
            ]
        );

        $countries = Country::where('type','main')->where('status','active')->select('id',lang().'_name as name')->get();
        $edit = User::find($id);

        return view('admin.merchants.single', get_defined_vars());
    }


    public function update(Request $request)
    {
        if($request->type == 'seller')
        {
            $rule_1 = 'required';
            $rule_2 = 'sometimes';
        }
        else
        {
            $rule_1 = 'sometimes';
            $rule_2 = 'required';
        }

        $this->validate($request,
            [
                'id' => 'required|exists:users,id,deleted,0',
                'city_id' => 'required|exists:countries,id,deleted,0,type,sub',
                'type' => 'required|in:seller,buyer',
                'first_name' => 'required',
                'last_name' => 'required',
                'expire_at' => 'nullable|date',
                'company_name' => $rule_1.'|unique:users,company_name,'.$request->id,
                'email' => 'required|email|unique:users,email,'.$request->id,
                'phone' => 'required|unique:users,phone,'.$request->id,
                'password' => 'nullable|min:6|confirmed',
                'bank_info' => $rule_1,
                'image' => 'sometimes|image',
            ],
            [
                'city_id.required' => 'city_required',
                'city_id.exists' => 'city_exists',
                'type.required' => 'type_required',
                'type.in' => 'type_invalid',
                'first_name.required' => 'first_name_required',
                'last_name.required' => 'last_name_required',
                'company_name.required' => 'field_required',
                'company_name.unique' => 'name_exists',
                'email.required' => 'email_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_exists',
                'phone.required' => 'phone_required',
                'phone.unique' => 'phone_exists',
                'password.min' => 'password_min_6',
                'password.confirmed' => 'password_confirmed',
                'image.image' => 'image_invalid',
                'bank_info.required' => 'field_required',
            ]
        );

        $city = Country::where('id',$request->city_id)->select('parent_id')->first();
        $featured = $request->featured ? 1 : 0;

        $admin = User::find($request->id);
            $admin->type = $request->type;
            $admin->country_id = $city->parent_id;
            $admin->city_id = $request->city_id;
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->company_name = $request->company_name;
            $admin->expire_at = $request->expire_at;
            $admin->email = $request->email;
            $admin->phone = $request->phone;
            if($request->bank_info) $admin->bank_info = $request->bank_info;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/users/'.$info['name']));
                $image->resize(200, 200);
                $image->save(public_path('/uploads/users/'.$info['name']));
            }
            $request->password ? $admin->password = Hash::make($request->password) : false;
            $admin->featured = $featured;
            $admin->featured_till = $request->featured_till;
        $admin->save();

        return redirect('/admin/merchants/index')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:users,id',
                'status' => 'required|in:active,suspended',
            ]
        );

        User::find($request->id)->update(['status' => $request->status]);
        Product::where('seller_id',$request->id)->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:users,id',
            ]
        );

        User::where('id',$request->id)->update(['deleted' => 1]);
        Product::where('seller_id',$request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }


    public function export()
    {
        return Excel::download(new MerchantExport(), 'merchants-'.Carbon::today()->toDateString().'.xlsx');
    }
}
