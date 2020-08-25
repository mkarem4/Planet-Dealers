<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransfer;
use App\Models\Pack;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $transfers = BankTransfer::latest()->where(function($q) use($inputs)
        {
            if(isset($inputs['bank']) && $inputs['bank'] != 'all') $q->where('bank_id',$inputs['bank']);
            if(isset($inputs['name'])) $q->where('user_name','like','%'.$inputs['name'].'%');
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->paginate(20)->appends($inputs);

        $banks = Bank::where('country_id',1)->where('status','active')->select('id',lang().'_name as name')->get();

        $pending_count = BankTransfer::where('status','pending')->count();
        $confirmed_count = BankTransfer::where('status','confirmed')->count();
        $declined_count = BankTransfer::where('status','declined')->count();

        return view('admin.bank_transfers.index', get_defined_vars());
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:bank_transfers',
                'status' => 'required|in:confirmed,declined',
                'admin_notes' => 'sometimes'
            ]
        );

        $transfer = BankTransfer::find($request->id);
        $transfer->admin_id = admin()->id;
        $transfer->admin_notes = $request->admin_notes;
        $transfer->status = $request->status;
        $transfer->save();

        $pack = Pack::where('id',$transfer->pack_id)->first();

        if($request->status == 'confirmed')
        {
            $user = User::find($transfer->user_id);
                $user->pack_id = $transfer->pack_id;
                $user->expire_at = Carbon::parse($user->getOriginal('expire_at') ? $user->expire_at : Carbon::now()->toDateString())->addMonths($pack->month_count);
            $user->save();
        }

        return back()->with('success', 'status_changed');
    }
}
