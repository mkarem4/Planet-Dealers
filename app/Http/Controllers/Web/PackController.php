<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\Banner;
use App\Models\Pack;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index()
    {
        $packs = Pack::where('status','active')->select('id','default',lang().'_name as name','month_count','price','image')->get();
        $banks = Bank::where('status','active')->where('country_id',country()->id)->select('id',lang().'_name as name',lang().'_desc as desc')->get();

        return view('web.packs',get_defined_vars());
    }


    public function subscribe(Request $request)
    {
        if(user()->type != 'seller') return back()->with('error','sellers_only');

        $this->validate($request,
            [
                'pack_id' => 'required|exists:packs,id,status,active,deleted,0',
                'bank_id' => 'required|exists:banks,id,status,active,deleted,0',
                'user_name' => 'required',
                'account_no' => 'required',
                'user_notes' => 'sometimes',
                'image' => 'required|image',
            ],
            [
                'pack_id.required' => 'pack_required',
                'pack_id.exists' => 'pack_required',
                'bank_id.required' => 'field_required',
                'bank_id.exists' => 'field_invalid',
                'account_no.required' => 'field_required',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
                'image.size' => 'image_large',
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/bank_transfers/' . $info['month']), $info['image']);

        BankTransfer::create
        (
            [
                'country_id' => user()->country_id,
                'user_id' => user()->id,
                'pack_id' => $request->pack_id,
                'bank_id' => $request->bank_id,
                'user_name' => user()->name,
                'account_no' => $request->account_no,
                'user_notes' => $request->user_notes,
                'image' => $info['name'],
            ]
        );

        return redirect('/')->with('success','sent_we_review');
    }
}
