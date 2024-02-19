<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Color;
use App\ProductColor;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $colors = Color::where('hide', '=', 0)->get();
        return view('admin.pages.colors.index')->with(['colors'=>$colors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.colors.create');
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
            'title.required'=>'Please Enter Color Name'
        ]);
        $color = new Color;
        $color->title  = $request->title;
        $color->color  = $request->color;
        $color->save();   
        return redirect()->route('colors.index');  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $color = Color::findorfail($id);
        if($color->hide == 0)
        {
            return view('admin.pages.colors.edit')->with(['color'=>$color]);    
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
        $color = Color::findorfail($id);
        if($color->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Color Name',
            ]);
            $color->title  = $request->title;
            $color->color  = $request->color;
            $color->save();   
            return redirect()->route('colors.index');  
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
        $color = Color::findorfail($id);
        $color->hide = 1;
        $color->save();
        $products = ProductColor::where('color', '=', $color->id)->get();
        foreach ($products as $product)
        {
            $product->delete();
        }
        return redirect()->back(); 
    }
}
