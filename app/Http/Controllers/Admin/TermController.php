<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function edit()
    {
        $data = Term::first();
        return view('admin.settings.terms.single', compact('data'));
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'ar_text' => 'required',
                'en_text' => 'required',
            ],
            [
                'ar_text.required' => 'field_required',
                'en_text.required' => 'field_required',
            ]
        );

        Term::first()->update
        (
            [
                'ar_text' => $request->ar_text,
                'en_text' => $request->en_text,
            ]
        );

        return back()->with('success','updated');
    }
}
