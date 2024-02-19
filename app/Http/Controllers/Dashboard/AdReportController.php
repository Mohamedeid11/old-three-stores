<?php

namespace App\Http\Controllers\Dashboard;

use App\Ad;
use App\AdPlatform;
use App\AdProduct;
use App\SellOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $toDate = date('Y-m-d');
        $specialDate = Carbon::parse($toDate);
        $fromDate = $specialDate->subDays(7)->toDateString();

        if ($request->fromDate) {
            $fromDate = $request->fromDate;
        }

        if ($request->toDate) {
            $toDate = $request->toDate;
        }

        $rows = collect();

        for ($date = Carbon::parse($fromDate); $date->lte(Carbon::parse($toDate)); $date->addDay()) {

            $date_in_format = $date->toDateString();
            $ads = Ad::where('parent_id', null)->where('date', $date_in_format)->get();
            $result = $total1 = $cost_per_result = $order =$order_value=$product_buy= 0;

            foreach ($ads as $ad) {
                $ad_order_value=0;
                 $child_ad_total=Ad::where('parent_id',$ad->id)->sum(DB::raw('result * cost_per_result'));
                $child_ad_result=\App\Ad::where('parent_id',$ad->id)->sum('result');

                if ($ad->result == 0) {

                    $total1 = $total1 + $ad->cost_per_result + $child_ad_total;
                    $cost_per_result=$cost_per_result+ $ad->cost_per_result + $child_ad_total;
                } else {
                    $total1 = $total1 + $ad->cost_per_result*$ad->result + $child_ad_total;
                    $cost_per_result=$cost_per_result+(($ad->cost_per_result*$ad->result + $child_ad_total)/($ad->result+$child_ad_result));
                }
                $result=$result+$ad->result+$child_ad_result;
                $ad_products_ides=AdProduct::where('ad_id',$ad->id)->pluck('product_id')->toArray();
                $ad_platform_ides=AdPlatform::where('ad_id',$ad->id)->pluck('platform_id')->toArray();
                $order=$order+SellOrder::whereHas('items',function ($query) use ($ad_products_ides){
                   $query->whereIn('sell_order_items.product',$ad_products_ides);
                })->whereHas('tags',function ($query) use ($ad_platform_ides){
                    $query->whereIn('sell_order_tags.tag_id',$ad_platform_ides);

                })
                    ->where('created_at', '>=', $ad->date . ' 00:00:00')
                    ->where('created_at', '<=', $ad->date . ' 23:59:59')
                    ->where('hide', 0)
                    ->count();


                $return_data=$this->calculateOrderValue($ad->products, $ad->date,$ad_platform_ides);
                $order_value=$order_value+$return_data['order_value'];
                $product_buy=$product_buy+$return_data['ad_product_buy'];


            }

            $rows->push([
                'date' => $date_in_format,
                'total1' => $total1,
                'result'=>$result,
                'cost_per_result'=>$cost_per_result,
                'order'=>$order,
                'order_value' =>$order_value ,
                'total2'=>$order_value,
                'product_buy'=>$product_buy,

            ]);
        }
        return view('admin.reports.ads', compact('fromDate', 'toDate','rows'));
    }

    private function calculateOrderValue($products, $adDate, $platform_ides)
    {
        $orderValue = 0;
        $ordersNummbers = 0;
        $ad_product_buy = 0;

        // Eager load the necessary relationships
        $products->load(['sell_orders.tags', 'sell_orders.items']);

        foreach ($products as $product) {
            foreach ($product->sell_orders ?? [] as $order) {
                if (
                    $order->created_at <= $adDate . ' 23:59:59' &&
                    $order->created_at >= $adDate . ' 00:00:00' &&
                    $order->hide == 0 &&
                    $order->tags->contains('tag_id', function ($tag) use ($platform_ides) {
                        return in_array($tag->tag_id, $platform_ides);
                    })
                ) {
                    $ordersNummbers++;
                    $orderValue += $order->total_price ?? 0;

                    // Sum the product_buy directly using database queries
                    $product_buy = \App\BuyOrderItem::whereHas('sellOrderItem', function ($query) use ($order) {
                        $query->where('order', $order->id)->where('hide', 0);
                    })
                        ->where('qty', '>', 0)
                        ->where('hide', 0)
                        ->where('price', '>', 5)
                        ->where('created_at', '<=', $order->created_at)
                        ->sum(DB::raw('price * qty'));
                    $ad_product_buy += $product_buy;

                    $orderValue += $product_buy;
                }
            }
        }

        $averageOrderValue = ($ordersNummbers > 0) ? $orderValue / $ordersNummbers : $orderValue;

        return ['order_value' => $averageOrderValue, 'ad_product_buy' => $ad_product_buy];
    }
}
