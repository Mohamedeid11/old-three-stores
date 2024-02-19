<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\OrderCategory;

class OrderCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = OrderCategory::where('hide', '=', 0)->get();
        return view('admin.pages.category.index')->with(['statuses'=>$statuses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.category.create');
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
            'title' => 'required',
        ],
        [
            'title.required'=>'Please Enter Category Name',
        ]);
        $status = new OrderCategory;
        $status->title  = $request->title;
        $status->order_symbol  = $request->order_symbol;
        $status->save();   
        return redirect()->route('order_category.index'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $status = OrderCategory::findorfail($id);
        if($status->hide == 0)
        {
            return view('admin.pages.category.edit')->with(['status'=>$status]);    
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
        $status = OrderCategory::findorfail($id);
        if($status->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Category Name',
            ]);
            $status->title  = $request->title;
            $status->order_symbol  = $request->order_symbol;
            $status->save();   
            return redirect()->route('order_category.index');  
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
        $status = OrderCategory::findorfail($id);
        $status->hide = 1;
        $status->save();
        return redirect()->route('order_category.index'); 
    }
}
