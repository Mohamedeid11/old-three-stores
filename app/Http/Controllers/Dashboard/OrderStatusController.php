<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\OrderStatus;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $statuses = OrderStatus::where('hide', '=', 0)->get();
        return view('admin.pages.status.index')->with(['statuses'=>$statuses]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.status.create');
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
            'title.required'=>'Please Enter Status Name',
        ]);
        $status = new OrderStatus;
        $status->title  = $request->title;
        $status->save();   
        return redirect()->route('order_status.index'); 
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
        $status = OrderStatus::findorfail($id);
        if($status->hide == 0)
        {
            return view('admin.pages.status.edit')->with(['status'=>$status]);    
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
        $status = OrderStatus::findorfail($id);
        if($status->hide == 0)
        {
            $validatedData = $request->validate([
                'title' => 'required',
            ],
            [
                'title.required'=>'Please Enter Status Name',
            ]);
            $status->title  = $request->title;
            $status->save();   
            return redirect()->route('order_status.index');  
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
        $status = OrderStatus::findorfail($id);
        $status->hide = 1;
        $status->save();
        return redirect()->route('order_status.index'); 
    }

    public function changeIsCounted(Request  $request){
        $status=OrderStatus::findOrFail($request->status_id);
        $status->is_counted=$request->is_counted;
        $status->save();
        return response()->json(['status'=>true]);
    }
}
