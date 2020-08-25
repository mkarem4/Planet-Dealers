<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Code;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Newsletter;
use App\Models\Notification;
use App\Models\Product;
use App\Models\SearchRequest;
use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    public function update_token(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'token' => 'required',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        Token::firstOrcreate
        (
            [
                'token' => $request->token
            ]
        );

        return r_json([],204);
    }


    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'token' => 'required',
                'email' => 'required',
                'password' => 'required'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('email',$request->email)->first();
        if($user)
        {
            if($user->status == 'suspended') return r_json(['status' => 'failed','msg' => 'suspended user']);

            if(Hash::check($request->password,$user->password))
            {
//                $user->update(['jwt' => Str::random(200),]);

                Token::updateOrcreate
                (
                    [
                        'token' => $request->token
                    ],
                    [
                        'user_id' => $user->id
                    ]
                );

                $user = $user->setAppends(['addresses']);
                $user['phone'] = 'm_p'.$user->phone;

                return r_json(['user' => $user]);
            }
            else
            {
                return r_json(['msg' => word('invalid_password')],401);
            }
        }
        else
        {
            return r_json(['msg' => word('invalid_email')],401);
        }
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'type' => 'required|in:seller,buyer',
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => 'sometimes',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'password' => 'required|min:6',
                'token' => 'required',
                'country_id' => 'required|exists:countries,id,type,main',
                'city_id' => 'required|exists:countries,id,type,sub',
                'address' => 'sometimes',
                'bank_info' => 'sometimes'
            ]
        );

        if ($validator->fails())
        {
            return r_json(['msg' => 'validation_error', 'bag' => $validator->getMessageBag()], 400);
        }

        change_lang(request()->header('Accept-Language'));

        if($request->type == 'seller')
        {
            $company_check =User::where('company_name', $request->company_name)->exists();
            if ($company_check) return r_json(['msg' => word('company_name_exists')], 401);
        }

        $email_check = User::where('email', $request->email)->exists();
        if ($email_check) return r_json(['msg' => word('email_exists')], 401);

        $phone_check = User::where('phone', $request->phone)->exists();
        if ($phone_check) return r_json(['msg' => word('phone_exists')], 401);

        $user = new User();
            $user->jwt = Str::random(200);
            $user->type = $request->type;
            $user->country_id = $request->country_id;
            $user->city_id = $request->city_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            if($request->company_name) $user->company_name = $request->company_name;
            if($request->bank_info) $user->bank_info = $request->bank_info;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
        $user->save();

        if ($request->address)
        {
            Address::create
            (
                [
                    'user_id' => $user->id,
                    'city_id' => $request->city_id,
                    'type' => 'main',
                    'text' => $request->address,
                ]
            );
        }


        Token::updateOrcreate
        (
            [
                'token' => $request->token
            ],
            [
                'user_id' => $user->id
            ]
        );

        Newsletter::firstOrCreate(['email' => $request->email]);

        $user['phone'] = 'm_p'.$user->phone;
        $user->setAppends(['addresses']);

        return r_json(['user' => $user]);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'first_name' => 'required',
                'last_name' => 'required',
                'company_name' => 'sometimes',
                'bank_info' => 'sometimes',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'image' => 'sometimes|image',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));


        $email_check = User::where('email', $request->email)->where('id','!=',$request->user_id)->exists();
        if($email_check) return r_json(['msg' => word('email_exists')],401);

        $phone_check = User::where('phone', $request->phone)->where('id','!=',$request->user_id)->exists();
        if($phone_check) return r_json(['msg' => word('phone_exists')],401);

        $user = User::find($request->user_id);
            if($user->type == 'seller')
            {
                $company_check =User::where('company_name', $request->company_name)->where('id','!=',$request->user_id)->exists();
                if ($company_check) return r_json(['msg' => word('company_name_exists')], 401);
            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            if($request->company_name) $user->company_name = $request->company_name;
            if($request->bank_info) $user->bank_info = $request->bank_info;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/users/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/users/'.$info['name']));
                    $image->resize(200, 200);
                $image->save(public_path('/uploads/users/'.$info['name']));

                if($user->getOriginal('image') != 'default-user.png') @unlink(public_path('/uploads/users/'.$user->getOriginal('image')));

                $user->image = $info['name'];
            }
        $user->save();

        $user['phone'] = 'm_p'.$user->phone;
        $user = $user->setAppends(['addresses']);

        Newsletter::firstOrCreate(['email' => $request->email]);

        return r_json(['user' => $user]);
    }


    public function update_city(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'city_id' => 'required|exists:countries,id,deleted,0,status,active,type,sub',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        User::find($request->user_id)->update(['city_id' => $request->city_id]);

        return r_json([],204);
    }


    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'old_password' => 'required',
                'new_password' => 'required|min:6',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }


        $user = User::where('id',$request->user_id)->select('id','password','lang','updated_at')->first();
        change_lang($user->lang);

        $check = Hash::check($request->old_password,$user->password);

        if(! $check) return r_json(['msg' => word('old_password_incorrect')],401);
        else
        {
            $user->update(['password' => Hash::make($request->new_password)]);
            return r_json([],204);
        }
    }


    public function send_reset(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('email',$request->email)->select('lang')->first();

        if(! $user) r_json(['msg' => word('invalid_email')],401);

        $code = Code::updateOrcreate
        (
            [
                'email' => $request->email
            ],
            [
                'code' => rand(100000,999999),
                'expire_at' => Carbon::now()->addHour()
            ]
        );

        $code['expire_at'] = strtotime($code->expire_at);

        if($user->lang == 'ar')
        {
            $subject = 'كود التحقق لتعيين كلمة المرور في تجار الكوكب';
            $title = 'التحقق';
            $name = 'تجار الكوكب';
        }
        else
        {
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

        Mail::send('emails.template',$data, function ($message) use ($data)
        {
            $message->from('reset@planet-dealers.com','Verification@PlanetDealers')
                ->to($data['email'])
                ->subject($data['title'].' | '.$data['name']);
        });

        return r_json([],204);
    }


    public function check_reset(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'code' => 'required'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $check = Code::where('code',$request->code)->where('expire_at','>',Carbon::now()->toDateTimeString())->first();
        if(! $check) r_json(['msg' => word('invalid_code')],401);
        else
        {
            $user = User::where('email',$check->email)->select('id','jwt')->first();
            return r_json(['user' => $user]);
        }
    }


    public function password_reset(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'password' => 'required|min:6',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        User::find($request->user_id)->update(['password' => $request->password]);

        return r_json([],204);
    }


    public function get_addresses(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $addresses = Address::where('user_id',$request->user_id)->select('id','city_id','text','number','close_to','notes')->get();
        foreach($addresses as $address)
        {
            $address['text'] = 'm_p'.$address->text;
            $address['number'] = 'm_p'.$address->number;
            $address['close_to'] = 'm_p'.$address->close_to;
            $address['notes'] = 'm_p'.$address->notes;
            $address['city'] = Country::where('id',$address->city_id)->select(lang().'_name as name')->first()->name;
        }

        return r_json($addresses);
    }


    public function store_address(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'city_id' => 'required|exists:countries,id,deleted,0,status,active,type,sub',
                'text' => 'required',
                'number' => 'required',
                'close_to' => 'required',
                'notes' => 'required',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $address = Address::create
        (
            [
                'user_id' => $request->user_id,
                'city_id' => $request->city_id,
                'text' => $request->text,
                'number' => $request->number,
                'close_to' => $request->close_to,
                'notes' => $request->notes,
            ]
        );

        return r_json(['id' => $address->id],200);
    }


    public function update_address(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'address_id' => 'required|exists:addresses,id,deleted,0,user_id,'.$request->user_id,
                'city_id' => 'required|exists:countries,id,deleted,0,status,active,type,sub',
                'text' => 'required',
                'number' => 'required',
                'close_to' => 'required',
                'notes' => 'required',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        Address::find($request->address_id)->update
        (
            [
                'city_id' => $request->city_id,
                'text' => $request->text,
                'number' => $request->number,
                'close_to' => $request->close_to,
                'notes' => $request->notes,
            ]
        );

        $addresses = Address::where('user_id',$this->id)->select('id','city_id','text','number','close_to','notes')->get();
        foreach($addresses as $address) $address['city'] = Country::where('id',$address->city_id)->select(lang().'_name as name')->first()->name;

        return r_json($addresses);
    }

    public function destroy_address(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'address_id' => 'required|exists:addresses,id,deleted,0,user_id,'.$request->user_id,
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        Address::find($request->address_id)->update(['deleted' => 1]);

        return r_json([],204);
    }


    public function contact_us(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'text' => 'required',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        Contact::create
        (
            [
                'name' => $request->name,
                'email' => $request->name,
                'phone' => $request->name,
                'text' => $request->name,
            ]
        );

        return r_json([],204);
    }


    public function search_request(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'type' => 'required|in:product,seller',
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'text' => 'required',
                'address' => 'required',
                'attachments' => 'sometimes|array',
                'attachments.*' => 'file',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $search = new SearchRequest();
            $search->type = $request->type;
            $search->name = $request->name;
            $search->email = $request->email;
            $search->phone = $request->phone;
            $search->text = $request->text;
            $search->address = $request->address;
            if($request->attachments)
            {
                $arr = [];
                foreach($request->attachments as $attachment)
                {
                    $info = unique_file_folder($attachment->getClientOriginalExtension());
                    $attachment->move(public_path('/uploads/search_requests/'.$info['month']),$info['image']);

                    $arr[] = $info['name'];
                }

                $search->attachments = implode(',',$arr);
            }
        $search->save();

        return r_json([],204);
    }


    public function notifications(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $notifications = Notification::where('user_id',$request->user_id)->select('id','type','action_id',lang().'_text as text','created_at as timestamp')->paginate(20);

        return r_json($notifications);
    }


    public function destroy_notification(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'notification_id' => 'required|exists:notifications,id,user_id,'.$request->user_id,
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }


        Notification::find($request->notification_id)->delete();

        return r_json([],204);
    }


    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'seller_id' => 'required|exists:users,id,deleted,0,status,active'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));

        $user = User::where('id',$request->seller_id)->select('id','first_name','last_name','image','country_id as country','city_id as city')->first();
        $user['country'] = Country::where('id',$user->country)->select(lang().'_name as name')->first()->name;
        $user['city'] = Country::where('id',$user->city)->select(lang().'_name as name')->first()->name;

        $user['products'] = Product::where('seller_id',$request->seller_id)->select('id',lang().'_name as name','thumb_image as image','price_meta')->paginate(20);

        foreach($user->products as $product)
        {
            $product['is_favorite'] = Favorite::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
            $product['is_cart'] = Cart::where('user_id',$request->user_id)->where('product_id',$product->id)->exists();
        }

        return r_json($user,200,'object');
    }
}
