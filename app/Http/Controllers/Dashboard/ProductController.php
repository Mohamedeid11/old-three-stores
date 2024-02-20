<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use App\Product;

use App\Category;
use App\Color;
use App\Size;

use App\ProductColor;
use App\ProductImage;
use App\ProductSize;
use App\Inventory;
use App\TagGroup;
use App\productTag;

use Codexshaper\WooCommerce\Facades\Product as WooCommerceProduct; 
use Codexshaper\WooCommerce\Facades\Category as WooCommerceCategory; 
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Products')) {abort(404);}
        if($request->get('type'))
        {
            $selected_type = $request->type;
        }else{
            $selected_type = 'continue';
        }
        $cats = Category::where('hide', '=', 0)->get();
        $selected_cat = array();
        if($request->get('cat'))
        {
            $selected_cat = $request->cat;
        }

        $selected_tags = array();
        $selected_tags_text = "";
        $selected_product = array();
        if(Input::get('tags'))
        {
            $selected_tags = explode(',', Input::get('tags'));
            if(count($selected_tags) > 0)
            {
                $slected_tags_ids = TagGroup::whereIn('title', $selected_tags)->pluck('id')->toArray();
                $selected_product = productTag::whereIn('tag_id', $slected_tags_ids)->pluck('product_id')->toArray();
            }
        }

        if(count($selected_cat) > 0)
        {
            $products = Product::whereIn('cat', $selected_cat)->where('hide', '=', 0);
        }
        else
        {
            $products = Product::where('hide', '=', 0);
        }
        if(count($selected_tags) > 0)
        {
            $products = $products->whereIn('id', $selected_product);
        }

        if ($selected_type == 'continue') {$products = $products->where('discontinue', 0);}
        else if ($selected_type == 'discontinue') {$products = $products->where('discontinue', 1);}

        if ($request->product_id){
            $productIdes = array_map('intval', $request->input('product_id'));

            $products=$products->whereIn('id',$productIdes);
        }

        $queryParameters = $request->query();


        $products = $products->orderBy('title')->with('cat_info')->paginate(30);
        return view('admin.pages.products.index')->with(['queryParameters'=>$queryParameters,'products'=>$products, 'cats'=>$cats, 'selected_cat'=>$selected_cat,
        'selected_type'=>$selected_type, 'selected_tags'=>$selected_tags, 'selected_tags_text'=>$selected_tags_text]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_product')) {abort(404);}
        $colors = Color::where('hide', '=', 0)->get();
        $sizes = Size::where('hide', '=', 0)->get();
        $cats = Category::where('hide', '=', 0)->where('cat', '=', 0)->get();
        return view('admin.pages.products.create')->with(['colors'=>$colors, 'sizes'=>$sizes, 'cats'=>$cats]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_product')) {abort(404);}
        $validatedData = $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
            'tags'=>'required',
            // 'cat' => 'required',
        ],
        [
            'title.required'=>'Please Enter Product Name',
            'price.required'=>'Please Enter Product Price',
            'price.numeric'=>'Product Price Must Be Number',
            // 'cat.required' => 'Please Choose Product Price',
            'tags.required'=>'please Enter Tags',
        ]);
        $product = new Product;
        $product->title = $request->title;
        $product->price = $request->price;
        $product->text = $request->text;

        if($request->has('cat'))
        {
            $product->cat = $request->cat;
        }
        elseif($request->has('main_cat'))
        {
            $product->cat = $request->main_cat;
        }
        else
        {
            $product->cat = 0;
        }
        $product->save();
        if($request->colors)
        {
            for ($i = 0; $i < count($request->colors); $i++)
            {
                $pc = new ProductColor;
                $pc->color = $request->colors[$i];
                $pc->product = $product->id;
                $pc->save();
            }
        }

        if($request->size)
        {
            for ($i = 0; $i < count($request->size); $i++)
            {
                $pc = new ProductSize;
                $pc->size = $request->size[$i];
                $pc->product = $product->id;
                $pc->save();
            }
        }

        // add_product_to_wp($product->id);

        $inventory =  Inventory::where('product', $product->id)->first();
        if($inventory === NULL && count($product->colors) == 0 && count($product->sizes) == 0)
        {
            $n = new Inventory;
            $n->product = $product->id;
            $n->color = 0;
            $n->size = 0;
            $n->save();
        }
        else
        {
            if(count($product->colors) > 0 && count($product->sizes) > 0)
            {
                foreach ($product->colors as $color)
                {
                    foreach ($product->sizes as $size)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', $size->size)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = $color->color;
                            $n->size = $size->size;
                            $n->save();
                        }
                    }
                }
            }
            else if(count($product->colors) > 0 && count($product->sizes) == 0)
            {
                foreach ($product->colors as $color)
                {
                    $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', 0)->first();
                    if($inventory === NULL)
                    {
                        $n = new Inventory;
                        $n->product = $product->id;
                        $n->color = $color->color;
                        $n->size = 0;
                        $n->save();
                    }
                }
            }
            else if(count($product->sizes) == 0 && count($product->colors) > 0)
            {
                foreach ($product->sizes as $size)
                {
                    $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $size->size)->first();
                    if($inventory === NULL)
                    {
                        $n = new Inventory;
                        $n->product = $product->id;
                        $n->color = 0;
                        $n->size = $size->size;
                        $n->save();
                    }
                }
            }

        }

        $all_tags = explode(',', $request->tags);
        if(count($all_tags) > 0)
        {
            for ($i = 0; $i < count($all_tags); $i++)
            {
                if($all_tags[$i] != '')
                {
                    $cht = TagGroup::where('title', $all_tags[$i])->first();
                    if($cht === NULL)
                    {
                        $cht = new TagGroup;
                        $cht->title = $all_tags[$i];
                        $cht->save();
                    }

                    $oo = new productTag;
                    $oo->product_id = $product->id;
                    $oo->tag_id = $cht->id;
                    $oo->save();
                }
            }
        }
        return redirect()->route('products.create');
    }

    public function show($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_product')) {abort(404);}
        $product = Product::findorfail($id);
        return view('admin.pages.products.images')->with(['product'=>$product]);
    }

    public function products_timeline ($id, Request $request)
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Products')) {abort(404);}
        $product = Product::findorfail($id);
        if($request->has('from_date'))
        {
            $from_date = $request->from_date;
        }
        else
        {
            $from_date = date('Y-m-d', strtotime('- 7 Days'));
        }
        if($from_date == '') {$from_date = date('Y-m-d', strtotime('- 7 Days'));}

        if($request->has('to_date'))
        {
            $to_date = $request->to_date;
        }
        else
        {
            $to_date = date('Y-m-d');
        }
        if($to_date == '') {$to_date = date('Y-m-d');}

        $from_date_time = $from_date." 00:00:00";
        $to_date_time = $to_date." 23:59:59";

        $color = 0;
        $size = 0;
        $timelines = $product->timeline->where('created_at', '>=', $from_date_time)
        ->where('created_at', '<=', $to_date_time);

        return view('admin.pages.products.timeline')->with(['product'=>$product, 'timelines'=>$timelines,
        'from_date'=>$from_date, 'to_date'=>$to_date]);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_product')) {abort(404);}
        $product = Product::findorfail($id);
        if($product->hide == 0)
        {
            $colors = Color::where('hide', '=', 0)->get();
            $sizes = Size::where('hide', '=', 0)->get();
            $cats = Category::where('hide', '=', 0)->where('cat', '=', 0)->get();
            $cat = Category::where('id', $product->cat)->first();
            if($cat !== NULL)
            {
                $main_cat = $cat->cat;
                $sub_cats = Category::where('hide', '=', 0)->where('cat', '=', $cat->cat)->get();
            }
            else
            {
                $main_cat = 0;
                $sub_cats = Category::where('hide', '=', 0)->where('cat', '=', $product->cat)->get();
            }
            $product_colors = array();
            $pps = ProductColor::where('product', '=', $id)->get();
            foreach ($pps as $a) {$product_colors[] = $a->color;}
            $product_sizes = array();
            $pps = ProductSize::where('product', '=', $id)->get();
            foreach ($pps as $a) {$product_sizes[] = $a->size;}
            $tags_ids = productTag::where('product_id', $id)->pluck('tag_id')->toArray();
            $all_tags = implode(',', TagGroup::whereIn('id', $tags_ids)->pluck('title')->toArray());
            return view('admin.pages.products.edit')->with(['product'=>$product, 'colors'=>$colors, 'sizes'=>$sizes, 'cats'=>$cats,
            'main_cat'=>$main_cat, 'sub_cats'=>$sub_cats, 'product_colors'=>$product_colors, 'product_sizes'=>$product_sizes,
            'all_tags'=>$all_tags]);
        }
        else
        {
            abort(404);
        }
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_product')) {abort(404);}
        $product = Product::findorfail($id);
        if($product->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
                'price' => 'required|numeric',
                // 'cat' => 'required'
                'tags'=>'required',
            ],
            [
                'title.required'=>'Please Enter Product Name',
                'price.required'=>'Please Enter Product Price',
                'price.numeric'=>'Product Price Must Be Number',
                // 'cat.required' => 'Please Choose Product Price'
                'tags.required'=>'please Enter Tags',

            ]);
            $product->title = $request->title;
            $product->price = $request->price;
            $product->text = $request->text;
            // $product->tags = $request->tags;
            if($request->has('cat'))
            {
                $product->cat = $request->cat;
            }
            elseif($request->has('main_cat'))
            {
                $product->cat = $request->main_cat;
            }
            else
            {
                $product->cat = 0;
            }
            $product->save();
            $pps = ProductColor::where('product', '=', $id)->get();
            foreach ($pps as $a) {$a->delete();}
            $pps = ProductSize::where('product', '=', $id)->get();
            foreach ($pps as $a) {$a->delete();}
            Inventory::where('product',$id)->update([
                'hide'=>1,
            ]);
            if($request->colors)
            {
                for ($i = 0; $i < count($request->colors); $i++)
                {
                    $pc = new ProductColor;
                    $pc->color = $request->colors[$i];
                    $pc->product = $product->id;
                    $pc->save();
                }
            }
            if($request->size)
            {
                for ($i = 0; $i < count($request->size); $i++)
                {
                    $pc = new ProductSize;
                    $pc->size = $request->size[$i];
                    $pc->product = $product->id;
                    $pc->save();
                }
            }
            $inventory =  Inventory::where('product', $product->id)->first();
            if ($inventory) {
                $inventory->hide = 0;
                $inventory->save();
            }
            if($inventory === NULL && count($product->colors) == 0 && count($product->sizes) == 0)
            {
                $n = new Inventory;
                $n->product = $product->id;
                $n->color = 0;
                $n->size = 0;
                $n->save();
            }
            else
            {
                if(count($product->colors) > 0 && count($product->sizes) > 0)
                {
                    foreach ($product->colors as $color)
                    {
                        foreach ($product->sizes as $size)
                        {
                            $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', $size->size)->first();
                            if($inventory === NULL)
                            {
                                $n = new Inventory;
                                $n->product = $product->id;
                                $n->color = $color->color;
                                $n->size = $size->size;
                                $n->save();
                            }
                            else{
                                $inventory->hide=0;
                                $inventory->save();
                            }
                        }
                    }
                }
                else if(count($product->colors) > 0 && count($product->sizes) == 0)
                {
                    foreach ($product->colors as $color)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', 0)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = $color->color;
                            $n->size = 0;
                            $n->save();
                        }
                        else{
                            $inventory->hide=0;
                            $inventory->save();
                        }
                    }
                }
                else if(count($product->sizes) == 0 && count($product->colors) > 0)
                {
                    foreach ($product->sizes as $size)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $size->size)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = 0;
                            $n->size = $size->size;
                            $n->save();
                        }
                        else{
                            $inventory->hide=0;
                            $inventory->save();
                        }
                    }
                }

            }

            $prspps = productTag::where('product_id', $product->id)->get();
            foreach ($prspps as $ppo)
            {
                $oo = productTag::where('product_id', '!=' ,$product->id)->where('tag_id', $ppo->tag_id)->get()->count();
                if($oo == 0)
                {
                    TagGroup::where('id', $ppo->tag_id)->delete();
                }
                $ppo->delete();
            }

            $all_tags = explode(',', $request->tags);
            if(count($all_tags) > 0)
            {
                for ($i = 0; $i < count($all_tags); $i++)
                {
                    if($all_tags[$i] != '')
                    {
                        $cht = TagGroup::where('title', $all_tags[$i])->first();
                        if($cht === NULL)
                        {
                            $cht = new TagGroup;
                            $cht->title = $all_tags[$i];
                            $cht->save();
                        }

                        $oo = new productTag;
                        $oo->product_id = $product->id;
                        $oo->tag_id = $cht->id;
                        $oo->save();
                    }
                }
            }

            //update_woocommerce_product($product->id);
            return redirect()->back();
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_product')) {abort(404);}
        $product = Product::findorfail($id);
        $product->hide = 1;
        $product->save();

        // DB::connection('mysql2')->table("wp_thposts")->where('ID', $product->woocommerce)->update(['post_status'=>'trash']);
        return redirect()->back();
    }

    // Copy product
    public function copy_product ($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'copy_product')) {abort(404);}
        $product = Product::findorfail($id);
        if($product->hide == 1) {abort(404);}
        $new_pr = $product->replicate();
        $new_pr->save();

        $pps = ProductColor::where('product', '=', $id)->get();
        foreach ($pps as $a)
        {
            $pc = new ProductColor;
            $pc->color = $a->color;
            $pc->product = $new_pr->id;
            $pc->save();
        }
        $pps = ProductSize::where('product', '=', $id)->get();
        foreach ($pps as $a)
        {
            $pc = new ProductSize;
            $pc->size = $a->size;
            $pc->product = $new_pr->id;
            $pc->save();
        }

        // add_product_to_wp($new_pr->id);

        $product = Product::find($new_pr->id);
        if(count($product->colors) == 0 && count($product->sizes) == 0)
        {
            $n = new Inventory;
            $n->product = $product->id;
            $n->color = 0;
            $n->size = 0;
            $n->save();
        }
        else
        {
            if(count($product->colors) > 0 && count($product->sizes) > 0)
            {
                foreach ($product->colors as $color)
                {
                    foreach ($product->sizes as $size)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', $size->size)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = $color->color;
                            $n->size = $size->size;
                            $n->save();
                        }
                    }
                }
            }
            else if(count($product->colors) > 0 && count($product->sizes) == 0)
            {
                foreach ($product->colors as $color)
                {
                    $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', 0)->first();
                    if($inventory === NULL)
                    {
                        $n = new Inventory;
                        $n->product = $product->id;
                        $n->color = $color->color;
                        $n->size = 0;
                        $n->save();
                    }
                }
            }
            else if(count($product->sizes) == 0 && count($product->colors) > 0)
            {
                foreach ($product->sizes as $size)
                {
                    $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $size->size)->first();
                    if($inventory === NULL)
                    {
                        $n = new Inventory;
                        $n->product = $product->id;
                        $n->color = 0;
                        $n->size = $size->size;
                        $n->save();
                    }
                }
            }

        }

        return redirect()->route('products.edit', $product->id);
    }

    public function product_discontinue ($id, Request $request)
    {
        $product = Product::findorfail($id);
        if($product->discontinue == 0) {$product->discontinue = 1;}
        else {$product->discontinue = 0;}
        $product->save();
    }

    public function products_new_tags (Request $request)
    {
        $all_tags = explode(',', $request->tags);
        for ($i = 0; $i < count($request->items); $i++)
        {
            for ($j = 0; $j < count($all_tags); $j++)
            {
                $cht = TagGroup::where('title', $all_tags[$j])->first();
                if($cht === NULL)
                {
                    $cht = new TagGroup;
                    $cht->title = $all_tags[$j];
                    $cht->save();
                }

                $oo = new productTag;
                $oo->product_id = $request->items[$i];
                $oo->tag_id = $cht->id;
                $oo->save();
            }
        }
        return response()->json(['success' => true, 'message'=>"New Tags Inserted Successfully"]);
    }
       public  function getTags(Request  $request){
           if ($request->ajax()) {

               $term = trim($request->term);
               $posts = DB::table('tag_groups')->select('id', 'title as text')
                   ->where('title', 'LIKE', '%' . $term . '%')
                   ->orderBy('title', 'asc')->simplePaginate(10);

               $morePages = true;
               $pagination_obj = json_encode($posts);
               if (empty($posts->nextPageUrl())) {
                   $morePages = false;
               }
               $results = array(
                   "results" => $posts->items(),
                   "pagination" => array(
                       "more" => $morePages
                   )
               );

               return \Response::json($results);

           }

       }


        public function getProducts(Request $request){
            if ($request->ajax()) {

                $term = trim($request->term);
                $posts = DB::table('products')->select('id', 'title as text')
                    ->where('title', 'LIKE', '%' . $term . '%')
                    ->where('hide',0);

                if(isset($request->type)){
                    if ($request->type == 'continue'){
                        $posts->where('discontinue',0);
                    }elseif ($request->type == 'discontinue'){
                        $posts->where('discontinue',1);
                    }
                }else{
                    $posts->where('discontinue',0);
                }

                $posts = $posts->orderBy('title', 'asc')->simplePaginate(6);
                $morePages = true;
                $pagination_obj = json_encode($posts);
                if (empty($posts->nextPageUrl())) {
                    $morePages = false;
                }
                $results = array(
                    "results" => $posts->items(),
                    "pagination" => array(
                        "more" => $morePages
                    )
                );

                return \Response::json($results);

            }


        }

}
