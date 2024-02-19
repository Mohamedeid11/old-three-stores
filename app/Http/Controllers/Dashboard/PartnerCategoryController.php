<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\PartnerCategory;

class PartnerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cats = PartnerCategory::where('hide', '=', 0)->get();
        return view('admin.pages.partners_categories.index')->with(['cats'=>$cats]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('admin.pages.expanses_categories.create');
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
        $cat = new PartnerCategory;
        $cat->title  = $request->title;
        $cat->save();   
        return redirect()->route('partners_categories.index'); 
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
        /*
        $cat = ExpanseCategory::findorfail($id);
        if($cat->hide == 0)
        {
            return view('admin.pages.expanses_categories.edit')->with(['cat'=>$cat]);    
        }
        else
        {
            abort(404);
        }
        */
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
        $cat = PartnerCategory::findorfail($id);
        if($cat->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Category Name',
            ]);
            $cat->title  = $request->title;
            $cat->save();   
            return redirect()->route('partners_categories.index');  
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
        $cat = PartnerCategory::findorfail($id);
        $cat->hide = 1;
        $cat->save();
        return redirect()->route('partners_categories.index'); 
    }

    public function partners_categories_task(Request $request)
    {
        if($request->type == 'Delete')
        {
            for ($i = 0; $i < count($request->items); $i++)
            {
                $id = $request->items[$i];
                $user = PartnerCategory::findorfail($id);
                $user->hide = 1;
                $user->save();
            }
        }
    }
}
