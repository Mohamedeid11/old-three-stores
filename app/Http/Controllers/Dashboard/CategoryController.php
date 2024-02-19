<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Category;
use Validator;

use Codexshaper\WooCommerce\Facades\Category as WooCommerceCategory; 
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Categories')) {abort(404);}
        $categories = Category::where('hide', '=', 0)->where('cat', '=', 0)->get();
        return view('admin.pages.categories.index')->with(['categories'=>$categories]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_category')) {abort(404);}
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Category Name',
        ]);
        if ($validator->fails())
        {
            return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
        }
        else
        {
            $checker = Category::where('title', $request->title)->where('cat', $request->cat)->first();
            if($checker !== NULL)
            {
                 return response()->json(['success' => false, 'errors'=>"Please Change Category Name"]);  
            }
            
            $category = new Category;
            $category->title  = $request->title;
            $category->cat  = $request->cat;
            $category->save();   
            

            if($category->cat == 0)
            {
                $name = $category->title;
                $data = ["name" => $name];
                $categorywoo = WooCommerceCategory::create($data);
                $category->woocommerce_id = $categorywoo->id;
                $category->save();
                return response()->json(['success' => true, 'message'=>"Category Has Been Added Successfully"]);
            }
            else
            {
                $parent = Category::where('cat', $category->cat)->first();
                $name = $category->title;
                $data = ["name" => $name, "parent"=>$parent->woocommerce_id];
                $categorywoo = WooCommerceCategory::create($data);
                $category->woocommerce_id = $categorywoo->id;
                $category->save();
                
                return response()->json(['success' => true, 'message'=>"Category Has Been Added Successfully"]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Categories')) {abort(404);}
        $main = Category::findorfail($id);
        if ($main->hide == 1 || $main->cat > 0) {abort(404);}
        $categories = Category::where('hide', '=', 0)->where('cat', '=', $id)->get();
        return view('admin.pages.categories.subs')->with(['categories'=>$categories, 'main'=>$main]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_category')) {abort(404);}
        $category = Category::findorfail($id);
        
        if($category->hide == 0)
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Category Name',
            ]);
            if ($validator->fails())
            {
                return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
            }
            else
            {
                $checker = Category::where('title', $request->title)->where('cat', $category->cat)->where('id', '!=', $id)->first();
                if($checker !== NULL)
                {
                     return response()->json(['success' => false, 'errors'=>"Please Change Category Name"]);  
                }
            
                $category->title  = $request->title;
                $category->save(); 
                
                $name = $category->title;
                $data = ["name" => $name];
                $categorywoo = WooCommerceCategory::update($category->woocommerce_id, $data);
                    
                if($category->cat == 0)
                {
                    return response()->json(['success' => true, 'message'=>"Category Has Been Added Successfully"]);
                }
                else
                {
                    
                    return response()->json(['success' => true, 'message'=>"Category Has Been Added Successfully"]);
                }
            }
        }
        else
        {
            abort(404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_category')) {abort(404);}
        $category = Category::findorfail($id);
        $category->hide = 1;
        $category->save();
        
        $categorywoo = WooCommerceCategory::delete($category->woocommerce_id, $data);
                        
        return redirect()->back(); 
    }


    public function get_subs(Request $request)
    {
        $id = $request->cat;
        $main = Category::findorfail($id);
        if ($main->hide == 0 && $main->cat == 0)
        {
            $categories = Category::where('hide', '=', 0)->where('cat', '=', $id)->get();
            echo '<option value="" disabled selected>Choose Category</option>';
            foreach ($categories as $cc)
            {
                ?>
                <option value="<?php echo $cc->id; ?>"><?php echo $cc->title; ?></option>
                <?php
            }
        }
    }
}
