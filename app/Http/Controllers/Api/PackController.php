<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'user_id' => 'required|exists:users,id,status,active,deleted,0,jwt,'.jwt(),
                'country_id' => 'required|exists:countries,id,status,active,deleted,0,type,main'
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        change_lang(request()->header('Accept-Language'));


        $user = User::where('id',$request->user_id)->select('pack_id','expire_at')->first();
        if($user->pack_id) $user['pack'] = Pack::where('id',$user->pack_id)->select(lang().'_name as name','price','image')->first();
        else $user['pack'] = new \stdClass();


        $packs = Pack::where('status','active')->select('id',lang().'_name as name','price','image')->get();
        $banks = Bank::where('status','active')->where('country_id',$request->country_id)->select('id',lang().'_name as name',lang().'_desc as desc')->get();

        return r_json(['user' => $user,'packs' => $packs,'banks' => $banks]);
    }


    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'country_id' => 'required|exists:countries,id,deleted,0,status,active,type,main',
                'bank_id' => 'required|exists:banks,id,deleted,0,status,active,country_id,'.$request->country_id,
                'pack_id' => 'required|exists:packs,id,deleted,0,status,active',
                'user_id' => 'required|exists:users,id,deleted,0,status,active,jwt,'.jwt(),
                'user_name' => 'required',
                'account_no' => 'required',
                'user_notes' => 'required',
                'image' => 'required|image',
            ]
        );

        if($validator->fails())
        {
            return r_json(['msg' => 'validation_error','bag' => $validator->getMessageBag()],400);
        }

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/transfers/'.$info['month']),$info['image']);

        BankTransfer::create
        (
            [
                'country_id' => $request->country_id,
                'bank_id' => $request->bank_id,
                'pack_id' => $request->pack_id,
                'user_id' => $request->user_id,
                'user_name' => $request->user_id,
                'account_no' => $request->user_id,
                'user_notes' => $request->user_id,
                'image' => $info['name'],
            ]
        );

        return r_json([],204);
    }
}
