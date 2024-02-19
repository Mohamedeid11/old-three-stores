<?php

namespace App\Http\Controllers\Dashboard;

use App\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActiveAdController extends Controller
{
    //
    public function index(){
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_active_ads')) {abort(404);}

        $ads = Ad::select('ads.*')
            ->join(DB::raw('(SELECT ad_number, MAX(date) as latest_date FROM ads GROUP BY ad_number) latest_ads'), function ($join) {
                $join->on('ads.ad_number', '=', 'latest_ads.ad_number');
                $join->on('ads.date', '=', 'latest_ads.latest_date');
            })
            ->where('ads.status', 1)
            ->whereNull('ads.parent_id')
            ->with(['products', 'platforms'])
            ->get();




        return view('admin.pages.ads.active',compact('ads'));
    }
}
