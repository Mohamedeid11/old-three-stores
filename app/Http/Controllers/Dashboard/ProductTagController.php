<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\TagGroup;
use App\productTag;
use App\Category;
use App\Product;

class ProductTagController extends Controller
{
    public function index()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tags = TagGroup::all();
        return view('admin.pages.tags.index', compact(['tags']));
    }

    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        return view('admin.pages.tags.create');
    }

    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = new TagGroup;
        $validatedData = $request->validate([
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Tag Name',
        ]);
        $tag->title = $request->title;
        $tag->save();
        return redirect()->route('products_tags.index');
    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = TagGroup::findorfail($id);
        return view('admin.pages.tags.edit', compact(['tag']));
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
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = TagGroup::findorfail($id);
        $validatedData = $request->validate([
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Tag Name',
        ]);
        $tag->title = $request->title;
        $tag->save();
        return redirect()->route('products_tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = TagGroup::findorfail($id);
        $tag->delete();
        productTag::where('tag_id', $id)->delete();
        return redirect()->back();
    }

    public function tags_suggestions (Request $request)
    {
        $term = "";
        if ($request->has('term')) {$term = $request->term;}
        $tags = TagGroup::where('title' ,'LIKE', '%'.$term.'%')->pluck('title')->toArray();
        return response()->json(["suggestions" => $tags]);
    }

    public function tag_search (Request $request)
    {
        $sel = explode(',', $request->tags);
        $tags = TagGroup::whereIn('title', $sel)->pluck('id')->toArray();
        $selected_tags = productTag::whereIn('tag_id', $tags)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $selected_tags)->where('hide', 0)->where('discontinue', 0)->pluck('cat')->toArray();
        $sub_categories = Category::whereIn('id', $products)->where('hide', 0)->pluck('cat')->toArray();
        $main_categories = Category::whereIn('id', $sub_categories)->where('hide', 0)->get();
        $selected_products_in = explode(',', $request->products);
        return view('ajax.products_list')->with(["main_categories" => $main_categories, "selected_products"=>$selected_tags,
        'selected_products_in'=>$selected_products_in]);
    }
    
    public function tag_search_dashboard (Request $request)
    {
        $sel = explode(',', $request->tags);
        $tags = TagGroup::whereIn('title', $sel)->pluck('id');
        $selected_tags = productTag::whereIn('tag_id', $tags)->pluck('product_id');


        $products = Product::whereIn('id', $selected_tags)
            ->where('hide', 0)
            ->where('discontinue', 0)
            ->with('colors', 'sizes') // Assuming you have defined relationships in your Product model
            ->get();

// Now, you can calculate the quantity and order the products by the maximum quantity
        $products = $products->sortByDesc(function ($product) {
            $qty = 0;

            if ($product->colors->count() > 0 || $product->sizes->count() > 0) {
                if ($product->colors->count() > 0) {
                    foreach ($product->colors as $color) {
                        if ($product->sizes->count() == 0) {
                            $qty += qty_sold_inventory($product->id, $color->color_info->id, 0);
                        } else {
                            foreach ($product->sizes as $size) {
                                $qty += qty_sold_inventory($product->id, $color->color_info->id, $size->size_info->id);
                            }
                        }
                    }
                } elseif ($product->colors->count() == 0 && $product->sizes->count() > 0) {
                    foreach ($product->sizes as $size) {
                        $qty += qty_sold_inventory($product->id, 0, $size->size_info->id);
                    }
                }
            } else {
                $qty += qty_sold_inventory($product->id, 0, 0);
            }

            $product->qty = $qty; // Add the calculated quantity to the product object
            return $qty;
        });

// Render your view with the ordered products

        return view('ajax.products_table')->with(["products"=>$products]);
    }
}
