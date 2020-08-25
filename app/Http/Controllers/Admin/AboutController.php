<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function edit()
    {
        $data = About::first();
        return view('admin.settings.abouts.single', compact('data'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'ar_text' => 'required',
                'en_text' => 'required',
                'android_link' => 'required',
                'ios_link' => 'required'
            ],
            [
                'ar_text.required' => 'field_required',
                'en_text.required' => 'field_required',
                'android_link.required' => 'field_required',
                'ios_link.required' => 'field_required',
            ]
        );

        About::first()->update
        (
            [
                'ar_text' => $request->ar_text,
                'en_text' => $request->en_text,
                'android_link' => $request->android_link,
                'ios_link' => $request->ios_link,
            ]
        );

        return back()->with('success','updated');
    }
}
