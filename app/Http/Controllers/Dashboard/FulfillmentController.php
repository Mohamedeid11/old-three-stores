<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule; 

use Validator;

use App\SellOrder;
use App\SellOrderItem;
use App\OrderStatus;
use App\Fulfillment;
use App\TimeLine;
use App\ProductTimeline;
use App\Inventory;

class FulfillmentController extends Controller
{
    public function index()
    {
        $selected_status = "All";
        if(Input::get('status'))
        {
            $selected_status = Input::get('status');
        }
        
        if($selected_status == 'All')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1, 2, 11))->orderBy('city')->get();
        }
        else if($selected_status == 'Pending')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1))->orderBy('city')->get();
        }
        else if($selected_status == 'Partly-Available')
        {
            $orders = SellOrder::where('hide', '=', 0)->where('status', 11)->orderBy('city')->get();
        }
        else if($selected_status == 'Not-Available')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1, 11))->orderBy('city')->get();
        }
        else
        {
            $orders = SellOrder::where('id', '=', 0)->get();
        }
        return view('admin.pages.fulfillment.index')->with(['orders'=>$orders, 'selected_status'=>$selected_status]);
    }

    public function avilable_items (Request $request)
    {
        $item_info = SellOrderItem::findorfail($request->item);
        $order = $item_info->order;
        $xch = Fulfillment::where('order', '=', $order)->where('item', '=', $item_info->id)->where('item_index', '=', $request->item_index)->first();
        if($xch === NULL)
        {
            $xch = new Fulfillment;
            $xch->order = $order;
            $xch->item = $item_info->id;
            $xch->item_index = $request->item_index;
            $xch->save();

            $oitem = SellOrderItem::where('id', $xch->item)->first();
//            if($item_info->qty > 0)
//            {
//                $time_line = new ProductTimeline;
//                $time_line->product = $item_info->product;
//                $time_line->color = $item_info->color;
//                $time_line->size = $item_info->size;
//                $time_line->admin = Auth::guard('admin')->user()->id;
//                $time_line->order = $item_info->order;
//                $time_line->order_type = 1;
//                $time_line->qty = get_product_qty_alt($item_info->product, $item_info->color, $item_info->size);
//                $time_line->text = " Booked (1) Piece From Inventory To Selling Order";
//                $time_line->save();
//            }
        }
        else
        {
            $xch->delete();

            $oitem = SellOrderItem::where('id', $xch->item)->first();

//            if($item_info->qty > 0)
//            {
//                $time_line = new ProductTimeline;
//                $time_line->product = $item_info->product;
//                $time_line->color = $item_info->color;
//                $time_line->size = $item_info->size;
//                $time_line->admin = Auth::guard('admin')->user()->id;
//                $time_line->order = $item_info->order;
//                $time_line->order_type = 1;
//                $time_line->qty = get_product_qty_alt($item_info->product, $item_info->color, $item_info->size);
//                $time_line->text = " Returned (1) Pieces To Inventory From Selling Order";
//                $time_line->save();
//            }
        }

        $order_items = SellOrderItem::where('order', '=', $order)->where('qty', '!=', 0)->get();
        $order_items_qty = 0;
        $avilable_items_qty = 0;
        $order_info = SellOrder::findorfail($order);
        $old_status_id = $order_info->status;
        foreach ($order_items as $od)
        {
            $order_items_qty = $order_items_qty + abs($od->qty);
            $xch = Fulfillment::where('order', '=', $order)->where('item', '=', $od->id)->first();
            if($xch !== NULL)
            {
                $xach = Fulfillment::where('order', '=', $order)->where('item', '=', $od->id)->get()->count();
                $avilable_items_qty = $avilable_items_qty + $xach;
            }
        }
        if($avilable_items_qty == $order_items_qty)
        {
            if($order_info->status == 0)
            {
                $old_status = "Pending";    
            }
            else
            {
                $old_status = OrderStatus::findorfail($order_info->status);
                $old_status = $old_status->title;
            }
           
            $new_status_i = OrderStatus::findorfail(2);
            $new_status = $new_status_i->title;
            
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order_info->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From ".$old_status. " To ".$new_status;
            $event->save();
            $order_info->status = 2;
            $order_info->save();
        }
        else if($avilable_items_qty < $order_items_qty && $avilable_items_qty > 0)
        {
            if($order_info->status != 11)
            {
                if($order_info->status == 0)
                {
                    $old_status = "Pending";    
                }
                else
                {
                    $old_status = OrderStatus::findorfail($order_info->status);
                    $old_status = $old_status->title;
                }
                $new_status_i = OrderStatus::findorfail(11);
                $new_status = $new_status_i->title;
                
                $event = new TimeLine;
                $event->admin = Auth::guard('admin')->user()->id;
                $event->order = $order_info->id;
                $event->order_type = 1;
                $event->text = " Has Changed Selling Order Status From ".$old_status. " To ".$new_status;
                $event->save();
            

                $order_info->status = 11;
                $order_info->save();
            }
        }
        else if($avilable_items_qty == 0)
        {
            if($order_info->status == 0)
            {
                $old_status = "Pending";    
            }
            else
            {
                $old_status = OrderStatus::findorfail($order_info->status);
                $old_status = $old_status->title;
            }
            $new_status_i = OrderStatus::findorfail(1);
            $new_status = $new_status_i->title;
            
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order_info->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From ".$old_status. " To ".$new_status;
            $event->save();
            
            
            if($order_info->status != 0 && $order_info->status != 1)
            {
                $order_info->status = 1;
                $order_info->save();
            }


        }
        
        // $nn = array(0, 1, 8);
        // if((in_array($old_status_id, $nn) && in_array($new_status_i->id, $nn)) || (!in_array($old_status_id, $nn) && !in_array($new_status_i->id, $nn)))
        // {
            
        // }
        // elseif(!in_array($old_status_id, $nn) && in_array($new_status_i->id, $nn))
        // {
        //     foreach ($order_info->itemsq as $item)
        //     {
        //         $inventory = Inventory::where('product', $item->product)->where('color', $item->color)->where('size', $item->size)->first();
        //         if($inventory === NULL)
        //         {
        //             $inventory = new Inventory;
        //             $inventory->product = $item->product;
        //             $inventory->color = $item->color;
        //             $inventory->size = $item->size;
        //             $inventory->sold = -1 * $item->qty;
        //             $inventory->save();
        //         }
        //         else
        //         {
        //             $inventory->sold = $inventory->sold - $item->qty;
        //             $inventory->save();                
        //         }
        //     }
        // }
        // elseif(in_array($old_status_id, $nn) && !in_array($new_status_i->id, $nn))
        // {
        //     foreach ($order_info->itemsq as $item)
        //     {
        //         $inventory = Inventory::where('product', $item->product)->where('color', $item->color)->where('size', $item->size)->first();
        //         if($inventory === NULL)
        //         {
        //             $inventory = new Inventory;
        //             $inventory->product = $item->product;
        //             $inventory->color = $item->color;
        //             $inventory->size = $item->size;
        //             $inventory->sold = $item->qty;
        //             $inventory->save();
        //         }
        //         else
        //         {
        //             $inventory->sold = $inventory->sold + $item->qty;
        //             $inventory->save();                
        //         }
        //     }
        // }
//        get_product_qty ($item_info->product, $item_info->color, $item_info->size);

        return response()->json(['success' => true, 'message'=>"Success"]);
    }
    
    
    public function print_items ()
    {
        if(Input::get('status'))
        {
            $selected_status = Input::get('status');
        }
        
        if($selected_status == 'All')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1, 2, 11))->orderBy('city')->get();
        }
        else if($selected_status == 'Pending')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1))->orderBy('city')->get();
        }
        else if($selected_status == 'Partly-Available')
        {
            $orders = SellOrder::where('hide', '=', 0)->where('status', 11)->orderBy('city')->get();
        }
        else if($selected_status == 'Not-Available')
        {
            $orders = SellOrder::where('hide', '=', 0)->whereIn('status', array(0, 1, 11))->orderBy('city')->get();
        }
        else
        {
            $orders = SellOrder::where('id', '=', 0)->get();
        }
        return view('admin.pages.fulfillment.print')->with(['orders'=>$orders]);
    }
}
