<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Size;
use App\ProductSize;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sizes = Size::where('hide', '=', 0)->get();
        return view('admin.pages.sizes.index')->with(['sizes'=>$sizes]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.sizes.create');
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
            'title' => 'required'
        ],
        [
            'title.required'=>'Please Enter Size Name'
        ]);
        $size = new Size;
        $size->title  = $request->title;
        $size->save();   
        return redirect()->route('sizes.index');  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $size = Size::findorfail($id);
        if($size->hide == 0)
        {
            return view('admin.pages.sizes.edit')->with(['size'=>$size]);    
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
        $size = Size::findorfail($id);
        if($size->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Size Name',
            ]);
            $size->title  = $request->title;
            $size->save();   
            return redirect()->route('sizes.index');  
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
        $size = Size::findorfail($id);
        $size->hide = 1;
        $size->save();
        $products = ProductSize::where('size', '=', $size->id)->get();
        foreach ($products as $product)
        {
            $product->delete();
        }
        return redirect()->back(); 
    }
}
