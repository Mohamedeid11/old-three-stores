<?php

namespace App\Http\Controllers\Dashboard;

use App\Ad;
use App\AdPlatform;
use App\AdProduct;
use App\OrderTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\AdsExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AdsImport;

class AdController extends Controller
{
    //


    public function index(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_ads')) {abort(404);}


        $status = 1;
        if ($request->status) {
            if ($request->status == -1)
                $status = -1;
            if ($request->status == 'all')
                $status = 'all';
        }
        $rows = Ad::query()->where('parent_id',null);

        if ($status != 'all') {
            if ($status == -1)
                $rows->where('status', 0);
            else
                $rows->where('status', 1);

        }

        if ($request->fromDate) {
            $rows->where('date', '>=', $request->fromDate);
        } else {
            $rows->where('date', '>=', date("Y-m-d", strtotime("yesterday")));

        }
        if ($request->toDate) {
            $rows->where('date', '<=', $request->toDate);
        } else {
            $rows->where('date', '<=', date('Y-m-d'));

        }
        if ($request->product_id) {
            $rows->whereHas('products', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            });
        }
        if ($request->platform_id) {

            $rows->whereHas('platforms', function ($query) use ($request) {
                $query->where('platform_id', $request->platform_id);
            });
        }
        if ($request->tag_id) {
            $rows->whereHas('products.tags', function ($query) use ($request) {
                $query->where('tag_id', $request->tag_id);
            });
        }
        $ads = $rows->with(['products' => function ($query) {
            $query->orderBy('id', 'asc');
        }])
            ->select('*', \DB::raw('result * cost_per_result as total'))
            ->orderBy('date')
            ->paginate(50);
        $platforms = OrderTag::where('is_platform', 1)->get();

        $queryParameters = $request->query();



        return view('admin.pages.ads.index', compact('ads', 'request', 'status', 'platforms','queryParameters'));
    }

    public function create()
    {

        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_ads')) {abort(404);}


        $platforms = OrderTag::where('is_platform', 1)->get();
        return view('admin.pages.ads.parts.create', compact('platforms'));
    }

    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_ads')) {abort(404);}

        $data = $request->validate([

            'date' => 'required|date',
            'ad_number' => 'required',
            'result' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'cost_per_result' => 'required',
            'status' => 'required|in:0,1',

        ]);

        $request->validate([

            'product_id' => 'required|array',
            'product_id.*' => 'required',

            'platform_id' => 'required|array',
            'platform_id.*' => 'required',
        ]);

        $ad = Ad::where('date', $request->date)->where('ad_number', $request->ad_number)->first();

        if ($ad) {
            $ad->update($data);
            $ad = Ad::where('date', $request->date)->where('ad_number', $request->ad_number)->first();
            AdProduct::where('ad_id', $ad->id)->delete();
            AdPlatform::where('ad_id', $ad->id)->delete();

        } else {
            $ad = Ad::create($data);

        }


        if ($request->product_id) {
            foreach ($request->product_id as $product_id)
                AdProduct::create([
                    'ad_id' => $ad->id,
                    'product_id' => $product_id,

                ]);
        }

        $ad = Ad::where('date', $request->date)->where('ad_number', $request->ad_number)->first();


        if ($request->platform_id) {
            foreach ($request->platform_id as $platform_id)
                AdPlatform::create([
                    'ad_id' => $ad->id,
                    'platform_id' => $platform_id,

                ]);
        }


        $parents = Ad::where('id', '!=', $ad->id)
            ->where('date', $ad->date)
            ->where('parent_id', null)
            ->get();
        foreach ($parents as $parent) {
            if ($parent) {
                // Get the product IDs of the current ad
                $adProductIds = $ad->products->pluck('id')->toArray();
                // Get the product IDs of the parent ad
                $parentProductIds = $parent->products->pluck('id')->toArray();
                // Check if the product IDs are the same

                $adPlatformIds=$ad->platforms->pluck('id')->toArray();
                $parentPlatformIds = $parent->platforms->pluck('id')->toArray();

                if (count($adProductIds) == count($parentProductIds) && empty(array_diff($adProductIds, $parentProductIds)) && count($adPlatformIds) == count($parentPlatformIds) && empty(array_diff($adPlatformIds, $parentPlatformIds))) {
                    $ad->parent_id = $parent->id;
                    $ad->save();
                    break;
                }
            }
        }
        $platforms = OrderTag::where('is_platform', 1)->get();

//        $row = view('admin.pages.ads.parts.row', compact('ad', 'platforms'))->render();
        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!',
//                'row' => $row,
                'id' => $ad->id,
            ]);

    }

    public function edit($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_ads')) {abort(404);}

        $row = Ad::findOrFail($id);
        $platforms = OrderTag::where('is_platform', 1)->get();
        return view('admin.pages.ads.parts.edit', compact('platforms', 'row'));
    }

    public function update($id, Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_ads')) {abort(404);}

        $data = $request->validate([

            'date' => 'required|date',
            'ad_number' => 'required',
            'result' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'cost_per_result' => 'required',
            'status' => 'required|in:0,1',

        ]);

        $request->validate([

            'product_id' => 'required|array',
            'product_id.*' => 'required',

            'platform_id' => 'required|array',
            'platform_id.*' => 'required',
        ]);

        $data['parent_id']=null;


        Ad::findOrFail($id)->update($data);
        $ad = Ad::findOrFail($id);



        AdPlatform::where('ad_id', $ad->id)->delete();
        if ($request->platform_id) {
            foreach ($request->platform_id as $platform_id)
                AdPlatform::create([
                    'ad_id' => $ad->id,
                    'platform_id' => $platform_id,

                ]);
        }




        AdProduct::where('ad_id', $ad->id)->delete();
        if ($request->product_id) {
            foreach ($request->product_id as $product_id)
                AdProduct::create([
                    'ad_id' => $ad->id,
                    'product_id' => $product_id,

                ]);
        }




        $parents = Ad::where('id', '!=', $ad->id)
            ->where('date', $ad->date)
            ->where('parent_id', null)
            ->get();
        foreach ($parents as $parent) {
            if ($parent) {
                // Get the product IDs of the current ad
                $adProductIds = $ad->products->pluck('id')->toArray();
                // Get the product IDs of the parent ad
                $parentProductIds = $parent->products->pluck('id')->toArray();
                // Check if the product IDs are the same

                $adPlatformIds=$ad->platforms->pluck('id')->toArray();
                $parentPlatformIds = $parent->platforms->pluck('id')->toArray();

                if (count($adProductIds) == count($parentProductIds) && empty(array_diff($adProductIds, $parentProductIds)) && count($adPlatformIds) == count($parentPlatformIds) && empty(array_diff($adPlatformIds, $parentPlatformIds))) {
                    $ad->parent_id = $parent->id;
                    $ad->save();
                    break;
                }
            }
        }

        $platforms = OrderTag::where('is_platform', 1)->get();

//        $row = view('admin.pages.ads.parts.row', compact('ad', 'platforms'))->render();

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!',
//                'row' => $row,
                'id' => $id,
            ]);


    }

    public function destroy($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_ads')) {abort(404);}

        Ad::where('parent_id',$id)->delete();
        $row = Ad::findOrFail($id)->delete();

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);

    }

    public function changeAdStatus(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_ads')) {abort(404);}

        $ad = Ad::findOrFail($request->ad_id);
        $ad->status = $request->status;
        $ad->save();
        return response()->json(['status' => true]);
    }

    public function exportAds()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_ads')) {abort(404);}

        return Excel::download(new AdsExport, 'ads.xlsx');
    }

    public function importAds()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_ads')) {abort(404);}

        return view('admin.pages.ads.parts.import');

    }

    public function importAdsStore(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'add_ads')) {abort(404);}

        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        Excel::import(new AdsImport, $file);

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }

    public function getPlatforms(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('order_tags')->select('id', 'title as text')
                ->where('title', 'LIKE', '%' . $term . '%')
                ->where('is_platform', 1)
                ->orderBy('title', 'asc')->simplePaginate(6);

            $morePages = true;
            $pagination_obj = json_encode($posts);
            if (empty($posts->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $posts->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return \Response::json($results);

        }

    }


}
