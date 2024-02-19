<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::where('hide', '=', 0)->get();
        return view('admin.pages.countries.index')->with(['countries'=>$countries]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.countries.create');
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
            'title.required'=>'Please Enter Country Name',
        ]);
        $country = new Country;
        $country->title  = $request->title;
        $country->save();   
        return redirect()->route('countries.index'); 
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
        $country = Country::findorfail($id);
        if($country->hide == 0)
        {
            return view('admin.pages.countries.edit')->with(['country'=>$country]);    
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
        $country = Country::findorfail($id);
        if($country->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Country Name',
            ]);
            $country->title  = $request->title;
            $country->save();   
            return redirect()->route('countries.index');  
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
        $country = Country::findorfail($id);
        $country->hide = 1;
        $country->save();
        return redirect()->route('countries.index'); 
    }

    public function countries_task(Request $request)
    {
        if($request->type == 'Delete')
        {
            for ($i = 0; $i < count($request->items); $i++)
            {
                $id = $request->items[$i];
                $user = Country::findorfail($id);
                $user->hide = 1;
                $user->save();
            }
        }
    }
}
