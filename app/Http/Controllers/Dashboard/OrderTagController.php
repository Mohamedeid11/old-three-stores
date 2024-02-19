<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\OrderTag;

class OrderTagController extends Controller
{
    public function index()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tags = OrderTag::all();
        return view('admin.pages.order_tags.index', compact(['tags']));
    }

    public function create()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        return view('admin.pages.order_tags.create');
    }

    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = new OrderTag;
        $validatedData = $request->validate([
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Tag Name',
        ]);
        $tag->title = $request->title;
        $tag->save();
        return redirect()->route('orders_tags.index');
    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'tags')) {abort(404);}
        $tag = OrderTag::findorfail($id);
        return view('admin.pages.order_tags.edit', compact(['tag']));
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
        $tag = OrderTag::findorfail($id);
        $validatedData = $request->validate([
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Tag Name',
        ]);
        $tag->title = $request->title;
        $tag->save();
        return redirect()->route('orders_tags.index');
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
        $tag = OrderTag::findorfail($id);
        $tag->delete();
        return redirect()->back();
    }

    public function tags_suggestions (Request $request)
    {
        $term = "";
        if ($request->has('term')) {$term = $request->term;}
        $tags = OrderTag::where('title' ,'LIKE', '%'.$term.'%')->pluck('title')->toArray();
        return response()->json(["suggestions" => $tags]);
    }

    public function tag_search (Request $request)
    {
        $sel = explode(',', $request->tags);
        $tags = OrderTag::whereIn('title', $sel)->pluck('id')->toArray();
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
        $tags = OrderTag::whereIn('title', $sel)->pluck('id');
        $selected_tags = productTag::whereIn('tag_id', $tags)->pluck('product_id');
        $products = Product::whereIn('id', $selected_tags)->where('hide', 0)->where('discontinue', 0)->get();
        return view('ajax.products_table')->with(["products"=>$products]);
    }
    public function changePlatform(Request  $request){
        $tag=OrderTag::findOrFail($request->tag_id);
        $tag->is_platform=$request->is_platform;
        $tag->save();
        return response()->json(['status'=>true]);
    }
}
