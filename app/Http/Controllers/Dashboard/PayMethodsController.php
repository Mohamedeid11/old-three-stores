<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\PayMethod;

class PayMethodsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $methods = PayMethod::where('hide', '=', 0)->get();
        return view('admin.pages.pay_methods.index')->with(['methods'=>$methods]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.pay_methods.create');
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
            'title.required'=>'Please Enter Pay Method Name',
        ]);
        $method = new PayMethod;
        $method->title  = $request->title;
        $method->save();   
        return redirect()->route('pay_methods.index'); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $method = PayMethod::findorfail($id);
        if($method->hide == 0)
        {
            return view('admin.pages.pay_methods.edit')->with(['method'=>$method]);    
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
        $method = PayMethod::findorfail($id);
        if($method->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Pay Method Name',
            ]);
            $method->title  = $request->title;
            $method->save();   
            return redirect()->route('pay_methods.index');  
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
        $method = PayMethod::findorfail($id);
        $method->hide = 1;
        $method->save();
        return redirect()->route('pay_methods.index'); 
    }

    public function pay_methods_task(Request $request)
    {
        if($request->type == 'Delete')
        {
            for ($i = 0; $i < count($request->items); $i++)
            {
                $id = $request->items[$i];
                $user = PayMethod::findorfail($id);
                $user->hide = 1;
                $user->save();
            }
        }
    }
}