<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;

use App\ExpanseCategory;
use App\Expanse;
use App\Admin;
use App\Accounting;

class ExpanseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cats = ExpanseCategory::where('hide', '=', 0)->get();
        $expanses = Expanse::where('hide', '=', 0);

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
        return view('admin.pages.expanses.index')->with(['cats'=>$cats, 'expanses'=>$expanses, 'filter_day'=>$filter_day, 'filter_month'=>$filter_month
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
            // if($request->cat == 2)
            // {
            //     $validator = Validator::make($request->all(), [
            //         'teacher' => 'required'
            //     ],
            //     [
            //         'teacher.required'=>'Please Choose Teacher'
            //     ]);
            //     if ($validator->fails())
            //     {
            //         return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
            //     }
            // }
 
            $expanse = new Expanse;
            $expanse->title = $request->title;
            $expanse->cat = $request->cat;
            $expanse->amount = $request->amount;
            // $expanse->usd_amount  = $request->usd_amount;
            $expanse->added_by = Auth::guard('admin')->user()->id;
            $expanse->added_at  = $request->date;
            // if($request->cat == 2)
            // {
            //     $expanse->teacher = $request->teacher;
            // }
            $expanse->save();  
            // if($request->cat == 2)
            // {
            //     $salary = new Accounting;
            //     $salary->user = $request->teacher;
            //     $salary->amount = $request->amount;
            //     $salary->type = 1;
            //     $salary->note = "Salary";
            //     $salary->transfer = 2;
            //     $salary->transfer_date = date('Y-m-d');
            //     $salary->paid = 1;
            //     $salary->added_by = Auth::guard('admin')->user()->id;
            //     $salary->expanse = $expanse->id;
            //     $salary->created_at  = $request->date." 12:00:00";
            //     $salary->save();
                
            //     $expanse->invoice = $salary->id;
            //     $expanse->save();
                
            //     user_balance($expanse->teacher);
            // }
            return response()->json(['success' => true, 'message'=>""]);
        }
    }

    public function get_teachers(Request $request)
    {
        $teachers = User::where('hide', '=', 0)->where('type', '=', 1)->get();
        ?>
        <div class="form-group">
			<div class="row">
				<div class="col-md-12">	
					<label>Teacher</label>												
					<select class="form-control" required name="teacher" 
					<?php if($request->type == 'add'){ ?>id="teacher_expanses_salary" action="<?php echo url('admin/get_teacher_balance');  ?>" 
					<?php } else { ?>id="teacher"<?php } ?>>
					    <option value="" disabled selected>Choose Teacher</option>   
					    <?php
					    foreach ($teachers as $teacher)
					    {
					        ?>
					        <option value="<?php echo $teacher->id; ?>"><?php echo $teacher->name; ?></option>
                            <?php
					    }
					    ?>
					</select>
				</div>
			</div>
		</div>
        <?php
    }

    public function get_teacher_balance (Request $request)
    {
        $teacher = User::findorfail($request->teacher);
        ?>
        <div class="form-group">
			<div class="row">
				<div class="col-md-12">	
					<label>Amount (EGP)</label>												
					<input class="form-control" type="text" required placeholder="Amount (egp)" name="amount" value="<?php echo $teacher->balance; ?>" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">	
					<label>Amount (USD)</label>												
					<input class="form-control" type="text" required placeholder="Amount (USD)" name="usd_amount" value="0" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-md-12">	
					<label>Date</label>												
					<input class="form-control" type="date" required="" name="date" value="">
				</div>
			</div>
		</div>
		<?php
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
        $expanse = Expanse::findorfail($id);
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
                // if($request->cat == 2)
                // {
                //     $validator = Validator::make($request->all(), [
                //         'teacher' => 'required'
                //     ],
                //     [
                //         'teacher.required'=>'Please Choose Teacher'
                //     ]);
                //     if ($validator->fails())
                //     {
                //         return response()->json(['success' => false, 'errors'=>$validator->errors()->first()]);
                //     }
                // }
                $expanse->title  = $request->title;
                $expanse->cat  = $request->cat;
                $expanse->amount  = $request->amount;
                // $expanse->usd_amount  = $request->usd_amount;
                $expanse->added_at  = $request->date;
                // if($expanse->cat == 2)
                // {
                //     $invoice = Accounting::where('id', '=', $expanse->invoice)->first();
                //     if($invoice !== NULL)
                //     {
                //         $invoice->delete();
                //         user_balance($expanse->teacher);
                //     }
                //     $expanse->invoice = 0;
                // }
                // if($request->cat == 2)
                // {
                //     $expanse->teacher = $request->teacher;
                // }
                $expanse->save();  
                // if($request->cat == 2)
                // {
                //     $salary = new Accounting;
                //     $salary->user = $request->teacher;
                //     $salary->amount = $request->amount;
                //     $salary->type = 1;
                //     $salary->note = "Salary";
                //     $salary->transfer = 2;
                //     $salary->transfer_date = date('Y-m-d');
                //     $salary->paid = 1;
                //     $salary->added_by = Auth::guard('admin')->user()->id;
                //     $salary->expanse = $expanse->id;
                //     $salary->created_at  = $request->date." 12:00:00";
                //     $salary->save();
                    
                //     $expanse->invoice = $salary->id;
                //     $expanse->save();
                    
                //     user_balance($expanse->teacher);
                // }
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
        $cat = Expanse::findorfail($id);
        $cat->hide = 1;
        $cat->save();
        // if($cat->cat == 2)
        // {
        //     $invoice = Accounting::where('id', '=', $cat->invoice)->first();
        //     if($invoice !== NULL)
        //     {
        //         $invoice->delete();
        //         user_balance($cat->teacher);
        //     }
        // }
        return redirect()->route('expanses.index'); 
    }

    public function expanses_task(Request $request)
    {
        if($request->type == 'Delete')
        {
            for ($i = 0; $i < count($request->items); $i++)
            {
                $id = $request->items[$i];
                $user = Expanse::findorfail($id);
                $user->hide = 1;
                $user->save();
                // if($user->cat == 2)
                // {
                //     $invoice = Accounting::where('id', '=', $user->invoice)->first();
                //     if($invoice !== NULL)
                //     {
                //         $invoice->delete();
                //         user_balance($user->teacher);
                //     }
                // }
            }
        }
    }
}
