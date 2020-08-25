<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $categories = Category::where('type','main')->latest()->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where('en_name','like','%'.$inputs['name'].'%');
                else $q->where('ar_name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','type','status',lang().'_name as name')->paginate(20)->appends($inputs);

        $active_count = Category::where('type','main')->where('status','active')->count();
        $active_sub_count = Category::where('type','sub')->where('status','active')->count();
        $active_sec_count = Category::where('type','sec')->where('status','active')->count();
        $suspended_count = Category::where('type','main')->where('status','suspended')->count();
        $suspended_sub_count = Category::where('type','sub')->where('status','suspended')->count();
        $suspended_sec_count = Category::where('type','sec')->where('status','suspended')->count();

        return view('admin.categories.index', get_defined_vars());
    }


    public function subs($parent_id,Request $request)
    {
        $request->merge(['parent_id' => $parent_id]);
        $this->validate($request,
            [
                'parent_id' => 'required|exists:categories,id,deleted,0'
            ]
        );

        $parent = Category::where('id',$parent_id)->select('id',lang().'_name as name')->first();
        $inputs = request()->except('parent_id','page');

        $categories = Category::where('type','!=','main')->where('parent_id',$parent_id)->where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where('en_name','like','%'.$inputs['name'].'%');
                else $q->where('ar_name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->select('id','type','status',lang().'_name as name')->paginate(20);

        return view('admin.categories.index',get_defined_vars());
    }


    public function create()
    {
        return view('admin.categories.single',get_defined_vars());
    }


    public function create_sub()
    {
        $categories = Category::where('type','main')->where('status','active')->select('id',lang().'_name as name')->get();
        return view('admin.categories.sub_single',get_defined_vars());
    }


    public function create_sec()
    {
        $categories = Category::where('type','main')->where('status','active')->select('id',lang().'_name as name')->get();
        return view('admin.categories.sec_single',get_defined_vars());
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
//                'ar_name' => 'required|unique:categories',
//                'en_name' => 'required|unique:categories',
                'ar_name' => 'required',
                'en_name' => 'required',
                'image' => 'required|image',
                'status' => 'in:active,suspended'
            ],
            [
                'ar_name.required' => 'ar_name_required',
//                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
//                'en_name.unique' => 'en_name_exists',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
                'status.in' => 'status_invalid'
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/categories/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/categories/'.$info['name']));
            $image->resize(50, 50);
        $image->save(public_path('/uploads/categories/'.$info['name']));

        $status = $request->status ? 'active' : 'suspended';

        Category::create
        (
            [
                'type' => 'main',
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
                'image' => $info['name'],
            ]
        );

        return redirect('/admin/categories/index')->with('success', 'created');
    }


    public function store_sub(Request $request)
    {
        $this->validate($request,
            [
                'parent_id' => 'required|exists:categories,id,deleted,0,type,main',
                'ar_name' => 'required',
                'en_name' => 'required',
                'status' => 'in:active,suspended'
            ],
            [
                'parent_id.required' => 'category_parent_required',
                'parent_id.exists' => 'category_parent_exists',
                'ar_name.required' => 'ar_name_required',
//                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
//                'en_name.unique' => 'en_name_exists',
                'status.in' => 'status_invalid'
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Category::create
        (
            [
                'parent_id' => $request->parent_id,
                'type' => 'sub',
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
            ]
        );

        return redirect('/admin/category/'.$request->parent_id.'/subs')->with('success', 'created');
    }


    public function store_sec(Request $request)
    {
        $this->validate($request,
            [
                'parent_id' => 'required|exists:categories,id,deleted,0,type,sub',
                'ar_name' => 'required',
                'en_name' => 'required',
                'status' => 'in:active,suspended'
            ],
            [
                'parent_id.required' => 'category_parent_required',
                'parent_id.exists' => 'category_parent_exists',
                'ar_name.required' => 'ar_name_required',
//                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
//                'en_name.unique' => 'en_name_exists',
                'status.in' => 'status_invalid'
            ]
        );

        $status = $request->status ? 'active' : 'suspended';

        Category::create
        (
            [
                'parent_id' => $request->parent_id,
                'type' => 'sec',
                'status' => $status,
                'ar_name' => $request->ar_name,
                'en_name' => $request->en_name,
            ]
        );

        return redirect('/admin/category/'.$request->parent_id.'/subs')->with('success', 'created');
    }


    public function edit($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0,type,main'
            ]
        );

        $edit = Category::find($id);

        return view('admin.categories.single',get_defined_vars());
    }


    public function edit_sub($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0,type,sub'
            ]
        );

        $categories = Category::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Category::find($id);

        return view('admin.categories.sub_single',get_defined_vars());
    }


    public function edit_sec($id,Request $request)
    {
        $request->merge(['id' => $id]);
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0,type,sec'
            ]
        );

        $categories = Category::where('type','main')->select('id',lang().'_name as name')->get();
        $edit = Category::find($id);

        return view('admin.categories.sec_single',get_defined_vars());
    }


    public function update(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0',
                'ar_name' => 'required|unique:categories,ar_name,'.$request->id,
                'en_name' => 'required|unique:categories,en_name,'.$request->id,
                'image' => 'sometimes|image'
            ],
            [
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
                'image.image' => 'image_image'
            ]
        );

        $category = Category::find($request->id);
            $category->ar_name = $request->ar_name;
            $category->en_name = $request->en_name;
            if($request->image)
            {
                $info = unique_file_folder($request->image->getClientOriginalExtension());
                $request->image->move(public_path('/uploads/categories/'.$info['month']),$info['image']);

                $image = Image::make(public_path('/uploads/categories/'.$info['name']));
                $image->resize(230, 230);
                $image->save(public_path('/uploads/categories/'.$info['name']));

                @unlink(public_path('/uploads/categories/'.$category->getOriginal('image')));
                $category->image = $info['name'];
            }
        $category->save();

        return redirect('/admin/categories/index')->with('success', 'updated');
    }


    public function update_sub(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0',
                'parent_id' => 'required|exists:categories,id,deleted,0,type,main',
                'ar_name' => 'required|unique:categories,ar_name,'.$request->id,
                'en_name' => 'required|unique:categories,en_name,'.$request->id,
            ],
            [
                'parent_id.required' => 'category_parent_required',
                'parent_id.exists' => 'category_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
            ]
        );

        $category = Category::find($request->id);
            $category->parent_id = $request->parent_id;
            $category->ar_name = $request->ar_name;
            $category->en_name = $request->en_name;
        $category->save();

        return redirect( '/admin/category/'.$category->parent_id.'/subs')->with('success', 'updated');
    }


    public function update_sec(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0',
                'parent_id' => 'required|exists:categories,id,deleted,0,type,sub',
                'ar_name' => 'required|unique:categories,ar_name,'.$request->id,
                'en_name' => 'required|unique:categories,en_name,'.$request->id,
            ],
            [
                'parent_id.required' => 'category_parent_required',
                'parent_id.exists' => 'category_parent_exists',
                'ar_name.required' => 'ar_name_required',
                'ar_name.unique' => 'ar_name_exists',
                'en_name.required' => 'en_name_required',
                'en_name.unique' => 'en_name_exists',
            ]
        );

        $category = Category::find($request->id);
            $category->parent_id = $request->parent_id;
            $category->ar_name = $request->ar_name;
            $category->en_name = $request->en_name;
        $category->save();

        return redirect( '/admin/category/'.$category->parent_id.'/subs')->with('success', 'updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0',
                'status' => 'required|in:active,suspended',
            ]
        );

        Category::where('id', $request->id)->update(['status' => $request->status]);
        Product::where('main_cat_id',$request->id)->orWhere('sub_cat_id',$request->id)->orWhere('sec_cat_id',$request->id)->update(['status' => $request->status]);
        return back()->with('success', 'status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:categories,id,deleted,0',
            ]
        );

        Category::where('id', $request->id)->orWhere('parent_id',$request->id)->update(['deleted' => 1]);
        Product::where('main_cat_id',$request->id)->orWhere('sub_cat_id',$request->id)->orWhere('sec_cat_id',$request->id)->update(['deleted' => 1]);


        return back()->with('success', 'deleted');
    }
}
