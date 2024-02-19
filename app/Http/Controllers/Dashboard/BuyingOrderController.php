<?php

namespace App\Http\Controllers\Dashboard;

use App\AgentBalance;
use App\BuyOrderNew;

use App\SellOrderItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;
use DB;
use Validator;

use App\Product;
use App\Agent;
use App\BuyOrder;
use App\BuyOrderItem;
use App\OrderStatus;
use App\City;
use App\Admin;
use App\BuyOrderNote;
use App\BuyOrderNoteStatus;
use App\TimeLine;
use App\Inventory;
use App\Category;
use App\ProductTimeline;

use App\ProductColor;
use App\ProductImage;
use App\ProductSize;

use Codexshaper\WooCommerce\Facades\Product as WooCommerceProduct;
use Codexshaper\WooCommerce\Facades\Category as WooCommerceCategory;
use Codexshaper\WooCommerce\Facades\Attribute;
use Codexshaper\WooCommerce\Facades\Variation;

class BuyingOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Buying Orders')) {
            abort('404');
        }

        $from_date = date('Y-m-d', strtotime('yesterday'));
        $to_date = date('Y-m-d');

        if ($request->from_date) {
            $from_date = $request->from_date;
        }
        if ($request->to_date) {
            $to_date = $request->to_date;
        }

        $rows = BuyOrder::query()->where('hide', '=', 0)->where('shipping_date', '>=', $from_date)
            ->where('shipping_date', '<=', $to_date);

        if ($request->product_id) {
            $productIdes = array_map('intval', $request->input('product_id'));
            $ordersIdes = BuyOrderItem::whereIn('product', $productIdes)->pluck('order')->toArray();
            $rows->whereIn('id', $ordersIdes);

        }

        if ($request->agent_id) {
            if ($request->agent_id != 'all')
                $rows->where('agent', $request->agent_id);
        }


        $agents = Agent::where('hide', 0)->get();


        $orders = $rows->orderBy('id', 'desc')->paginate(30);
        return view('admin.pages.buying_order.index')->with(['orders' => $orders, 'request' => $request, 'from_date' => $from_date, 'to_date' => $to_date, 'agents' => $agents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_buying_order')) {
            abort('404');
        }
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $first_box_id = "First_ITEM";
        $orders = BuyOrder::orderBy('id', 'desc')->first();
        $orders = $orders->id ?? 0 + 1;
        $agents = Agent::orderBy('name')->get();
        return view('admin.pages.buying_order.create')->with(['products' => $products, 'first_box_id' => $first_box_id, 'order_number' => $orders, 'agents' => $agents]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_buying_order')) {
            abort('404');
        }
        $arr_request = $request->all();
        $validator = Validator::make($request->all(), [
            'client' => 'required',
            'payment_status' => 'required|in:paid,not_paid,partly_paid',

        ],
            [
                'client.required' => 'Please Find This Order Agent'
            ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else {
            if ($request->payment_status == 'partly_paid') {
                $validator = Validator::make($request->all(), [
                    'payment_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',

                ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }
            if ($request->client == 0) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                ],
                    [
                        'name.required' => 'Please Enter Name',
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }

            if (!$request->type) {
                $validator = Validator::make($request->all(), [
                    'product' => 'required',
                    'qty.*' => 'required',
                    'price.*' => 'required|min:0|numeric'
                ],
                    [
                        'product.required' => 'Please Choose All Order Items',
                        'qty.*.required' => 'Enter Each Item Qty',
                        'price.*.required' => 'Enter Each Item Price',
                        'price.*.min' => 'Price MIN. Amount Is 0',
                        'price.*.numeric' => 'Item Price Must Be Number'
                    ]);
                // comment for closse validator from ahmed edition
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
                // else if(count($request->order_item) != count($request->product))
                // {
                //     return response()->json(['success' => false, 'errors'=>'You Must Choose Product In Each Order Item']);
                // }
                else if (count($request->product) != count($request->color)) {
                    return response()->json(['success' => false, 'errors' => 'You Must Choose Color In Each Order Item']);
                } else if (count($request->product) != count($request->size)) {
                    return response()->json(['success' => false, 'errors' => 'You Must Choose Size In Each Order Item']);
                }

            }

            // else if(count(array_unique($request->product)) != count($request->product))
            // {
            //     return response()->json(['success' => false, 'errors'=>'You Have Duplicated Items']);
            // }
            // comment for closse validator from ahmed edition
            if ($request->client > 0) {
                $client_id = $request->client;
                $client = Agent::find($request->client);
            } else {
                $client = new Agent;
                $client->name = $request->name;
                $client->save();
                $client_id = $client->id;
            }
            $order = new BuyOrder;
            $order->order_number = '-';
            if ($request->shipping_date != '') {
                $order->shipping_date = $request->shipping_date;
            } else {
                $order->shipping_date = date('Y-m-d');
            }
            $order->agent = $client->id;

            if ($request->type) {
                $order->type = $request->type;
            }

            $order->total_price = 0;
            $order->note = $request->order_note;
            $order->shipping_fees = 0;
            $order->city = 0;
            if ($request->hasFile('order_invoice')) {
                $validatedData = $request->validate([
                    'invoice' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
                ],
                    $messages = [
                        'invoice.mimes' => 'Please Choose Image File'
                    ]);

                $image = $request->file('order_invoice');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/invoices');
                $image->move($destinationPath, $imageName);
                $order->invoice = '/uploads/invoices/' . $imageName;
            }
            $order->save();

            $order->order_number = $order->id;
            $order->save();

            $total = 0;
            if ($request->product) {
                for ($i = 0; $i < count($request->product); $i++) {
                    $product = Product::find($request->product[$i]);
                    if ($product !== NULL) {
                        $qty = 0;
                        $colors_reqs = 'color';
                        $sizes_reqs = 'size';
                        $qty = $request->qty[$i] + $qty;
                        $item = new BuyOrderItem;
                        $item->order = $order->id;
                        $item->product = $request->product[$i];
                        $item->color = $request->color[$i];
                        $item->size = $request->size[$i];
                        // $item->note = $request->note[$i];
                        $old_qty = $item->qty;
                        $item->qty = $request->qty[$i];
                        $item->price = $request->price[$i];
                        $item->save();
                        update_bought_inventory($item->product, $item->color, $item->size, $item->qty);
                        $inventory = Inventory::where('product', $item->product)->where('color', $item->color)->where('size', $item->size)->first();

                        if (!$inventory) {
                            $inventory = Inventory::create([
                                'product' => $item->product,
                                'color' => $item->color,
                                'size' => $item->size,
                                'sold' => 0,
                                'bought' => 0,
                            ]);
                        }
                        if ($item->price > 0) {
                            $inventory->update([
                                'last_cost' => $item->price,
                            ]);
                        }

                        $total = $total + ($item->qty * $item->price);

                    }
                }
            }
            $order->total_price = $total;
            $order->save();
            if ($order->order_number == '') {
                $order->order_number = $order->id;
                $order->save();
            }

            $payment_amount = 0;
            $payment_status = $request->payment_status;
            if ($payment_status == 'paid') {
                $payment_amount = $order->total_price;
            } elseif ($payment_status == 'partly_paid') {
                $payment_amount = $request->payment_amount;
            } else {
            }

            $order->payment_amount = $payment_amount;
            $order->payment_status = $payment_status;

            $order->save();

            AgentBalance::create([
                'agent_id' => $order->agent,
                'buying_order_id' => $order->id,
                'total_price' => $order->total_price,
                'payment_amount' => $order->payment_amount,
                'differance' => $order->total_price - $order->payment_amount,
            ]);


            BuyOrderNew::where('order_number', $order->order_number)->delete();

            return response()->json(['success' => true, 'message' => "Order Has Been Placed Successfully"]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $totals = 0;
        $order = BuyOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        foreach ($order->items as $item) {
            $totals = $totals + ($item->qty * $item->price);
        }

        return view('admin.pages.buying_order.show')->with(['order' => $order, 'statuss' => $statuss, 'totals' => $totals]);
    }

    public function invoice($id)
    {
        $totals = 0;
        $orders = BuyOrder::findorfail($id);
        if ($orders->hide == 1) {
            return abort(404);
        }
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        foreach ($orders->items as $item) {
            $totals = $totals + ($item->qty * $item->price);
        }
        return view('admin.pages.buying_order.invoice')->with(['orders' => $orders, 'statuss' => $statuss, 'totals' => $totals]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'edit_buying_orders')) {
            abort('404');
        }
        $order = BuyOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $products = Product::where('hide', '=', 0)->orderBy('title')->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $ep = array();
        foreach ($order->items as $item) {
            if (!in_array($item->product, $ep)) {
                $ep[] = $item->product;
            }
        }
        $agents = Agent::orderBy('name')->get();
        return view('admin.pages.buying_order.edit')->with(['order' => $order, 'products' => $products, 'cities' => $cities, 'order_products' => $ep, 'agents' => $agents]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'edit_buying_orders')) {
            abort('404');
        }
        $order = BuyOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $arr_request = $request->all();
        $validator = Validator::make($request->all(), [
            'client' => 'required',
            'payment_status' => 'required|in:paid,not_paid,partly_paid',

        ],
            [
                'client.required' => 'Please Find This Order Agent'
            ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else {
            if ($request->payment_status == 'partly_paid') {
                $validator = Validator::make($request->all(), [
                    'payment_amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',

                ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }

            if ($request->client == 0) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'nullable|email'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.email' => 'Please Enter Correct Email Address',
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }
            if ($request->type == 'invoice') {

                $validator = Validator::make($request->all(), [
                    'product' => 'required',
                    'qty.*' => 'required',
                    'price.*' => 'required|min:0|numeric'
                ],
                    [
                        'product.required' => 'Please Choose All Order Items',
                        'qty.*.required' => 'Enter Each Item Qty',
                        'price.*.required' => 'Enter Each Item Price',
                        'price.*.min' => 'Price MIN. Amount Is 0',
                        'price.*.numeric' => 'Item Price Must Be Number'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                } else if (count($request->order_item) != count($request->product)) {
                    return response()->json(['success' => false, 'errors' => 'You Must Choose Product In Each Order Item']);
                } else if (count($request->order_item) != count($request->color)) {
                    return response()->json(['success' => false, 'errors' => 'You Must Choose Color In Each Order Item']);
                } else if (count($request->order_item) != count($request->size)) {
                    return response()->json(['success' => false, 'errors' => 'You Must Choose Size In Each Order Item']);
                }
            }
            // else if(count(array_unique($request->product)) != count($request->product))
            // {
            //     return response()->json(['success' => false, 'errors'=>'You Have Duplicated Items']);
            // }

            if ($request->client > 0) {
                $client_id = $request->client;
                $client = Agent::find($request->client);
            } else {
                $client = new Agent;
                $client->name = $request->name;
                $client->save();
                $client_id = $client->id;
            }
            $order->order_number = $request->order_number;
            if ($request->shipping_date != '') {
                $order->shipping_date = $request->shipping_date;
            } else {
                $order->shipping_date = date('Y-m-d', strtotime($order->created_at) + 24 * 3600);
            }
            $order->agent = $client->id;
            $order->total_price = 0;
            $order->shipping_fees = 0;
            $order->city = 0;

            $order->note = $request->order_note;

            if ($request->hasFile('order_invoice')) {
                $validatedData = $request->validate([
                    'invoice' => 'mimes:jpeg,png,jpg,gif,svg,gif,webp'
                ],
                    $messages = [
                        'invoice.mimes' => 'Please Choose Image File'
                    ]);
                if (File::exists(public_path() . $order->invoice)) {
                    File::delete(public_path() . $order->invoice);
                }
                $image = $request->file('order_invoice');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/invoices');
                $image->move($destinationPath, $imageName);
                $order->invoice = '/uploads/invoices/' . $imageName;
            }

            $order->save();
            $total = 0;
            $products_list = array();
            $xx = array();
            $xqty = array();
            if ($request->type == 'invoice') {
                foreach ($order->items as $item) {
                    $item->delete();
//                get_product_qty($item->product, $item->color, $item->size);
                    $product = Product::findorfail($item->product);
                    $products_list[] = $item->product;

                    update_bought_inventory($item->product, $item->color, $item->size, -$item->qty);

                    $inventory = Inventory::where('product', $item->product)->where('color', $item->color)->where('size', $item->size)->first();

                    if (!$inventory) {
                        $inventory = Inventory::create([
                            'product' => $item->product,
                            'color' => $item->color,
                            'size' => $item->size,
                            'sold' => 0,
                            'bought' => 0,
                        ]);
                    }
                    if ($item->price > 0) {
                        $inventory->update([
                            'last_cost' => $item->price,
                        ]);
                    }


//                $time_line = new ProductTimeline;
//                $time_line->product = $item->product;
//                $time_line->color = $item->color;
//                $time_line->size = $item->size;
//                $time_line->admin = Auth::guard('admin')->user()->id;
//                $time_line->order = $order->id;
//                $time_line->order_type = 2;
//                $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
//                $time_line->text = " Removed (".$item->qty.") Pieces From Inventory";
//                $time_line->save();
//                $xx[] = $time_line->id;
//                $xqty[$time_line->id] = $item->qty;
                }

                for ($i = 0; $i < count($request->order_item); $i++) {
                    $product = Product::find($request->product[$i]);
                    if ($product !== NULL) {
                        $qty = 0;
                        $qty_reqs = 'qty';
                        $colors_reqs = 'color';
                        $sizes_reqs = 'size';

                        $qty = $request->qty[$i] + $qty;
                        $item = new BuyOrderItem;

                        $item->order = $order->id;
                        $item->product = $request->product[$i];
                        $item->color = $request->color[$i];
                        $item->size = $request->size[$i];
                        $item->note = $request->note[$i];
                        $item->qty = $request->qty[$i];
                        $item->price = $request->price[$i];
                        $item->save();

//                    get_product_qty($item->product, $item->color, $item->size);

                        update_bought_inventory($item->product, $item->color, $item->size, $item->qty);


//                    $chhp = ProductTimeline::where('product', $item->product)->
//                    where('color', $item->color)->where('size', $item->size)->
//                    whereIn('id', $xx)->first();
//                    if($chhp === NULL)
//                    {
//                        $time_line = new ProductTimeline;
//                        $time_line->product = $item->product;
//                        $time_line->color = $item->color;
//                        $time_line->size = $item->size;
//                        $time_line->admin = Auth::guard('admin')->user()->id;
//                        $time_line->order = $order->id;
//                        $time_line->order_type = 2;
//                        $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
//                        $time_line->text = " Added New Buying Order With (".$item->qty.") Piece";
//                        $time_line->save();
//                    }
//                    else
//                    {
//                        $chhp->delete();
//                        $old_qty = $xqty[$chhp->id];
//                        if($old_qty != $item->qty)
//                        {
//                            $time_line = new ProductTimeline;
//                            $time_line->product = $item->product;
//                            $time_line->color = $item->color;
//                            $time_line->size = $item->size;
//                            $time_line->admin = Auth::guard('admin')->user()->id;
//                            $time_line->order = $order->id;
//                            $time_line->order_type = 2;
//                            $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
//                            $time_line->text = " Updated Qty At Buying order From (".$old_qty.") Pieces to (".$item->qty.") Pieces";
//                            $time_line->save();
//                        }
//                    }

                        $total = $total + ($item->qty * $item->price);
                    }
                }

            }
            $order->total_price = $total;
            $order->save();

            for ($i = 0; $i < count($products_list); $i++) {
                $product = Product::find($products_list[$i]);
                if ($product !== NULL && $product->hide == 0) {

                }
            }
            if ($order->order_number == '') {
                $order->order_number = $order->id;
                $order->save();
            }

            $payment_amount = 0;
            $payment_status = $request->payment_status;
            if ($payment_status == 'paid') {
                $payment_amount = $order->total_price;
            } elseif ($payment_status == 'partly_paid') {
                $payment_amount = $request->payment_amount;
            } else {
            }

            $order->payment_amount = $payment_amount;
            $order->payment_status = $payment_status;

            $order->save();
            $agentBalance = AgentBalance::where('buying_order_id', $order->id)->first();

            if ($agentBalance)
                $agentBalance->update([
                    'agent_id' => $order->agent,
                    'buying_order_id' => $order->id,
                    'total_price' => $order->total_price,
                    'payment_amount' => $order->payment_amount,
                    'differance' => $order->total_price - $order->payment_amount,
                ]);


            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 2;
            $event->text = " Has Updated Buying Order";
            $event->save();

            return response()->json(['success' => true, 'message' => "Order Has Been Updated Successfully"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'delete_buying_order')) {
            abort('404');
        }
        $order = BuyOrder::findorfail($id);
        $utems = $order->items;
        $order->delete();
        foreach ($utems as $item) {
            update_bought_inventory($item->product, $item->color, $item->size, -$item->qty);

        }
//        TimeLine::where('order', $order->id)->where('order_type', 2)->delete();
        BuyOrderItem::where('order', $order->id)->delete();
        AgentBalance::where('buying_order_id', $order->id)->delete();
        return redirect()->back();
    }

    public function selling_order_status(Request $request)
    {

        $order = BuyOrder::findorfail($request->num);

        if ($order->status == 0) {
            $old_status = "Pending";
        } else {
            $old_status = OrderStatus::findorfail($order->status);
            $old_status = $old_status->title;
        }
        $new_status = OrderStatus::findorfail($request->status);
        $new_status = $new_status->title;

        $order->status = $request->status;
        $order->save();
        if ($old_status != $new_status) {
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 2;
            $event->text = " Has Changed Buying Order Status From " . $old_status . " To " . $new_status;
            $event->save();
        }
        return redirect()->back();
    }

    public function orders_operation($type)
    {
        if (!Input::get('orders')) {
            abort(404);
        }


        if ($type == 'Shipping_Info') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = BuyOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->get();
            return view('admin.pages.buying_order.shipping_info')->with(['orders' => $orders]);
        } elseif ($type == 'Print_Invoice') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = BuyOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->get();
            return view('admin.pages.buying_order.invoice')->with(['orders' => $orders]);
        } elseif ($type == 'Shipping_Products') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = BuyOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->orderBy('city')->get();
            $abb = array();
            $cities = array();
            $products = array();
            $qtys = array();
            $colors = array();
            $sizes = array();
            foreach ($orders as $order) {
                $stt = $order->city;
                foreach ($order->items as $item) {
                    $stt .= '-' . $item->product . '-' . $item->color . '-' . $item->size;
                    if (!in_array($stt, $abb)) {
                        $abb[] = $stt;
                        $cities[] = $order->city_info->title;
                        $products[] = $item->product_info->title;
                        if ($item->color > 0) {
                            $colors[] = $item->color_info->title;
                        } else {
                            $colors[] = '';
                        }
                        if ($item->size > 0) {
                            $sizes[] = $item->size_info->title;
                        } else {
                            $sizes[] = '';
                        }
                        $qtys[] = $item->qty;
                    } else {
                        $key = array_search($stt, $abb);
                        $qtys[$key] = $qtys[$key] + $item->qty;
                    }
                }
            }

            return view('admin.pages.buying_order.shipping_products')->with(['orders' => $orders, 'cities' => $cities
                , 'products' => $products, 'qtys' => $qtys, 'colors' => $colors, 'sizes' => $sizes]);
        } else {
            abort(404);
        }
    }

    public function orders_task(Request $request)
    {
        if ($request->type == 'Change_Status') {
            $validator = Validator::make($request->all(), [
                'items' => 'required',
                'status' => 'required',
            ],
                [
                    'items.required' => 'Please Choose Orders',
                    'status.required' => 'Please Choose Status For This Orders'
                ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            }
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = BuyOrder::findorfail($id);
                if ($order->status == 0) {
                    $old_status = "Pending";
                } else {
                    $old_status = OrderStatus::findorfail($order->status);
                    $old_status = $old_status->title;
                }
                $new_status = OrderStatus::findorfail($request->status);
                $new_status = $new_status->title;

                $order->status = $request->status;
                $order->save();
                if ($old_status != $new_status) {
                    $event = new TimeLine;
                    $event->admin = Auth::guard('admin')->user()->id;
                    $event->order = $order->id;
                    $event->order_type = 2;
                    $event->text = " Has Changed Buying Order Status From " . $old_status . " To " . $new_status;
                    $event->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Orders Status Changed']);
        } elseif ($request->type == 'Delete') {
            if (!permission_checker(Auth::guard('admin')->user()->id, 'delete_buying_order')) {
                abort('404');
            }
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = BuyOrder::findorfail($id);
                $items = $order->items;
                $order->delete();

                foreach ($items as $item) {
                    get_product_qty($item->product, $item->color, $item->size);
                    $item->hide = 1;
                    $item->save();

                    $time_line = new ProductTimeline;
                    $time_line->product = $item->product;
                    $time_line->color = $item->color;
                    $time_line->size = $item->size;
                    $time_line->admin = Auth::guard('admin')->user()->id;
                    $time_line->order = $order->id;
                    $time_line->order_type = 2;
                    $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
                    $time_line->text = " Removed (" . $item->qty . ") Pieces From Inventory";
                    $time_line->save();
                }
                $TimeLine::where('order', $order->id)->where('order_type', 2)->delete();
                BuyOrderItem::where('order', $order->id)->delete();
            }
            return response()->json(['success' => true, 'message' => 'Orders Deleted']);
        } elseif ($request->type == 'Change_REP') {
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = BuyOrder::findorfail($id);

                if ($order->delivered_by == 0) {
                    $eea = "From None";
                    $old_r = "None";
                } else {
                    $old_rep = Admin::findorfail($order->delivered_by);
                    $eea = " From " . $old_rep->name;
                    $old_r = $old_rep->name;
                }
                if ($request->rep > 0) {
                    $new_rep = Admin::findorfail($request->rep);
                    $eea .= " To " . $new_rep->name;
                    $new_r = $new_rep->name;
                } else {
                    $eea .= " To None";
                    $new_r = "None";
                }
                $order->delivered_by = $request->rep;
                $order->save();
                if ($old_r != $new_r) {
                    $event = new TimeLine;
                    $event->admin = Auth::guard('admin')->user()->id;
                    $event->order = $order->id;
                    $event->order_type = 2;
                    $event->text = " Has Changed Buying Order REP" . $eea;
                    $event->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Success']);
        }
    }


    public function notes_viewer(Request $request)
    {
        $last_note = BuyOrderNote::where('order', '=', $request->order)->orderBy('id', 'desc')->first();
        if ($last_note !== NULL) {
            $last_viewed = BuyOrderNoteStatus::where('admin', '=', Auth::guard('admin')->user()->id)->where('order', '=', $request->order)->orderBy('note', 'desc')->first();
            if ($last_viewed === NULL) {
                $st = new BuyOrderNoteStatus;
                $st->order = $request->order;
                $st->note = $last_note->id;
                $st->admin = Auth::guard('admin')->user()->id;
                $st->save();
            } else {
                if ($last_note->id != $last_viewed->note) {
                    $st = new BuyOrderNoteStatus;
                    $st->order = $request->order;
                    $st->note = $last_note->id;
                    $st->admin = Auth::guard('admin')->user()->id;
                    $st->save();
                }
            }
        }
        return response()->json(['success' => true]);
    }

    public function create_note($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required',
        ],
            [
                'note.required' => 'Please Enter Your Note',
            ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else {
            $order = BuyOrder::findorfail($id);
            $note = new BuyOrderNote;
            $note->order = $id;
            $note->added_by = Auth::guard('admin')->user()->id;
            $note->note = $request->note;
            $note->save();

            $st = new BuyOrderNoteStatus;
            $st->order = $note->order;
            $st->note = $note->id;
            $st->admin = Auth::guard('admin')->user()->id;
            $st->save();

            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 2;
            $event->text = " Has Created Buying Order Note [" . $note->note . "]";
            $event->save();

            return response()->json(['success' => true, 'message' => "Note Has Been Added Successfully"]);
        }
    }

    //////new eidt for ahmed ///////
    public function newcreate()
    {
        // rif(!permission_checker(Auth::guard('admin')->user()->id, 'add_buying_order')) {abort('404');}
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $first_box_id = "First_ITEM";
        $orders = BuyOrder::orderBy('id', 'desc')->first();
        $orders = $orders->id + 1;
        $agents = Agent::orderBy('name')->get();
        return view('admin.pages.buying_order.buying_add')->with(['products' => $products, 'first_box_id' => $first_box_id, 'order_number' => $orders, 'agents' => $agents]);
    }

    public function ajax_store(Request $request)
    {
        if ($request->size_id !== "00" and $request->color_id !== "00") {
            $product = BuyOrderNew::create([

                'order_number' => $request->get('order_num'),
                'product_id' => $request->get('product_id'),
                'qty' => $request->get('qty'),
                'price' => $request->get('price'),
                'color_id' => $request->get('color_id'),
                'size_id' => $request->get('size_id'),
            ]);
            $name = $product->Product;
            $size = $product->Size;
            $color = $product->Color;
            $total_qty = BuyOrderNew::sum('price');

            $html = view('admin.pages.buying_order.buying_table_ajax', compact('name', 'size', 'color', 'total_qty', 'product'))->render();
            return response()->json(['status' => true, 'result' => $html]);
        }

    }

    public function delete(Request $request)
    {
        $id = $request->id_product;
        $row = BuyOrderNew::where('id', $id)->first();
        $price = $row->qty * $row->price;
        $qty = $row->qty;
        $row->delete();
        return response()->json([
            'success' => 'Record deleted successfully!',
            'price' => $price,
            'qty' => $qty
        ]);
    }

    ////////////////////////////////

    public function fetch_color(Request $request)
    {
        $products_fetch = ProductColor::where('product', $request->product_id)->get();
        $html = view('admin.pages.buying_order.buying_color_ajax', compact('products_fetch'))->render();
        return response()->json(['status' => true, 'result' => $html]);
    }

    public function fetch_size(Request $request)
    {
        $products_fetch = ProductSize::where('product', $request->product_id)->get();
        $html = view('admin.pages.buying_order.buying_size_ajax', compact('products_fetch'))->render();
        return response()->json(['status' => true, 'result' => $html]);
    }

    public function update_qty_ajax(Request $request)
    {
        $new_qty = $request->qty;
        $product = BuyOrderNew::where('id', $request->product_id)->first();
        $old_qty = $product->qty;
        $product->update(['qty' => $new_qty]);
        $total_price_item = $request->qty * $request->price;
        return response()->json(['status' => true, 'total_price_item' => $total_price_item]);
    }

    public function update_price_ajax(Request $request)
    {
        $new_price = $request->price;
        $product = BuyOrderNew::where('id', $request->product_id)->first();
        $product->update(['price' => $new_price]);
        $total_price_item = $request->qty * $request->price;
        return response()->json(['status' => true, 'total_price_item' => $total_price_item]);
    }


    public function getAgents(Request $request)
    {

        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('agents')->select('id', 'name as text')
                ->where('name', 'LIKE', '%' . $term . '%')
                ->orWhere('phone', 'LIKE', '%' . $term . '%')
                ->orderBy('name', 'asc')->simplePaginate(6);

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

    public function add_payment(Request $request)
    {
        $orders = BuyOrder::orderBy('id', 'desc')->first();
        $orders = $orders->id ?? 0 + 1;
        $agents = Agent::orderBy('name')->get();
        return view('admin.pages.buying_order.payment')->with(['order_number' => $orders, 'agents' => $agents]);


    }


    public function update_payment_amount(){

        $chunkSize = 200;
        $lastId = 0;

        do {
            $orders = DB::table('buy_orders')
                ->where('id', '<', 6199)
                ->where('id', '>', $lastId)
                ->orderBy('id')
                ->limit($chunkSize)
                ->get();

            if ($orders->isEmpty()) {
                break;
            }

            $lastId = $orders->last()->id;

            $ordersToUpdate = [];

            foreach ($orders as $order) {
                $ordersToUpdate[] = [
                    'id' => $order->id,
                    'payment_amount' => $order->total_price,
                ];
            }
            // Perform upsert using raw SQL
            foreach ($ordersToUpdate as $order) {
                DB::table('buy_orders')->updateOrInsert(
                    ['id' => $order['id']],
                    ['payment_amount' => $order['payment_amount']]
                );
            }

        } while ($orders->count() === $chunkSize);

        return "Elsdodey";
    }


}
