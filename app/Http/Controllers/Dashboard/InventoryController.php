<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; 


use Validator;
use App\Jobs\UpdateWooCommerceInventory;
use App\Product;
use App\BuyOrderItem;
use App\SellOrderItem;
use App\BuyOrder;
use App\SellOrder;
use App\RuinedItem;
use App\Fulfillment;
use App\Inventory;
use  App\Category;
use App\ProductTimeline;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
        return "Is Under Development By Eng M.Elsdodey";
        $items = array();
        $products_num = array();
        $products = array();
        // 0 & 1 For pending
        // 11 Part. Available	
        // 8 Returned
        
        $total_items = 0;
        $total_prices = 0;
        $orders = SellOrder::where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11, 10, 12])->pluck('id');
        $items_sold = SellOrderItem::whereIn('order', $orders)->get();

        foreach ($items_sold as $item)
        {                
            $itx = $item->product."_".$item->color."_".$item->size;
            if(in_array($itx, $items))
            {
                $in = array_search($itx, $items);
                $products[$in][3] = $products[$in][3] + $item->qty;
            }
            else
            {
                $items[] = $itx;
                $px = "";
                $px = $item->product_info->title;
                if($item->color > 0) {$px .= " - ".$item->color_info->title;}
                if($item->size > 0) {$px .= " - ".$item->size_info->title;}
                $cr = RuinedItem::where('product', '=', $item->product)->where('color', '=', $item->color)->where('size', '=', $item->size)->first();
                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                $zxxch = $item->qty;
                $products[] = array($px, $item->color, $item->size, $zxxch, 0, 0, $item->product, $ruined);
            }
        }

        $borders = BuyOrder::where('hide', '=', 0)->pluck('id');
        $items_bought = BuyOrderItem::whereIn('order', $borders)->get();
        foreach ($items_bought as $item)
        {
            $itx = $item->product."_".$item->color."_".$item->size;
            if(in_array($itx, $items))
            {
                $in = array_search($itx, $items);
                $products[$in][4] = $products[$in][4] + $item->qty;
                $products[$in][5] = $products[$in][5] + ($item->qty * $item->price);
                // $total_items = $total_items + $item->qty;
            }
            else
            {
                $items[] = $itx;
                $px = "";
                $px = $item->product_info->title;
                if($item->color > 0) {$px .= " - ".$item->color_info->title;}
                if($item->size > 0) {$px .= " - ".$item->size_info->title;}
                $cr = RuinedItem::where('product', '=', $item->product)->where('color', '=', $item->color)->where('size', '=', $item->size)->first();
                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                $products[] = array($px, $item->color, $item->size, 0, $item->qty, $item->qty * $item->price, $item->product, $ruined);
                // $total_items = $total_items + $item->qty - $ruined;
            }
        }
       
        
        $total_amount = 0;
        for ($i = 0; $i < count($products); $i++)
        {
            $product = $products[$i][6];
            $color = $products[$i][1];
            $size = $products[$i][2];
            $tot = $products[$i][4] - $products[$i][3] - $products[$i][7];
            if($tot > 0)
            {
                $total_items = $total_items + $tot;
            }
            $ruined_items = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
            if($ruined_items !== NULL) {$ruined = $ruined_items->qty;}else {$ruined = 0;}
            $sold = 0;
            $sold_items = SellOrderItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();
            foreach ($sold_items as $item)
            {
                if($item->order_info->hide == 0 && ($item->order_info->status != 0 && $item->order_info->status != 1 && 
                $item->order_info->status != 8 && $item->order_info->status != 11)) {$sold = $sold + $item->qty;}
                else if($item->order_info->hide == 0 && $item->order_info->status == 11)
                {
                    $xch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->first();
                    if($xch !== NULL)
                    {
                        $xxch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->get()->count();
                        $sold = $sold + $xxch;
                    }
                }
            }
            $bought = 0;
            $amount = 0;
            $bought_items = BuyOrderItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();
            foreach ($bought_items as $item)
            {
                if($item->order_info->hide == 0) {$bought = $bought + $item->qty; $amount = $amount + ($item->qty * $item->price);}
            }
            if($bought != 0)
            {
                $total_amount = $total_amount + ($bought-$sold-$ruined) * ($amount / $bought);
            }
        }

        return view('admin.pages.inventory.index')->with(['products'=>$products, 'total_items'=>$total_items, 'total_prices'=>$total_amount]);
    }
    public function indexv2()
    {  
                return "Is Under Development By Eng M.Elsdodey";

        $total_items = 0;
        $total_prices = 0;
        $total_amount = 0;
        $all_products = Category::where('cat', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $inventory = Inventory::where('id', '>', 0);
        $total = Inventory::where('id', '>', 0);
        $selected_product = array();
        if(Input::get('product'))
        {
            $selected_product = Input::get('product');
            if(count($selected_product) > 0)
            {
                $inventory = $inventory->whereIn('product', $selected_product);
                $total = $total->whereIn('product', $selected_product);
            }
        }
        if(Input::get('perPage'))
        {
            $perPage = Input::get('perPage');
        }
        else
        {
            $perPage = 50;
        }
        if ($perPage == 0) {$perxPage = 999999;}
        else {$perxPage = $perPage;}
        if($perxPage < 1) {$perxPage = 50;}

        $inventory = $inventory->paginate($perxPage);
        $total = $total->get();
        $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11, 10, 12])->pluck('id');
        $sorders_alts = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereIn('status', [0, 1, 11, 10, 12])->pluck('id');
        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
        
        $total_items = BuyOrderItem::whereIn('order', $borders)->sum('qty') - SellOrderItem::whereIn('order', $sorders)->sum('qty') - RuinedItem::sum('qty');
        // $total_bought = 0;
        $data = array();
        $i = 0;
        
        
        foreach ($inventory as $iory)
        {
            $i++;
            $product = $iory->product;
            $color = $iory->color;
            $size = $iory->size;
            get_product_qty_alt($iory->product, $iory->color, $iory->size);

            $inventoryxx = Inventory::where('id', $iory->id)->first();

            // $sold = SellOrderItem::
            //     whereIn('sell_order_items.order', $sorders)
            //     ->where('sell_order_items.product', $product)
            //     ->where('sell_order_items.color', $color)
            //     ->where('sell_order_items.size', $size)
            //     ->select('sell_order_items.*')
            //     ->sum('qty');

            // $xs =  SellOrderItem::
            //     whereIn('sell_order_items.order', $sorders_alts)
            //     ->where('sell_order_items.product', $product)
            //     ->where('sell_order_items.color', $color)
            //     ->where('sell_order_items.size', $size)
            //     ->get();
            // foreach ($xs as $x)
            // {
            //     $sold = $sold + Fulfillment::where('order', '=', $x->order)->where('item', '=', $x->id)->count();
            // }

            // $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');    
            // $iory->sold = $sold;
            // $iory->bought = $bought;
            // $iory->save();
            
            $checkbox = '<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
				<input type="checkbox" class="check_single" name="item[]" value="'.$iory->product.'-'.$iory->color.'-'.$iory->size.'" />
				<span></span>
			</label>';
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
    		$action = '	<div class="dropdown">
    				<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    					Action
    				</button>
    				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    					<a class="dropdown-item" data-toggle="modal" href="#myModal-'.$iory->id.'">Ruined Items</a>
    				</div>
				</div>
				<div class="modal fade" id="myModal-'.$iory->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Ruined Items</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
							<form role="form" action="'.url('inventory_ruined_items').'" class="inventory_ruined_items_form" method="POST" 
								data-num="'.$iory->product.'_'.$iory->color.'_'.$iory->size.'">
								'.csrf_field().'
								<div id="inventory_ruined_items_form_res'.$iory->product.'_'.$iory->color.'_'.$iory->size.'"></div>
								<input type="hidden" name="color" value="'.$iory->color.'" />
								<input type="hidden" name="size" value="'.$iory->size.'" />
								<input type="hidden" name="product" value="'.$iory->product.'" />
								<div class="form-group">
								    <input type="number" name="ruined_item" class="form-control" value="'.$iory->ruined_qty().'" />
								</div>
                                '.$yyu.'
								<button type="submit" class="btn btn-danger" name="save">Save</button>
								<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
								</form>
								</div>
								</div>
								</div>
								</div>';
			$total_amount_bought = BuyOrderItem::select(DB::raw("(qty * price) as total"))->whereIn('order', $borders)->where('product', $product)->where('color', $color)->
            where('size', $size)->get()->sum('total');
    		if($iory->bought != 0) {$avg_total_amount_bought = $total_amount_bought / $iory->bought;}
    		else {$avg_total_amount_bought = 0;}
            $data[] = array("CheckBox"=>$checkbox, "ID"=>$i, "Product"=>$productnm, "Sold"=>$iory->sold, "Bought"=>$iory->bought, 
            "Qty"=>$iory->bought - $iory->sold - $iory->ruined_qty(), "AvgPrice"=>number_format($avg_total_amount_bought, 2), "Action"=>$action);
        }
        return view('admin.pages.inventory.index2')->with(['products'=>$inventory, 'dproducts'=>$data, 'total_items'=>$total_items, 'total_prices'=>$total_amount,
        'all_products'=>$all_products, 'selected_product'=>$selected_product, 'perPage'=>$perPage]);
    }
    
    public function inventory_total_amounts (Request $request)
    {
        $total_items = 0;
        $total_prices = 0;
        $total_amount = 0;
        $all_products = Category::where('cat', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $total = Inventory::where('id', '>', 0)->where(function($q) {$q->where('sold', '>', 0)->orWhere('bought', '>', 0);});
        $selected_product = array();
        if(Input::get('selected_product'))
        {
            $selected_product = explode(',', $request->selected_product);
            if(count($selected_product) > 0 && $selected_product[0] != '')
            {
                $total = $total->whereIn('product', $selected_product);
            }
        }
        $total = $total->get();
        $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11, 10, 12])->pluck('id');
        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
        
        foreach ($total as $iory)
        {
            $product = $iory->product;
            $color = $iory->color;
            $size = $iory->size;
            $bought = $iory->bought;
            $sold = $iory->sold;
            $ruined = RuinedItem::where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');
            $total_amount_bought = BuyOrderItem::select(DB::raw("(qty * price) as total"))->whereIn('order', $borders)->where('product', $product)->where('color', $color)->
            where('size', $size)->get()->sum('total');
            if($bought != 0) {$avg_total_amount_bought = $total_amount_bought / $bought;}
    		else {$avg_total_amount_bought = 0;}
    		
            $total_amount = $total_amount + (($bought - $sold - $ruined) * $avg_total_amount_bought);
        }
        return number_format($total_amount, 2)." EGP";
    }
    
    public function inventory_data (Request $request)
    {
        $page = 1;
        $perpage = $request->length;
        $start = 0;
        if($perpage > 0)
        {
            if($request->has('start')) {$start = $request->start; $page = $request->start / $perpage;}
        }
        else
        {
            $start = 0;
            $perpage = Inventory::count();
        }
        if($page < 1) {$page = 1;}
        $totalpages = ceil(Inventory::count() / $perpage);
        
        $data = array();
        $inventory = Inventory::where('id', '>', 0)->where(function($q) {$q->where('sold', '>', 0)->orWhere('bought', '>', 0);});
        $total =  Inventory::where('id', '>', 0)->where(function($q) {$q->where('sold', '>', 0)->orWhere('bought', '>', 0);});
        $selected_product = array();
        if($request->has('selected_product'))
        {
            $selected_product = explode(',', $request->selected_product);
            if(count($selected_product) > 0 && $selected_product[0] != '')
            {
                $total = $total->whereIn('product', $selected_product);
                $inventory = $inventory->whereIn('product', $selected_product);
            }
        }
        $total = $total->count();

        $inventory = $inventory->get();

        $meta = array("field"=>"ID", "page"=>$page, "_iRecordsTotal"=>ceil($total / $perpage), "iDisplayLength"=>intval($perpage), "iTotalRecords"=>$total, 
        "recordsFiltered"=>$total, "sort"=>'asc', 'recordsDisplay'=>intval($perpage));
        
        $sorders = SellOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11, 10, 12])->pluck('id');
        $borders = BuyOrder::where('created_at', '>', '2022-04-03 11:00:00')->where('hide', '=', 0)->pluck('id');
        
        $total_items = Inventory::sum('bought') - Inventory::sum('sold') - RuinedItem::sum('qty');
        $i = 0;
        foreach ($inventory as $iory)
        {
            $i++;
            $product = $iory->product;
            $color = $iory->color;
            $size = $iory->size;

            $sold = SellOrderItem::
                whereIn('sell_order_items.order', $sorders)
                ->where('sell_order_items.product', $product)
                ->where('sell_order_items.color', $color)
                ->where('sell_order_items.size', $size)
                ->select('sell_order_items.*')
                ->sum('qty');
            
            $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $product)->where('color', $color)->where('size', $size)->sum('qty');    
            $iory->sold = $sold;
            $iory->bought = $bought;
            $iory->save();
            
            $checkbox = '<label class="kt-checkbox kt-checkbox--bold  kt-checkbox--primary">
				<input type="checkbox" class="check_single" name="item[]" value="'.$iory->product.'-'.$iory->color.'-'.$iory->size.'" />
				<span></span>
			</label>';
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
    		$action = '	<div class="dropdown">
    				<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    					Action
    				</button>
    				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    					<a class="dropdown-item" data-toggle="modal" href="#myModal-'.$iory->id.'">Ruined Items</a>
    				</div>
				</div>
				<div class="modal fade" id="myModal-'.$iory->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Ruined Items</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
							<form role="form" action="'.url('inventory_ruined_items').'" class="inventory_ruined_items_form" method="POST" 
								data-num="'.$iory->product.'_'.$iory->color.'_'.$iory->size.'">
								'.csrf_field().'
								<div id="inventory_ruined_items_form_res'.$iory->product.'_'.$iory->color.'_'.$iory->size.'"></div>
								<input type="hidden" name="color" value="'.$iory->color.'" />
								<input type="hidden" name="size" value="'.$iory->size.'" />
								<input type="hidden" name="product" value="'.$iory->product.'" />
								<div class="form-group">
								    <input type="number" name="ruined_item" class="form-control" value="'.$iory->ruined_qty().'" />
								</div>
                                '.$yyu.'
								<button type="submit" class="btn btn-danger" name="save">Save</button>
								<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
								</form>
								</div>
								</div>
								</div>
								</div>';
			$total_amount_bought = BuyOrderItem::select(DB::raw("(qty * price) as total"))->whereIn('order', $borders)->where('product', $product)->where('color', $color)->
            where('size', $size)->get()->sum('total');
    		if($bought != 0) {$avg_total_amount_bought = $total_amount_bought / $bought;}
    		else {$avg_total_amount_bought = 0;}
            $data[] = array("CheckBox"=>$checkbox, "ID"=>$i, "Product"=>$productnm, "Sold"=>$iory->sold, "Bought"=>$iory->bought, 
            "Qty"=>$iory->bought - $iory->sold - $iory->ruined_qty(), "AvgPrice"=>number_format($avg_total_amount_bought, 2), "Action"=>$action);
        }
        $result = ["meta"=>$meta, "data"=>$data];
        return response()->json($result);
    }
    
    public function ruined_items(Request $request)
    {
        if($request->ruined_item > -1)
        {
            $product = (int)$request->product;
            $color = (int)$request->color;
            $size = (int)$request->size;
            $cr = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
            if($cr !== NULL)
            {
                $cr->qty = $request->ruined_item;
                $cr->save();

                
                $time_line = new ProductTimeline;
                $time_line->product = $product;
                $time_line->color = $color;
                $time_line->size = $size;
                $time_line->admin = Auth::guard('admin')->user()->id;
                $time_line->order = 0;
                $time_line->order_type = 0;
                $time_line->qty = get_product_qty_alt($product, $color, $size);
                $time_line->text = " Has Update Ruined Items Qty To (".$request->ruined_item.") Pieces";
                $time_line->save();
            }
            else
            {
                $cr = new RuinedItem;
                $cr->product = $product;
                $cr->color = $color;
                $cr->size = $size;
                $cr->qty = $request->ruined_item;
                $cr->added_by = Auth::guard('admin')->user()->id;
                $cr->save();

                $time_line = new ProductTimeline;
                $time_line->product = $product;
                $time_line->color = $color;
                $time_line->size = $size;
                $time_line->admin = Auth::guard('admin')->user()->id;
                $time_line->order = 0;
                $time_line->order_type = 0;
                $time_line->qty = get_product_qty_alt($product, $color, $size);
                $time_line->text = " Has Update Ruined Items Qty To (".$request->ruined_item.") Pieces";
                $time_line->save();
            }
        }
        return response()->json(['success' => true, 'message'=>'Saved Successfully']);
    }

    public function task_calc (Request $request)
    {
        if($request->type == "CalculateTotalAmount")
        {
            $total_amount = 0;
            if($request->items)
            {
                for ($i = 0; $i < count($request->items); $i++)
                {
                    $item = explode('-', $request->items[$i]);
                    $product = $item[0];
                    $color = $item[1];
                    $size = $item[2];
                    $ruined_items = RuinedItem::where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->first();
                    if($ruined_items !== NULL) {$ruined = $ruined_items->qty;}else {$ruined = 0;}
                    $sold = 0;
                    $sold_items = SellOrderItem::where('created_at', '>', '2022-04-03 11:00:00')->where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();
                    foreach ($sold_items as $item)
                    {
                        if($item->order_info->hide == 0 && ($item->order_info->status != 0 && $item->order_info->status != 1 && 
                        $item->order_info->status != 8)) {$sold = $sold + $item->qty;}
                        // else if($item->order_info->hide == 0 && $item->order_info->status == 11)
                        // {
                        //     $xch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->first();
                        //     if($xch !== NULL)
                        //     {
                        //         $xxch = Fulfillment::where('order', '=', $item->order_info->id)->where('item', '=', $item->id)->get()->count();
                        //         $sold = $sold + $xxch;
                        //     }
                        // }
                    }
                    $bought = 0;
                    $amount = 0;
                    $bought_items = BuyOrderItem::where('created_at', '>', '2022-04-03 11:00:00')->where('product', '=', $product)->where('color', '=', $color)->where('size', '=', $size)->where('qty', '!=', 0)->get();
                    foreach ($bought_items as $item)
                    {
                        if($item->order_info->hide == 0) {$bought = $bought + $item->qty; $amount = $amount + ($item->qty * $item->price);}
                    }
                    if($bought != 0)
                    {
                        $total_amount = $total_amount + ($bought-$sold-$ruined) * ($amount / $bought);
                    }
                }
                echo "<p style='font-size:16px;'><b>Total Amount : </b>".number_format($total_amount, 2)." EGP</p>";
            }
            else
            {
                 echo "<p style='font-size:16px; text-align:center;'>No Selected Items</p>";
            }
        }
    }




    
}
