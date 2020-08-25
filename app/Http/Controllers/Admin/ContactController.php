<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $contacts = Contact::where(function($q) use($inputs)
        {
            if(isset($inputs['keyword'])) $q->where('email','like','%'.$inputs['keyword'].'%')->orWhere('phone','like','%'.$inputs['keyword'].'%');
            if(isset($inputs['type']) && $inputs['type'] != 'all') $q->where('type',$inputs['type']);
            if(isset($inputs['status'])) $q->where('closed',$inputs['status']);
        })->paginate();

        $active_count = Contact::where('closed',0)->count();
        $closed_count = Contact::where('closed',1)->count();

        return view('admin.contacts.index', compact('contacts','active_count','closed_count'));
    }


    public function show($id)
    {
        $contact = Contact::find($id);
        return view('admin.contacts.show', compact('contact'));
    }


    public function close($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:contacts,id,closed,0'
            ]
        );

        Contact::find($request->id)->update(['closed' => 1]);

        return response()->json(['status' => 'success']);
    }


    public function destroy($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:contacts,id'
            ]
        );

        Contact::find($request->id)->delete();

        return response()->json(['status' => 'success']);
    }
}
