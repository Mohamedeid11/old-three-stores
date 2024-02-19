<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule; 

use Validator;

use App\BuyOrder;
use App\BuyOrderItem;

use App\SellOrder;
use App\SellOrderItem;

use App\ExpanseCategory;
use App\Expanse;
use App\PartnerCategory;
use App\Partner;

class AccountingController extends Controller
{
    public function index()
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Profit & Loss')) {abort(404);}
        $orders = BuyOrder::where('hide', 0);
        $selected_year = date('Y');

        if(Input::get('year'))
        {
            $selected_year = Input::get('year');
            if($selected_year > 0)
            {
                $orders = $orders->whereYear('shipping_date', $selected_year);
            }
        }
        $cats = ExpanseCategory::where('hide', 0)->get();
        $partners = PartnerCategory::where('hide', 0)->get();
        return view('admin.pages.accounting.index')->with(['selected_year'=>$selected_year, 'cats'=>$cats,  'partners'=>$partners]);
    }
    
    function monthly_report(Request $request)
    {
        if(!permission_group_checker(Auth::guard('admin')->user()->id, 'Profit & Loss')) {abort(404);}
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        if($request->has('from_date') && $request->from_date) {$from_date = $request->from_date;}
        if($request->has('to_date') && $request->to_date) {$to_date = $request->to_date;}

        $incomes = SellOrder::where('hide', 0)->where('status', 6);
        $boughts = BuyOrder::where('hide', 0);
        $expanses = Expanse::where('hide', 0);
        $partners = Partner::where('hide', 0);

        $incomes = $incomes->whereDate('collected_date', '>=', $from_date)->whereDate('collected_date', '<=', $to_date);
        $boughts = $boughts->whereDate('shipping_date', '>=', $from_date)->whereDate('shipping_date', '<=', $to_date);
        $expanses = $expanses->whereDate('added_at', '>=', $from_date)->whereDate('added_at', '<=', $to_date);
        $partners = $partners->whereDate('added_at', '>=', $from_date)->whereDate('added_at', '<=', $to_date);

        $incomes = $incomes->orderBy('delivered_by', 'asc')->orderBy('collected_date', 'desc')->get();
        $total_income = 0;
        $total_outcome = 0;
        $boughts = $boughts->orderBy('created_at')->get();
        $total_expanses = 0;
        $total_partners = 0;

        $expanses = $expanses->orderBy('added_at')->get();
        $partners = $partners->orderBy('added_at')->get();
        return view('admin.pages.accounting.monthly_report')->with(['incomes'=>$incomes, 'total_income'=>$total_income , 'boughts'=>$boughts, 
        'expanses'=>$expanses, 'total_expanses'=>$total_expanses, 'partners'=>$partners, 'total_partners'=>$total_partners, 
        'total_outcome'=>$total_outcome, 'from_date'=>$from_date, 'to_date'=>$to_date]);
    }
}
