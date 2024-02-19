<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Input;


use App\SellOrder;
use App\City;
use App\Admin;
use App\OrderStatus;
use App\Category;
use App\SellOrderItem;
use App\Client;
use App\SellOrderTag;
use App\OrderTag;

class ModeratorReportController extends Controller
{
    public function index()
    {
        $first_order = SellOrder::where('hide', '=', 0)->orderBy('created_at')->first();
        $from_date = date('Y-m-d', strtotime($first_order->created_at));
        $from_date = date('Y-m-d', strtotime('- 7 Days'));
        $to_date = date('Y-m-d');
        $selected_city = array();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $reps = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $selected_rep = array();
        $selected_moderator = array();
        $admins = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $selected_status = array();
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        $selected_product = array();
        $selected_tags = array();
        $all_products = Category::where('cat', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $selected_client_type = "All";
        $orders = SellOrder::where('hide', '=', 0);
        $new_clients_orders = 0;
        $recurring_clients_orders = 0;

        if(Input::get('from_date'))
        {
            $from_date = Input::get('from_date');
        }
        if(Input::get('to_date'))
        {
            $to_date = Input::get('to_date');
        }
        if($from_date != '' && $to_date != '')
        {
            $orders = $orders->whereBetween('created_at', array($from_date." 00:00:00", $to_date." 23:59:59"));
        }
        else if($from_date == '' && $to_date != '')
        {
            $orders = $orders->whereDate('created_at', '<=', $to_date);
        }
        else if($from_date != '' && $to_date == '')
        {
            $orders = $orders->whereDate('created_at', '>=', $from_date);
        }

        $selected_orders = array();
        if(Input::get('product'))
        {
            $selected_product = Input::get('product');
            if(count($selected_product) > 0)
            {
                $product_orders = SellOrderItem::whereIn('product', $selected_product)->pluck('order')->toArray();
                $selected_orders = $product_orders;
            }
        }

        if(Input::get('tags'))
        {
            $selected_tags = Input::get('tags');
            if(count($selected_tags) > 0)
            {
                $tags_orders = SellOrderTag::whereIn('tag_id', $selected_tags)->pluck('order_id')->toArray();
                if(count($selected_product) > 0)
                {
                    $selected_orders = array_intersect($selected_orders, $tags_orders);
                }
                else {
                    $selected_orders = $tags_orders;
                }
            }
        }

        if(count($selected_orders) > 0 && ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0)))
        {
            $orders = $orders->whereIn('id', $selected_orders);
        }
        else if(count($selected_orders) == 0 && ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0))) {
            $orders = $orders->where('id', 0);
        }


        if(isset($_GET['client_type']))
        {
            $selected_client_type = Input::get('client_type');
            if($selected_client_type == '0')
            {
                $abc = array();
                $abc = Client::where('hide', 0)->where('type', 0)->pluck('id');
                $orders = $orders->whereIn('client', $abc);
            }
            else if($selected_client_type == '1')
            {
                $abc = array();
                $abc = Client::where('hide', 0)->where('type', 1)->pluck('id');
                $orders = $orders->whereIn('client', $abc);
            }
        }
        if(Input::get('rep'))
        {
            $selected_rep = Input::get('rep');
            if(count($selected_rep) > 0 && !in_array("All", $selected_rep))
            {
                $orders = $orders->whereIn('delivered_by', $selected_rep);
            }
        }
        
        if(Input::get('moderator'))
        {
            $selected_moderator = Input::get('moderator');
            if(count($selected_moderator) > 0 && !in_array("All", $selected_moderator))
            {
                $orders = $orders->whereIn('added_by', $selected_moderator);
            }
        }
        
        if(Input::get('city'))
        {
            $selected_city = Input::get('city');
            if(count($selected_city) > 0 && !in_array("All", $selected_city))
            {
                $orders = $orders->whereIn('city', $selected_city);
            }
        }
        
        if(Input::get('status'))
        {
            $selected_status = Input::get('status');
            if(count($selected_status) > 0 && !in_array("All", $selected_status))
            {
                $orders = $orders->where('status', $selected_status);
            }
        }


        $orders_list = $orders->pluck('id');
        $orders_clients = $orders->pluck('client');
        $orders = $orders->get();
        $items = array();
        $products_num = array();
        $products = array();
        $total_items = 0;
        $total_cost = 0;

        $new_clients_orders = Client::whereIn('id', $orders_clients)->where('type', 0)->count();
        $recurring_clients_orders = Client::whereIn('id', $orders_clients)->where('type', 1)->count();

        $total_items = SellOrderItem::whereIn('order', $orders_list)->get()->sum('qty');
        $all_tags = OrderTag::get();
        return view('admin.reports.moderators')->with(['first_order'=>$first_order, 'from_date'=>$from_date, 'to_date'=>$to_date, 'selected_city'=>$selected_city, 'cities'=>$cities, 
        'reps'=>$reps, 'selected_rep'=>$selected_rep, 'selected_moderator'=>$selected_moderator, 'admins'=>$admins, 'selected_status'=>$selected_status, 'statuss'=>$statuss
        , 'selected_product'=>$selected_product, 'products'=>$products, 'all_products'=>$all_products, 'orders'=>$orders, 'total_items'=>$total_items, 
        'total_cost'=>$total_cost, 'new_clients_orders'=>$new_clients_orders, 'recurring_clients_orders'=>$recurring_clients_orders, 
        'selected_client_type'=>$selected_client_type, 'all_tags'=>$all_tags, 'selected_tags' => $selected_tags]);
    }

    public function report_moderators_table (Request $request)
    {
        $selected_city = explode(',', $request->city);
        $selected_rep = explode(',', $request->rep);
        $selected_moderator = explode(',', $request->moderator);
        $selected_status = $request->status;
        $selected_product = explode(',', $request->product);
        $selected_tags = explode(',', $request->tags);
        $selected_client_type = $request->client_type;
        $orders = SellOrder::where('hide', '=', 0);
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        if($from_date != '' && $to_date != '')
        {
            $orders = $orders->whereBetween('created_at', array($from_date." 00:00:00", $to_date." 23:59:59"));
        }
        else if($from_date == '' && $to_date != '')
        {
            $orders = $orders->whereDate('created_at', '<=', $to_date);
        }
        else if($from_date != '' && $to_date == '')
        {
            $orders = $orders->whereDate('created_at', '>=', $from_date);
        }

        $selected_orders = array();
        if(count($selected_product) > 0 && $selected_product[0] != '')
        {
            $product_orders = SellOrderItem::whereIn('product', $selected_product)->pluck('order')->toArray();
            $selected_orders = $product_orders;
        }

        if(count($selected_tags) > 0 && $selected_tags[0] != '')
        {
            $tags_orders = SellOrderTag::whereIn('tag_id', $selected_tags)->pluck('order_id')->toArray();
            if(count($selected_product) > 0)
            {
                $selected_orders = array_intersect($selected_orders, $tags_orders);
            }
            else {
                $selected_orders = $tags_orders;
            }
        }

        if(count($selected_orders) > 0 && ((count($selected_product) > 0 && $selected_product[0] != '') || (count($selected_tags) > 0 && $selected_tags[0] != '')))
        {
            $orders = $orders->whereIn('id', $selected_orders);
        }
        else if(count($selected_orders) == 0 && ((count($selected_product) > 0 && $selected_product[0] != '') || (count($selected_tags) > 0 && $selected_tags[0] != ''))) {
            $orders = $orders->where('id', 0);            
        }



        if($selected_client_type == '0')
        {
            $abc = array();
            $abc = Client::where('hide', 0)->where('type', 0)->pluck('id');
            $orders = $orders->whereIn('client', $abc);
        }
        else if($selected_client_type == '1')
        {
            $abc = array();
            $abc = Client::where('hide', 0)->where('type', 1)->pluck('id');
            $orders = $orders->whereIn('client', $abc);
        }

        if(count($selected_rep) > 0 && $selected_rep[0] != '')
        {
            $orders = $orders->whereIn('delivered_by', $selected_rep);
        }

        if(count($selected_moderator) > 0 && $selected_moderator[0] != '')
        {
            $orders = $orders->whereIn('added_by', $selected_moderator);
        }

        if(count($selected_city) > 0 && $selected_city[0] != '')
        {
            $orders = $orders->whereIn('city', $selected_city);
        }

        if($selected_status > 0)
        {
            $orders = $orders->where('status', $selected_status);
        }
        $orders_clients = $orders->pluck('client');
        $orders_list = $orders->pluck('id');

        $orders = $orders->get();
        // print_r($orders_list);
        $items = array();
        $moderators_num = array();
        $moderators = array();

        $total_items = 0;
        $total_cost = 0;

        $new_clients_orders = Client::whereIn('id', $orders_clients)->where('type', 0)->count();
        $recurring_clients_orders = Client::whereIn('id', $orders_clients)->where('type', 1)->count();

        $all_orders = SellOrderItem::select('order', 'qty', 'product', 'color', 'size', 'price')->
        whereIn('order', $orders_list)->having('qty', '!=', 0)->with('order_info', 'product_info', 'color_info', 'size_info')->get();

        $oo_orders = [];
        foreach ($all_orders as $item)
        {
            $all_qty = $item->qty;
            $order = $item->order_info;
            $itx = "Moderator - ".$item->order_info->added_by;
            if(in_array($itx, $items))
            {
                $in = array_search($itx, $items);
                if (!in_array($item->order, $oo_orders)) 
                {
                    $oo_orders[] = $item->order;
                    $moderators[$in][0] = $moderators[$in][0] + 1;
                    if($order->status == 5 || $order->status == 6) {$moderators[$in][2] = $moderators[$in][2] + 1;}
                    else if($order->status == 7 || $order->status == 8) {$moderators[$in][4] = $moderators[$in][4] + 1;}
                    else {$moderators[$in][3] = $moderators[$in][3] + 1;}
                }
                
                // $moderators[$in][0] = $moderators[$in][0] + $all_qty;
                $total_items = $total_items + $all_qty;


                if($order->status != 7 && $order->status != 8) 
                {
                    $amount = $all_qty * $item->price; 
                    $cost = 0;
                }
                else {$amount = 0; $cost = 0;}
                $total_cost = $total_cost + ($cost * $all_qty);
                $moderators[$in][5] = $moderators[$in][5] + $amount;
            }
            else
            {
                if (!in_array($item->order, $oo_orders)) 
                {
                    $oo_orders[] = $item->order;
                    if($order->status == 5 || $order->status == 6) {$won = 1; $open = 0; $loss = 0;}
                    else if($order->status == 7 || $order->status == 8) {$won = 0; $open = 0; $loss = 1;}
                    else {$won = 0; $open = 1; $loss = 0;}
                }
                
               
                if($order->status != 7 && $order->status != 8) 
                {
                    $amount = $all_qty * $item->price; 
                    $cost = 0;
                }
                else {$amount = 0; $cost = 0;}
                $total_cost = $total_cost + ($cost * $all_qty);
                $items[] = $itx;
                $px = "";
                if($item->order_info->moderator_info !== NULL)
                {
                    $px = $item->order_info->moderator_info->name;
                }
                else
                {
                    $px = "Unknown";
                }
                $cost = 0;
                $moderators[] = array(1, $px, $won, $open, $loss, $amount, $cost);
                $total_items = $total_items + $all_qty;
            }
        }
        rsort($moderators);
        ?>
         <div id="product_reports_table" class="minimized_table table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>City</th>
                        <th>Count</th>
                        <th>Percentage %</th>
                        <th>Won</th>
                        <th>Open</th>
                        <th>Lost</th>
                        <th>Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($moderators); $i++)
                    {
                        ?>
                        <tr>
                            <td><?php echo $moderators[$i][1]; ?></td>
                            <td><?php echo $moderators[$i][0]; ?></td>
                            <td><?php echo number_format(($moderators[$i][0] / count($orders_list)) * 100, 2); ?> %</td>
                            <td><?php echo number_format($moderators[$i][2], 2); ?></td>
                            <td><?php echo number_format($moderators[$i][3], 2); ?></td>
                            <td><?php echo number_format($moderators[$i][4], 2); ?></td>
                            <td><?php echo number_format($moderators[$i][5], 2); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center pt-5 pb-5" id="productts_table_btn_container">
                <button type="button" id="products_table_btn" class="btn btn-pill btn-outline-info">View More</button>
            </div>
            </div>
            <?php
            $avg_cost = number_format(0, 2);
            if($orders->count() > 0) {$avg_cost = number_format($total_cost / $orders->count(), 2);}
            $total_rev = number_format($orders->sum('total_price') - $total_cost, 2);
            ?>
            <script>
                $('#avg_orders_cost').html('<?php echo $avg_cost; ?> EGP');
                $('#total_order_revnue').html('<?php echo $total_rev; ?> EGP');
            </script>
        <?php
    }

}
