<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

use App\Product;
use App\Category;

use App\ProductImage;


use Codexshaper\WooCommerce\Facades\Product as WooCommerceProduct; 
use Codexshaper\WooCommerce\Facades\Category as WooCommerceCategory; 
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;

class ProductImageController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $product = Product::findorfail($id);
        return view('admin.pages.products.create_images')->with(['product'=>$product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'required',
            'image.*' => 'mimes:jpeg,png,jpg,gif,svg'
        ],
        [
            'image.required' => 'Please Choose Product Images',
            'image.*.mimes' => 'Please Choose Product Images',
        ]);
        $product = Product::findorfail($request->product);
        $files = $request->file('image');
        $i = 0;
        foreach ($files as $image)
        {
            $product_img = new ProductImage;
            $product_img->product = $product->id;
            $imageName = str_random(20)."_".time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/products/'.$product->id);
            $image->move($destinationPath, $imageName);
            $product_img->image = '/uploads/products/'.$product->id.'/'.$imageName;
            $product_img->color = $request->color;
            $product_img->save();
            $i++;
        }
        update_woocommerce_product($product->id);
        
        return redirect()->route('products.show', $product->id);  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $image = ProductImage::findOrFail($id);
        $product = Product::findorfail($image->product);
        return view('admin.pages.products.edit_image')->with(['image'=>$image, 'product'=>$product]);
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
        $pimage = ProductImage::findOrFail($id);
        
        $validatedData = $request->validate([
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg'
        ],
        $messages = [
            'image.required' => 'Please Choose Image',
            'image.mimes' => 'Please Choose Image'
        ]);

        if (File::exists(public_path().$pimage->image))
        {
            File::delete(public_path().$pimage->image);
        }

        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('/uploads/products');
        $image->move($destinationPath, $imageName);
        $pimage->image = '/uploads/products/'.$imageName;
        $pimage->color = $request->color;
        $pimage->save();
        
        $product = Product::findorfail($pimage->product);
        
        update_woocommerce_product($product->id);
        
        return redirect()->route('products.show', $pimage->product);  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $image = ProductImage::findorfail($id);
        if (File::exists(public_path().$image->image))
        {
            File::delete(public_path().$image->image);
        }
        $product = Product::findorfail($image->product);
        $image->delete();

        update_woocommerce_product($product->id);

        return redirect()->back();  
    }
}
