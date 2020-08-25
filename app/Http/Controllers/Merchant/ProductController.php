<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $inputs = request()->except('page');

        $products = Product::where(function($q) use($inputs)
        {
            if(isset($inputs['name']))
            {
                $check = preg_match('@[a-zA-Z]@', $inputs['name']);
                if($check) $q->where('en_name','like','%'.$inputs['name'].'%');
                else $q->where('ar_name','like','%'.$inputs['name'].'%');
            }
            if(isset($inputs['status']) && $inputs['status'] != 'all') $q->where('status',$inputs['status']);
        })->latest()->paginate(20)->appends($inputs);

        $pending_count = Product::where('merchant_id',merchant()->id)->where('status','pending')->count();
        $active_count = Product::where('merchant_id',merchant()->id)->where('status','active')->count();
        $suspended_count = Product::where('merchant_id',merchant()->id)->where('status','suspended')->count();
        $blocked_count = Product::where('merchant_id',merchant()->id)->where('status','blocked')->count();

        return view('merchant.products.index', get_defined_vars());
    }


    public function create()
    {
        $products = Product::where('merchant_id',merchant()->id)->select('id','names')->get();
        return view('merchant.products.create',get_defined_vars());
    }


    public function store(Request $request)
    {
        $this->validate($request,
            [
                'cat_ids' => 'required|array',
                'cat_ids.*' => 'exists:categories,id,deleted,0,status,active',
                'related_ids' => 'required|array',
                'related_ids.*' => [function ($attribute,$value,$fail) use($request)
                    {
                        if($value != NULL && Product::where('id',$value)->exists() == FALSE) $fail('related_invalid');
                    }
                ],
                'names' => 'required|array|min:'.langs_count(),
                'names.*' => ['required',function ($attribute,$value,$fail) use($request)
                    {
                        if(isset(explode('.',$attribute)[1]) && ! in_array(explode('.',$attribute)[1],langs())) $fail('name_invalid');
                    }
                ],
                'sku' => 'required|unique:products,sku',
                'image' => 'required|image'
            ],
            [
                'cat_ids.required' => 'cat_ids_required',
                'cat_ids.array' => 'cat_ids_invalid',
                'cat_ids.*.exists' => 'cat_ids_invalid',
                'related_ids.required' => 'related_ids_required',
                'related_ids.array' => 'related_ids_invalid',
                'related_ids.*.exists' => 'related_ids_invalid',
                'sku.required' => 'sku_required',
                'sku.unique' => 'sku_exists',
                'image.required' => 'image_required',
                'image.image' => 'image_image',
            ]
        );

        $info = unique_file_folder($request->image->getClientOriginalExtension());
        $request->image->move(public_path('/uploads/products/'.$info['month']),$info['image']);

        $image = Image::make(public_path('/uploads/products/'.$info['name']));
            $image->resize(565, 520);
        $image->save(public_path('/uploads/products/'.$info['name']));

        $thumb_image = Image::make(public_path('/uploads/products/'.$info['name']));
            $thumb_image->resize(185, 175);
        $thumb_image->save(public_path('/uploads/products/'.$info['thumb_name']));

        $relateds = [];

        if($request->realted_ids) foreach($request->related_ids as $related) if($related != NULL) $relateds[] = $related;

        $product = Product::create
        (
            [
                'status' => 'pending',
                'merchant_id' => merchant()->id,
                'cat_ids' => json_encode($request->cat_ids),
                'related_ids' => json_encode($relateds),
                'names' => json_encode($request->names),
                'sku' => $request->sku,
                'image' => $info['name'],
                'thumb_image' => $info['thumb_name'],
            ]
        );

        return redirect('/merchant/product/'. $product->id .'/edit?tap=2')->with('success','created_complete');
    }


    public function edit($id, Request $request)
    {
        $request->merge(['id' => $id]);

        $this->validate($request,
            [
                'id' => 'exists:products,id,deleted,0,merchant_id,'.merchant()->id
            ]
        );

        $inputs = request()->query();
        $products = Product::where('merchant_id',merchant()->id)->select('id','names')->get();

        $edit = Product::find($id);

        return view('merchant.products.edit', get_defined_vars());
    }


    public function update_base(Request $request)
    {

        $this->validate($request,
            [
                'id' => 'required|exists:merchants,id',
                'name' => 'required|unique:merchants,name,' . $request->id,
                'email' => 'required|email|unique:merchants,email,' . $request->id,
                'phone' => 'required|unique:merchants,phone,' . $request->id,
                'password' => 'nullable|min:6|confirmed',
            ],
            [
                'name.required' => 'name_required',
                'email.required' => 'email_required',
                'email.email' => 'email_email',
                'email.unique' => 'email_exists',
                'phone.required' => 'phone_required',
                'phone.unique' => 'phone_exists',
                'password.min' => 'password_min_6',
                'password.confirmed' => 'password_confirmed',
            ]
        );

        $admin = Product::find($request->id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        $request->password ? $admin->password = Hash::make($request->password) : false;
        $admin->save();

        return redirect('/admin/merchants/index')->with('success', 'updated');
    }


    public function meta_update(Request $request)
    {

        throw ValidationException::withMessages(['specs' => 'spec_invalid']);
        dd($request->all());

        $this->validate($request,
            [
                'id' => 'required|exists:products,id,merchant_id,'.merchant()->id,
                'descs' => 'sometimes|array|min:'.langs_count(),
                'descs.*' => ['sometimes',function ($attribute,$value,$fail) use($request)
                    {
                        if(isset(explode('.',$attribute)[1]) && ! in_array(explode('.',$attribute)[1],langs())) $fail('desc_invalid');
                    }],
                'specs' => 'sometimes|array',
                'specs.*' => ['sometimes',function ($attribute,$value,$fail) use($request)
                    {
                        if(langs_count() != count($value)) $fail('enter_all_langs');
                        foreach(langs() as $lang)
                        {
                            if(array_search(NULL,$value[$lang])) $fail('enter_all_langs');
                            if(! in_array($lang,array_keys($value))) $fail('spec_invalid');
                        }
                    }],
                'images' => 'sometimes|array',
                'images.*' => 'image',
                'deleted' => 'sometimes'
            ],
            [
                'descs.array' => 'descs_invalid',
                'descs.min' => 'enter_all_langs',
                'specs.array' => 'specs_invalid',
                'specs.min' => 'enter_all_langs',
                'images.array' => 'images_invalid',
                'images.*.image' => 'images_invalid',

            ]
        );

        foreach($request->all() as $key => $value)
        {
            if(in_array($key,['descs','short_descs']))
            {
                ProductMeta::updateOrcreate
                (
                    [
                        'product_id' => $request->id,
                        'key' => $key
                    ],
                    [
                        'value' => json_encode($value)
                    ]
                );
            }
            elseif($key == 'images')
            {
                foreach($value as $image)
                {
                    $info = unique_file_folder($image->getClientOriginalExtension());
                    $image->move(public_path('/uploads/products/'.$info['month']),$info['image']);

                    $image = Image::make(public_path('/uploads/products/'.$info['name']));
                        $image->resize(565, 520);
                    $image->save(public_path('/uploads/products/'.$info['name']));

                    $thumb_image = Image::make(public_path('/uploads/products/'.$info['name']));
                        $thumb_image->resize(185, 175);
                    $thumb_image->save(public_path('/uploads/products/'.$info['thumb_name']));

                    $collection = collect(['image' => $info['name'],'thumb' => $info['thumb_name']]);

                    ProductMeta::create
                    (
                        [
                            'product_id' => $request->id,
                            'key' => 'image',
                            'value' => json_encode($collection)
                        ]
                    );
                }
            }
            elseif($key == 'specs')
            {   dd($value);
                foreach($value as $key => $this_value)
                {
                    ProductMeta::updateOrcreate
                    (
                        [
                            'id' => $key,
                            'product_id' => $request->id,
                        ],
                        [
                            'key' => 'spec',
                            'value' => json_encode($this_value)
                        ]
                    );
                }

            }
            elseif($key == 'deleted')
            {
                foreach(explode(',',$value) as $id)
                {
                    ProductMeta::where('id',$id)->where('product_id',$request->id)->where('key','image')->unlink()->delete();
                }
            }
        }

        return redirect('/merchant/product/'. $request->id .'/edit?tab=meta')->with('success','updated');
    }


    public function change_status(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,merchant_id,'.merchant()->id,
                'status' => 'required|in:active,suspended',
            ]
        );

        Product::find($request->id)->where('status','!=','blocked')->update(['status' => $request->status]);

        return back()->with('success','status_changed');
    }


    public function destroy(Request $request)
    {
        $this->validate($request,
            [
                'id' => 'required|exists:products,id,merchant_id,'.merchant()->id
            ]
        );

        Product::where('id',$request->id)->update(['deleted' => 1]);

        return back()->with('success', 'deleted');
    }
}
