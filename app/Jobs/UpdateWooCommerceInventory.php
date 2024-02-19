<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Inventory;
use App\SellOrder;
use App\SellOrderItem;
use App\Fulfillment;
use App\BuyOrderItem;
use App\BuyOrder;

class UpdateWooCommerceInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ShouldQueue;

    /**
     * Create a new job instance.
     *
     * @return void1
     */

    public function __construct()
    {
       
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $inventory = Inventory::get();
        $data = array();
        $i = 0;        
        $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11])->pluck('id');
        $sorders_alts = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereIn('status', [0, 1, 11])->pluck('id');
        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');

        foreach ($inventory as $iory)
        {
            $i++;
            $product = $iory->product;
            $color = $iory->color;
            $size = $iory->size;
            get_product_qty($iory->product, $iory->color, $iory->size);
            $sold = SellOrderItem::
                whereIn('sell_order_items.order', $sorders)
                ->where('sell_order_items.product', $product)
                ->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)
                ->select('sell_order_items.*')
                ->sum('qty');

            $xs =  SellOrderItem::
                whereIn('sell_order_items.order', $sorders_alts)
                ->where('sell_order_items.product', $product)
                ->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)
                ->get();
            foreach ($xs as $x)
            {
                $sold = $sold + Fulfillment::where('order', '=', $x->order)->where('item', '=', $x->id)->count();
            }

            $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');    
            $iory->sold = $sold;
            $iory->bought = $bought;
            $iory->save();
            
			$productnm = $iory->product_info->title;
			if($iory->color > 0)
			{
    			$productnm .= " - ".$iory->color_info->title;
    		}
    		if($iory->size > 0) 
    		{
    		    	$productnm .= " - ".$iory->size_info->title;
    		}
    		$yyu = ruinded_items_admn($iory->product, $iory->color, $iory->size);
			$total_amount_bought = BuyOrderItem::select(DB::raw("(qty * price) as total"))->whereIn('order', $borders)->where('product', $product)->where('color', $color)->
            where('size', $size)->get()->sum('total');
    		if($bought != 0) {$avg_total_amount_bought = $total_amount_bought / $bought;}
    		else {$avg_total_amount_bought = 0;}
        }
    }
}
