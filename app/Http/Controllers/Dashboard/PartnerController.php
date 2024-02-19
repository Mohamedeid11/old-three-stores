<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;

use App\PartnerCategory;
use App\Partner;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cats = PartnerCategory::where('hide', '=', 0)->get();
        $expanses = Partner::where('hide', '=', 0);

        $filter_day = 0;
        $filter_month = date('m');
        $filter_year = date('Y');
        $filter_cat = 0;
        $total_expanses = 0;
        $usd_total_expanses = 0;
        if(Input::get('day'))
        {
             $filter_day = Input::get('day');
        }
        if(Input::get('month'))
        {
             $filter_month = Input::get('month');
        }
        if(Input::get('year'))
        {
             $filter_year = Input::get('year');
        }
        if(Input::get('cat'))
        {
             $filter_cat = Input::get('cat');
        }
        if($filter_cat > 0) {$expanses = $expanses->where('cat', '=', $filter_cat);}
        if($filter_day > 0) {$expanses = $expanses->whereDay('added_at', '=', $filter_day);}
        if($filter_month > 0) {$expanses = $expanses->whereMonth('added_at', '=', $filter_month);}
        if($filter_year > 0) {$expanses = $expanses->whereYear('added_at', '=', $filter_year);}
        $expanses = $expanses->get();
        return view('admin.pages.partners.index')->with(['cats'=>$cats, 'expanses'=>$expanses, 'filter_day'=>$filter_day, 'filter_month'=>$filter_month
        , 'filter_year'=>$filter_year, 'filter_cat'=>$filter_cat, 'total_expanses'=>$total_expanses, 'usd_total_expanses'=>$usd_total_expanses]);
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
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'amount' => 'required|numeric',
            'cat' => 'required'
        ],
        [
            'title.required'=>'Please Enter Expanse Description',
            'amount.required'=>'Please Enter Expanse Required',
            'amount.numeric'=>'Expanse Amount Must Be Number',
            'cat.required'=>'Choose Expanse Category',
        ]);
        if ($validator->fails())
        {
            return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
        }
        else
        {
            
 
            $expanse = new Partner;
            $expanse->title = $request->title;
            $expanse->cat = $request->cat;
            $expanse->amount = $request->amount;
            $expanse->added_by = Auth::guard('admin')->user()->id;
            $expanse->added_at  = $request->date;
            $expanse->save();  
            return response()->json(['success' => true, 'message'=>""]);
        }
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
        $expanse = Partner::findorfail($id);
        if($expanse->hide == 0)
        {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'amount' => 'required|numeric',
                'cat' => 'required'
            ],
            [
                'title.required'=>'Please Enter Expanse Description',
                'amount.required'=>'Please Enter Expanse Required',
                'amount.numeric'=>'Expanse Amount Must Be Number',
                'cat.required'=>'Choose Expanse Category',
            ]);
            if ($validator->fails())
            {
                return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
            }
            else
            {
                $expanse->title  = $request->title;
                $expanse->cat  = $request->cat;
                $expanse->amount  = $request->amount;
                $expanse->added_at  = $request->date;
                $expanse->save();  
                return response()->json(['success' => true, 'message'=>""]);
            }
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
        $cat = Partner::findorfail($id);
        $cat->hide = 1;
        $cat->save();
        return redirect()->route('partners.index'); 
    }

    public function partners_task(Request $request)
    {
        if($request->type == 'Delete')
        {
            for ($i = 0; $i < count($request->items); $i++)
            {
                $id = $request->items[$i];
                $user = Partner::findorfail($id);
                $user->hide = 1;
                $user->save();
            }
        }
    }
}
