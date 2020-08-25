<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Country;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = Address::where('user_id', user()->id)->paginate();
        $cities = Country::where('parent_id',user()->country_id)->where('status','active')->where('type','sub')->select('id',lang().'_name as name')->get();

        $count = Address::where('user_id', user()->id)->count();

        return view('web.addresses', compact('addresses','cities','count'));
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'city_id' => 'required|exists:countries,id,type,sub,status,active,deleted,0,parent_id,'.user()->country_id,
                'text' => 'required',
                'number' => 'required',
                'close_to' => 'required',
                'notes' => 'sometimes',
            ],
            [
                'city_id.required' => 'field_required',
                'city_id.exists' => 'field_invalid',
                'text.required' => 'field_required',
                'number.required' => 'field_required',
                'close_to.required' => 'field_required',
            ]
        );

        $address = new Address();
            $address->user_id = user()->id;
            $address->city_id = $request->city_id;
            $address->text = $request->text;
            $address->number = $request->number;
            $address->close_to = $request->close_to;
            $address->notes = $request->notes ? $request->notes : '';
        $address->save();

        return back()->with('success','created');
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'edit_id' => 'required|exists:addresses,id,deleted,0,user_id,'.user()->id,
                'edit_city_id' => 'required|exists:countries,id,type,sub,status,active,deleted,0,parent_id,'.user()->country_id,
                'edit_text' => 'required',
                'edit_number' => 'required',
                'edit_close_to' => 'required',
                'edit_notes' => 'sometimes',
            ],
            [
                'edit_city_id.required' => 'field_required',
                'edit_city_id.exists' => 'field_invalid',
                'edit_text.required' => 'field_required',
                'edit_number.required' => 'field_required',
                'edit_close_to.required' => 'field_required',
            ]
        );

        $address = Address::find($request->edit_id);
            $address->city_id = $request->edit_city_id;
            $address->text = $request->edit_text;
            $address->number = $request->edit_number;
            $address->close_to = $request->edit_close_to;
            $address->notes = $request->edit_notes ? $request->edit_notes : '';
        $address->save();

        return back()->with('success','updated');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:addresses,id,deleted,0,user_id,'.user()->id,
            ]
        );

        if(Address::where('user_id',user()->id)->count() != 1) Address::find($request->id)->update(['deleted' => 1]);
        else return back()->with('error','1_address_required');

        return back()->with('success','deleted');
    }
}
