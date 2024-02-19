<?php

namespace App\Http\Controllers\Dashboard;

use App\OrderStatus;
use App\SellOrder;
use App\SellOrderItem;
use App\TimeLine;
use Codexshaper\WooCommerce\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FulfillmentsController extends Controller
{
    //
    public function index(Request $request)
    {
        $items = SellOrderItem::whereHas('order_info', function ($query) use ($request) {
            $query->where('hide', 0);

            $query->when($request->status, function ($q) use ($request) {
                $q->where('sell_orders.status', $request->status);
            }, function ($q) {
                $q->whereIn('sell_orders.status', [1, 2, 11]);
            });
        })
            ->with([
                'order_info.tags',
                'order_info.client_info',
                'order_info.city_info',
            ])
            ->where('sell_order_items.hide', 0) // Specify the table for hide column
//            ->join('sell_orders', 'sell_order_items.order', '=', 'sell_orders.id')

            ->get(); // Specify the columns you need

        return view('admin.pages.fulfillments.index', compact('items', 'request'));
    }

    public function fulfillments_action($id){
        $item=SellOrderItem::findOrfail($id);
        $order=SellOrder::findOrFail($item->order);
        $old_model_status=OrderStatus::findOrFail($order->status);
        if ($item->fulfillment==1){
            $item->fulfillment=0;
        }
        else{
            $item->fulfillment=1;
        }
        $item->save();

        $all_order_item_count=SellOrderItem::where('hide',0)->where('order',$order->id)->count();
        $available_order_item_count=SellOrderItem::where('hide',0)->where('order',$order->id)->where('fulfillment',1)->count();

        if ($available_order_item_count==$all_order_item_count){
            $order->status=2;
        }
        elseif ($available_order_item_count>0&& $available_order_item_count<$all_order_item_count){
            $order->status=11;
        }
        else{
            $order->status=1;
        }
        $order->save();
        $new_model_status=OrderStatus::findOrFail($order->status);

        if ($new_model_status->id!= $old_model_status->id){

            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From ".$old_model_status->title. " To ".$new_model_status->title;
            $event->from_status=$old_model_status->id;
            $event->to_status=$new_model_status->id;
            $event->save();
        }

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!',
            ]);

    }
}
