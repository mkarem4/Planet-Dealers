<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchRequest;
use Illuminate\Http\Request;

class SearchRequestController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $searches = SearchRequest::where(function($q) use($inputs)
        {
            if(isset($inputs['keyword'])) $q->where('email','like','%'.$inputs['keyword'].'%')->orWhere('phone','like','%'.$inputs['keyword'].'%');
            if(isset($inputs['type']) && $inputs['type'] != 'all') $q->where('type',$inputs['type']);
            if(isset($inputs['status'])) $q->where('closed',$inputs['status']);
        })->paginate();

        $active_count = SearchRequest::where('closed',0)->count();
        $closed_count = SearchRequest::where('closed',1)->count();

        return view('admin.search_requests.index', compact('searches','active_count','closed_count'));
    }


    public function show($id)
    {
        $search = SearchRequest::find($id);
        return view('admin.search_requests.show', compact('search'));
    }


    public function close($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:search_requests,id,closed,0'
            ]
        );

        SearchRequest::find($request->id)->update(['closed' => 1]);

        return response()->json(['status' => 'success']);
    }


    public function destroy($id,Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'required|exists:search_requests,id'
            ]
        );

        SearchRequest::find($request->id)->delete();

        return response()->json(['status' => 'success']);
    }
}
