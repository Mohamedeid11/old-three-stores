<?php

namespace App\Http\Controllers\Dashboard;

use App\Inventory;
use App\Traits\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\Rule;

use Validator;

use App\Product;
use App\Client;
use App\SellOrder;
use App\SellOrderItem;
use App\OrderStatus;
use App\City;
use App\Admin;
use App\OrderNote;
use App\OrderNoteRep;
use App\OrderNoteStatus;
use App\TimeLine;
use App\Fulfillment;
use App\OrderCategory;
use App\Color;
use App\Size;
use App\Category;

use App\SellOrderTag;
use App\OrderTag;

use App\Tag;
use App\OrderNoteTag;
use App\ProductTimeline;

use App\Traits\JT;

class SellingOrderController extends Controller
{
    use JT,Chat;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home2()
    {
        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders') && Auth::guard('admin')->user()->position == 1) {
            abort('404');
        } else if (Auth::guard('admin')->user()->position == 2 && !permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            return $this->delivery();
        }

        if (Auth::guard('admin')->user()->position == 1 || permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            $orders = SellOrder::where('hide', '=', 0);
        }
        $first_order = SellOrder::where('hide', '=', 0)->orderBy('created_at')->first();
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        $clients = Client::where('hide', '=', 0)->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $admins = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $search_filter = "";
        $from_date = date('Y-m-d', strtotime($first_order->created_at));
        $from_date = date('Y-m-d', strtotime('- 30 Days'));
        $to_date = date('Y-m-d');
        $selected_status = array();
        $selected_client = 0;
        $selected_admin = array();
        $selected_moderator = array();
        $selected_city = 0;
        $selected_product = array();
        $selected_tags = array();
        //$orders_per_page = 50;
        $orders_per_page = 0;
        $selected_order = array();
        $search_filter_clients = array();
        if (Input::get('search')) {
            $search_filter = Input::get('search');
            if ($search_filter != "") {
                $clients = Client::where('name', 'LIKE', '%' . $search_filter . '%')->orWhere('email', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search_filter . '%')->orWhere('address', 'LIKE', '%' . $search_filter . '%')->where('hide', '=', 0)->get();
                foreach ($clients as $client) {
                    $search_filter_clients[] = $client->id;
                }
            }
        }

        $selected_orders = array();
        if (Input::get('products')) {
            $selected_product = Input::get('products');
            if (count($selected_product) > 0) {
                $product_orders = SellOrderItem::whereIn('product', $selected_product)->pluck('order')->toArray();
                $selected_orders = $product_orders;
            }
        }

        if (Input::get('tags')) {
            $selected_tags = Input::get('tags');
            if (count($selected_tags) > 0) {
                $tags_orders = SellOrderTag::whereIn('tag_id', $selected_tags)->pluck('order_id')->toArray();
                if (count($selected_product) > 0) {
                    $selected_orders = array_intersect($selected_orders, $tags_orders);
                } else {
                    $selected_orders = $tags_orders;
                }
            }
        }

        if (count($selected_orders) > 0 && ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0))) {
            $orders = $orders->whereIn('id', $selected_orders);
        } else if (count($selected_orders) == 0 && ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0))) {
            $orders = $orders->where('id', 0);
        }

        if ($search_filter != '') {
            $orders = $orders->whereIn('client', $search_filter_clients);
        }
        if (Input::get('from_date')) {
            $from_date = Input::get('from_date');
        }
        if (Input::get('to_date')) {
            $to_date = Input::get('to_date');
        }
        if ($from_date != '' && $to_date != '') {
            $orders = $orders->whereBetween('created_at', array($from_date . " 00:00:00", $to_date . " 23:59:59"));
        } else if ($from_date == '' && $to_date != '') {
            $orders = $orders->whereDate('created_at', '<=', $to_date);
        } else if ($from_date != '' && $to_date == '') {
            $orders = $orders->whereDate('created_at', '>=', $from_date);
        }

        if (Input::get('status')) {
            $selected_status = Input::get('status');
        }

        if (count($selected_status) == 0 || ($selected_status[0] == '' && count($selected_status) == 1)) {
            $orders = $orders->whereNotIn('status', [0, 1, 6, 8]);
        } else if (in_array('All', $selected_status)) {

        } else {
            $orders = $orders->whereIn('status', $selected_status);
        }
        if (Input::get('client')) {
            $selected_client = Input::get('client');
            if ($selected_client > 0) {
                $orders = $orders->where('client', '=', $selected_client);
            }
        }
        if (Input::get('admin')) {
            $selected_admin = Input::get('admin');
        }
        if (in_array('All', $selected_admin)) {

        } else if (count($selected_admin) > 0 && $selected_admin[0] != '') {
            $orders = $orders->whereIn('delivered_by', $selected_admin);
        }


        if (Input::get('moderator')) {
            $selected_moderator = Input::get('moderator');
        }
        if (in_array('All', $selected_moderator)) {

        } else if (count($selected_moderator) > 0 && $selected_moderator[0] != '') {
            $orders = $orders->whereIn('added_by', $selected_moderator);
        }

        if (Input::get('order_number')) {
            $selected_order = Input::get('order_number');
        }
        if (in_array('All', $selected_order)) {

        } else if (count($selected_order) > 0 && $selected_order[0] != '') {
            $orders = $orders->whereIn('id', $selected_order);
        }

        if (Input::get('city')) {
            $selected_city = Input::get('city');
            if ($selected_city > 0) {
                $orders = $orders->where('city', '=', $selected_city);
            }
        }
        if (Input::get('orders_per_page')) {
            $orders_per_page = Input::get('orders_per_page');
        }
        if ($orders_per_page > 0) {
            $orders = $orders->orderBy('id', 'desc')->paginate($orders_per_page);
        } else {
            $orders = $orders->orderBy('id', 'desc')->with(['client_info', 'status_info', 'city_info', 'itemsq', 'delivery_info', 'notes', 'time_lines']
            )->get();

        }
        $all_orders = SellOrder::where('hide', '=', 0)->orderBy('created_at')->get();

        $delete_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order')) {
            $delete_selling_order = true;
        }

        $edit_selling_orders = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders')) {
            $edit_selling_orders = true;
        }

        $add_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            $add_selling_order = true;
        }

        return view('admin.pages.selling_order.index')->with(['orders' => $orders, 'statuss' => $statuss, 'from_date' => $from_date,
            'to_date' => $to_date, 'selected_status' => $selected_status, 'clients' => $clients, 'all_orders' => $all_orders, 'selected_order' => $selected_order,
            'selected_client' => $selected_client, 'cities' => $cities, 'selected_city' => $selected_city, 'orders_per_page' => $orders_per_page
            , 'selected_admin' => $selected_admin, 'admins' => $admins, 'products' => $products, 'selected_product' => $selected_product, 'search_filter' => $search_filter,
            'delete_selling_order' => $delete_selling_order, 'selected_moderator' => $selected_moderator, 'selected_tags' => $selected_tags,
            'edit_selling_orders' => $edit_selling_orders, 'add_selling_order' => $add_selling_order]);
    }

    public function index()
    {
        // $sell_order = SellOrder::where('address', '!=', '')->first();
        // $this->init_order($sell_order);
        // dd(1);
        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders') && Auth::guard('admin')->user()->position == 1) {
            abort('404');
        } else if (Auth::guard('admin')->user()->position == 2 && !permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            return $this->delivery();
        }

        $first_order = SellOrder::where('hide', '=', 0)->orderBy('created_at')->first();

        $statuss = OrderStatus::where('hide', '=', 0)->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $admins = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $moderators = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $search_filter = "";
        $from_date = date('Y-m-d', strtotime($first_order->created_at ?? ''));
        $all_products = Category::where('cat', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();


        $from_date = date('Y-m-d', strtotime('- 30 Days'));
        $to_date = date('Y-m-d');

        $delete_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order')) {
            $delete_selling_order = true;
        }

        $edit_selling_orders = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders')) {
            $edit_selling_orders = true;
        }

        $add_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            $add_selling_order = true;
        }

        $repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $tags = Tag::get();
        $all_tags = OrderTag::get();
        $selected_status = [];
        $selected_admin = [];
        $selected_moderator = [];
        $selected_tags = [];
        $selected_product = [];
        return view('admin.pages.selling_order.index2')->with(['statuss' => $statuss, 'from_date' => $from_date,
            'to_date' => $to_date, 'cities' => $cities, 'all_products' => $all_products, 'admins' => $admins,
            'products' => $products, 'search_filter' => $search_filter, 'tags' => $tags, 'repps' => $repps, 'selected_product' => $selected_product,
            'delete_selling_order' => $delete_selling_order, 'edit_selling_orders' => $edit_selling_orders, 'all_tags' => $all_tags,
            'add_selling_order' => $add_selling_order, 'moderators' => $moderators, 'selected_status' => $selected_status,
            'selected_admin' => $selected_admin, 'selected_moderator' => $selected_moderator, 'selected_tags' => $selected_tags]);
    }

    public function home_search()
    {
        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Selling Orders') && Auth::guard('admin')->user()->position == 1) {
            abort('404');
        } else if (Auth::guard('admin')->user()->position == 2 && !permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            return $this->delivery();
        }

        if (Auth::guard('admin')->user()->position == 1 || permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            // limit_orders
            // $orders = SellOrder::where('hide', '=', 0);
            $orders = SellOrder::where('hide', '=', 0);
        }
        $first_order = SellOrder::where('hide', '=', 0)->orderBy('created_at')->first();
        $statuss = OrderStatus::where('hide', '=', 0)->get();
//        $clients = Client::where('hide', '=', 0)->get();
//        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
//        $admins = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
//        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $search_filter = "";
        $from_date = date('Y-m-d', strtotime($first_order->created_at));
        $from_date = date('Y-m-') . '01';
        $to_date = date('Y-m-d');
        $selected_status = array();
        $selected_client = 0;
        $selected_admin = array();
        $selected_moderator = array();
        $selected_city = 0;
        $selected_product = array();
        $selected_tags = array();
        //$orders_per_page = 50;
        $orders_per_page = 0;
        $selected_order = array();
        $search_filter_clients = array();
//        if(Input::get('search'))
//        {
//            $search_filter = Input::get('search');
//            if($search_filter != "")
//            {
//                $clients = Client::where('name', 'LIKE', '%'.$search_filter.'%')->orWhere('email', 'LIKE', '%'.$search_filter.'%')
//                    ->orWhere('phone', 'LIKE', '%'.$search_filter.'%')->orWhere('phone_2', 'LIKE', '%'.$search_filter.'%')->orWhere('address', 'LIKE', '%'.$search_filter.'%')->where('hide', '=', 0)->get();
//                foreach ($clients as $client)
//                {
//                    $search_filter_clients[] = $client->id;
//                }
//            }
//        }

        $selected_orders = array();
        if (Input::get('products')) {
            $selected_product = Input::get('products');
            if (count($selected_product) > 0) {
                $product_orders = SellOrderItem::whereIn('product', $selected_product)->pluck('order')->toArray();
                $selected_orders = $product_orders;
            }
        }

        if (Input::get('tags')) {
            $selected_tags = Input::get('tags');
            if (count($selected_tags) > 0) {
                $tags_orders = SellOrderTag::whereIn('tag_id', $selected_tags)->pluck('order_id')->toArray();
                if (count($selected_product) > 0) {
                    $selected_orders = array_intersect($selected_orders, $tags_orders);
                } else {
                    $selected_orders = $tags_orders;
                }
            }
        }

        if ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0)) {
            $orders = $orders->whereIn('id', $selected_orders);
        }
        if ($search_filter != '') {
            $orders = $orders->whereIn('client', $search_filter_clients);
        }
        if (Input::get('from_date')) {
            $from_date = Input::get('from_date');
        }
        if (Input::get('to_date')) {
            $to_date = Input::get('to_date');
        }
        if ($from_date != '' && $to_date != '') {
            $orders = $orders->whereBetween('created_at', array($from_date . " 00:00:00", $to_date . " 23:59:59"));
        } else if ($from_date == '' && $to_date != '') {
            $orders = $orders->whereDate('created_at', '<=', $to_date);
        } else if ($from_date != '' && $to_date == '') {
            $orders = $orders->whereDate('created_at', '>=', $from_date);
        }

        if (Input::get('status')) {
            $selected_status = Input::get('status');
        }

        if (count($selected_status) == 0 || ($selected_status[0] == '' && count($selected_status) == 1)) {
            $orders = $orders->whereNotIn('status', [0, 1, 6, 8]);
        } else if (in_array('All', $selected_status)) {

        } else {
            $orders = $orders->whereIn('status', $selected_status);
        }
        if (Input::get('client')) {
            $selected_client = Input::get('client');
            if ($selected_client > 0) {
                $orders = $orders->where('client', '=', $selected_client);
            }
        }
        if (Input::get('admin')) {
            $selected_admin = Input::get('admin');
        }
        if (in_array('All', $selected_admin)) {

        } else if (count($selected_admin) > 0 && $selected_admin[0] != '') {
            $orders = $orders->whereIn('delivered_by', $selected_admin);
        }

        if (Input::get('moderators')) {
            $selected_moderator = Input::get('moderators');
        }
        if (in_array('All', $selected_moderator)) {

        } else if (count($selected_moderator) > 0 && $selected_moderator[0] != '') {
            $orders = $orders->whereIn('added_by', $selected_moderator);
        }

        if (Input::get('order_number')) {
            $selected_order = explode(' ', Input::get('order_number'));
        }

        if (count($selected_order) > 0 && $selected_order[0] != '') {
            $orders = $orders->where(function ($query) use ($selected_order) {
                $query->whereIn('order_number', $selected_order)->orWhereIn('shipping_number', $selected_order);
            });
        }
//        if(Input::get('city'))
//        {
//            $selected_city = Input::get('city');
//            if($selected_city > 0)
//            {
//                $orders = $orders->where('city', '=', $selected_city);
//            }
//        }
        if (Input::get('orders_per_page')) {
            $orders_per_page = Input::get('orders_per_page');
        }
        if ($orders_per_page > 0) {
            $orders = $orders->orderBy('id', 'desc')->paginate($orders_per_page);
        } else {
            $orders = $orders->orderBy('id', 'desc')->with(['client_info', 'status_info', 'city_info', 'itemsq', 'delivery_info', 'notes', 'time_lines'])->get();

        }

        $delete_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order')) {
            $delete_selling_order = true;
        }

        $edit_selling_orders = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders')) {
            $edit_selling_orders = true;
        }

        $add_selling_order = false;
        if (permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            $add_selling_order = true;
        }

        return view('admin.pages.selling_order.search_tables')->with(['orders' => $orders, 'statuss' => $statuss,
            'selected_order' => $selected_order,

            'delete_selling_order' => $delete_selling_order, 'edit_selling_orders' => $edit_selling_orders,
            'add_selling_order' => $add_selling_order,]);
    }

    /// For Reps Who Cannot See All Orders
    public function delivery()
    {
        if (Auth::guard('admin')->user()->position == 2 && permission_checker(Auth::guard('admin')->user()->id, 'view_all_selling_orders')) {
            abort('404');
        }
        $orders = SellOrder::where('hide', '=', 0);
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        $clients = Client::where('hide', '=', 0)->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $admins = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $search_filter = "";
        $selected_day = 0;
        $selected_year = 0;
        $selected_month = 0;
        $selected_status = "";
        $selected_client = 0;
        $selected_admin = 0;
        $selected_city = 0;
        $selected_product = 0;
        $selected_tags = 0;
        //$orders_per_page = 50;
        $orders_per_page = 0;
        $search_filter_clients = array();
        if (Input::get('search')) {
            $search_filter = Input::get('search');
            if ($search_filter != "") {
                $clients = Client::where('name', 'LIKE', '%' . $search_filter . '%')->orWhere('email', 'LIKE', '%' . $search_filter . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search_filter . '%')->orWhere('address', 'LIKE', '%' . $search_filter . '%')->where('hide', '=', 0)->get();
                foreach ($clients as $client) {
                    $search_filter_clients[] = $client->id;
                }
            }
        }

        $selected_orders = array();
        if (Input::get('products')) {
            $selected_product = Input::get('products');
            if (count($selected_product) > 0) {
                $product_orders = SellOrderItem::whereIn('product', $selected_product)->pluck('order')->toArray();
                $selected_orders = $product_orders;
            }
        }

        if (Input::get('tags')) {
            $selected_tags = Input::get('tags');
            if (count($selected_tags) > 0) {
                $tags_orders = SellOrderTag::whereIn('tag_id', $selected_tags)->pluck('order_id')->toArray();
                if (count($selected_product) > 0) {
                    $selected_orders = array_intersect($selected_orders, $tags_orders);
                } else {
                    $selected_orders = $tags_orders;
                }
            }
        }

        if ((Input::get('products') && count(Input::get('products')) > 0) || (Input::get('tags') && count(Input::get('tags')) > 0)) {
            $orders = $orders->whereIn('id', $selected_orders);
        }

        if ($search_filter != '') {
            $orders = $orders->whereIn('client', $search_filter_clients);
        }
        if (Input::get('day')) {
            $selected_day = Input::get('day');
            if ($selected_day > 0) {
                $orders = $orders->whereDay('created_at', '=', $selected_day);
            }
        }
        if (Input::get('year')) {
            $selected_year = Input::get('year');
            if ($selected_year > 0) {
                $orders = $orders->whereYear('created_at', '=', $selected_year);
            }
        }
        if (Input::get('month')) {
            $selected_month = Input::get('month');
            if ($selected_month > 0) {
                $orders = $orders->whereMonth('created_at', '=', $selected_month);
            }
        }
        $orders = $orders->whereIn('status', [5, 7]);
        if (Input::get('client')) {
            $selected_client = Input::get('client');
            if ($selected_client > 0) {
                $orders = $orders->where('client', '=', $selected_client);
            }
        }
        $orders = $orders->where('delivered_by', '=', Auth::guard('admin')->user()->id);
        if (Input::get('city')) {
            $selected_city = Input::get('city');
            if ($selected_city > 0) {
                $orders = $orders->where('city', '=', $selected_city);
            }
        }
        if (Input::get('orders_per_page')) {
            $orders_per_page = Input::get('orders_per_page');
        }
        if ($orders_per_page > 0) {
            $orders = $orders->orderBy('id', 'desc')->paginate($orders_per_page);
        } else {
            $orders = $orders->orderBy('id', 'desc')->get();

        }

        return view('admin.pages.selling_order.delivery')->with(['orders' => $orders, 'statuss' => $statuss, 'selected_day' => $selected_day,
            'selected_year' => $selected_year, 'selected_month' => $selected_month, 'selected_status' => $selected_status, 'clients' => $clients,
            'selected_client' => $selected_client, 'cities' => $cities, 'selected_city' => $selected_city, 'orders_per_page' => $orders_per_page
            , 'selected_admin' => $selected_admin, 'admins' => $admins, 'products' => $products, 'selected_product' => $selected_product, 'search_filter' => $search_filter,
            'selected_tags' => $selected_tags]);
    }

    /// For Reps Who Cannot See All Orders    
    public function reps_delivery()
    {
        $orders = SellOrder::where('hide', '=', 0)->whereIn('status', [4, 5, 7]);
        $all_orders = SellOrder::where('hide', '=', 0)->whereIn('status', [4, 5, 7]);
        $admins = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $selected_admin = array();
        if (Input::get('admin')) {
            $selected_admin = Input::get('admin');
        }
        if (Auth::guard('admin')->user()->position == 2) {
            $orders = $orders->where('delivered_by', '=', Auth::guard('admin')->user()->id);
            $all_orders = $all_orders->where('delivered_by', '=', Auth::guard('admin')->user()->id);
        } else {
            if (count($selected_admin) > 0 && $selected_admin[0] != '' && !in_array('All', $selected_admin)) {
                $orders = $orders->whereIn('delivered_by', $selected_admin);
                $all_orders = $all_orders->whereIn('delivered_by', $selected_admin);
            }
        }
        $all_orders = $all_orders->get();
        $repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $orders = $orders->orderBy('status')->orderBy('id', 'desc')->paginate(100);

        return view('admin.pages.selling_order.reps_delivery')->with(['repps' => $repps, 'orders' => $orders, 'selected_admin' => $selected_admin, 'admins' => $admins, 'all_orders' => $all_orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $first_box_id = "First_ITEM";
        $orders = SellOrder::where('id', '>', 177666)->get()->count();
        $orders = $orders + 185300;

        $order = new SellOrder;
        $order->hide = 1;
        $order->order_number = $orders;
        $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));
        $order->client = 0;
        $order->city = 0;
        $order->address = '';
        $order->total_price = 0;
        $order->note = '';
        $order->shipping_fees = 0;
        $order->location = '';
        $order->added_by = Auth::guard('admin')->user()->id;
        $order->save();

        $cats = OrderCategory::where('hide', '=', 0)->get();
        $tags = OrderTag::get();
        $admins = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();


        return view('admin.pages.selling_order.create')->with(['products' => $products, 'first_box_id' => $first_box_id, 'order_number' => $orders, 'order' => $order,
            'cats' => $cats, 'admins' => $admins, 'tags' => $tags]);
    }

    public function create2()
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $first_box_id = "First_ITEM";

        $orders = SellOrder::where('id', '>', 177666)->get()->count();
        $orders = $orders + 185300;
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $order = new SellOrder;
        $order->hide = 1;
        $order->order_number = "TEST-0000-V2";
        $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));
        $order->client = 0;
        $order->city = 0;
        $order->address = '';
        $order->total_price = 0;
        $order->note = '';
        $order->shipping_fees = 0;
        $order->location = '';
        $order->added_by = Auth::guard('admin')->user()->id;
        $order->save();
        $cats = OrderCategory::where('hide', '=', 0)->get();
        $admins = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        return view('admin.pages.selling_order.create2')->with(['products' => $products, 'first_box_id' => $first_box_id, 'order_number' => $orders, 'order' => $order, 'cities' => $cities,
            'cats' => $cats, 'admins' => $admins]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $arr_request = $request->all();
        $validator = Validator::make($request->all(), [
            'client' => 'required',
            'tag_id' => 'required',
            'payment_status' => 'required|in:paid,not_paid,partly_paid',
        ],
            [
                'client.required' => 'Please Find This Order Client',
                'tag_id.required' => 'Please Choose Order Tags'
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
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }
            $validator = Validator::make($request->all(), [
                'location' => 'url|nullable',
                'address' => 'required',
                'city' => 'required',
                'product' => 'required',
                'qty.*' => 'required|integer',
                'color.*' => 'required',
                'size.*' => 'required',
                'price.*' => 'required|numeric|min:0',
                'ship_price' => 'required|numeric|min:0'
            ],
                [
                    'location.url' => 'Please Enter Valid Location',
                    'address.required' => 'Please Enter Order Address',
                    'city.required' => 'Please Enter Order City',
                    'product.required' => 'Please Choose All Order Items',
                    'qty.*.required' => 'Enter Each Item Quantity',
                    'qty.*.integer' => 'Item Quantity Must Be Integer',
                    'color.*.required' => 'You Must Choose Color For Each Product Has Colors',
                    'size.*.required' => 'You Must Choose Size For Each Product Has Sizes',
                    'price.*.required' => 'You Must Enter Price For Each Product',
                    'price.*.numeric' => 'Product Price Must Be Number',
                    'price.*.min' => 'Product Price Min. Is 0',
                    'ship_price.required' => 'You Must Enter Shipping Price',
                    'ship_price.numeric' => 'Shipping Price Must Be Number',
                    'ship_price.min' => 'Shipping Price Min. Is 0'
                ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            } else if (count($request->order_item) != count($request->product)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Product In Each Order Item']);
            } else if (count($request->order_item) != count($request->color)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Color For Each Product Has Colors']);
            } else if (count($request->order_item) != count($request->size)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Size For Each Product Has Sizes']);
            }
            // $order = SellOrder::where('id', '!=', $request->id)->where('order_number', $request->order_number_xx)->first();
            // if($order !== NULL)
            // {
            //     return response()->json(['success' => false, 'errors'=>'Another Order Exists With Same Order Number']);
            // }
            if ($request->client > 0) {
                $client_id = $request->client;
                $client = Client::find($request->client);

                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->phone_2 = $request->phone_2;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();

            } else {
                $client = new Client;
                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->phone_2 = $request->phone_2;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();
                $client_id = $client->id;
            }
            $city_info = City::findorfail($request->city);
            $order_number = $request->order_number;
            $order = SellOrder::where('id', '!=', $request->order_num_id)->where('order_number', $order_number)->first();
            if ($order !== NULL) {
                $orders = SellOrder::where('id', '>', 177666)->get()->count();
                $order_number = $orders + 185300;
                $cat = OrderCategory::where('id', '=', $request->order_category)->first();

                $order = new SellOrder;
                $order->hide = 1;
                $order->order_number = $order_number . $cat->order_symbol;
                $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));
                $order->client = 0;
                $order->city = 0;
                $order->address = '';
                $order->total_price = 0;
                $order->note = '';
                $order->shipping_fees = 0;
                $order->location = '';
                $order->added_by = Auth::guard('admin')->user()->id;
            } else {
                $order = SellOrder::where('id', $request->order_num_id)->first();
            }


            $order->shipping_number = $request->shipping_number;
            $order->hide = 0;
            $order->order_number = $request->order_number;
            $order->order_category = $request->order_category;
            if ($request->shipping_date != '') {
                $order->created_at = $request->shipping_date . " " . now()->toTimeString();
            }
            $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));
            $order->client = $client->id;
            $order->city = $request->city;
            $order->address = $request->address;
            $order->total_price = 0;
            $order->note = $request->order_note;
            $order->shipping_fees = $request->ship_price;
            $order->location = $request->location;
            $order->added_by = $request->moderator;
            $order->save();
            $order->client_info->update_client_type();
            $total = 0;
            for ($i = 0; $i < count($request->order_item); $i++) {
                $product = Product::find($request->product[$i]);
                if ($product !== NULL && $product->hide == 0) {
                    $item = new SellOrderItem;
                    $item->order = $order->id;
                    $item->product = $request->product[$i];
                    $item->color = $request->color[$i];
                    $item->size = $request->size[$i];
                    $item->note = $request->note[$i];
                    $item->qty = $request->qty[$i];
                    $item->price = $request->price[$i];
                    $item->save();
                    $total = $total + ($item->qty * $item->price);
                    // get_product_qty($item->product, $item->color, $item->size);  

                    update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'add', null, null);
//                        $inventory=Inventory::where('product',$item->product)->where('color',$item->color)->where('size',$item->size)->first();
//                        if ($inventory){
//                            $qty=$inventory->bought-$inventory->sold;
//                            $this->notify($qty,$inventory->product,$inventory->color,$inventory->size);
//                        }
                }
            }
            $order->total_price = $total;
            $order->save();
            if ($order->order_number == '') {
                $order->order_number = $order->id - 177666 + 185300;
                $order->save();
            }
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Created New Selling Order";
            $event->from_status = $order->status;
            $event->to_status = $order->status;
            $event->save();


            if (count($request->tag_id) > 0) {
                for ($i = 0; $i < count($request->tag_id); $i++) {

                    $oo = new SellOrderTag;
                    $oo->order_id = $order->id;
                    $oo->tag_id = $request->tag_id[$i];
                    $oo->save();
                }
            }
            $payment_amount = 0;
            $payment_status = $request->payment_status;
            if ($payment_status == 'paid') {
                $payment_amount = $order->total_price + $order->shipping_fees;
            } elseif ($payment_status == 'partly_paid') {
                $payment_amount = $request->payment_amount;
            } else {
            }

            $order->payment_amount = $payment_amount;
            $order->payment_status = $payment_status;
            $order->save();

            return response()->json(['success' => true, 'message' => "Order Has Been Placed Successfully"]);
        }
    }


    public function form_data(Request $request)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $arr_request = $request->all();
        if ($request->city > 0) {
            $city = City::findorfail($request->city);
        }
        $cat = OrderCategory::where('id', '=', $request->order_category)->first();
        ?>
        <div class="kt-wizard-v1__review-item">
            <div class="kt-wizard-v1__review-content">
                <p><b>Order Number : </b><?php echo $request->order_number; ?></p>
                <p><b>Name : </b><?php echo $request->name; ?></p>
                <p><b>Phone : </b><?php echo $request->phone; ?></p>
                <p><b>Phone 2 : </b><?php echo $request->phone_2; ?></p>
                <p><b>Address : </b><?php echo $request->address; ?></p>
                <p><b>City : </b><?php if ($request->city > 0) {
                        echo $city->title;
                    } ?></p>

                <?php
                $total = 0;
                for ($i = 0; $i < count($request->order_item); $i++) {
                    $x = $i + 1;
                    if (count($request->product >= $i)) {
                        $product = Product::find($request->product[$i]);
                        if ($request->color[$i] > 0) {
                            $color = Color::findorfail($request->color[$i]);
                        }
                        if ($request->size[$i] > 0) {
                            $size = Size::findorfail($request->size[$i]);
                        }
                        if ($product !== NULL && $product->hide == 0) {
                            ?>
                            <p><?php echo $product->title;
                                if ($request->color[$i] > 0) {
                                    echo " - " . $color->title;
                                }
                                if ($request->size[$i] > 0) {
                                    echo " - " . $size->title;
                                } ?>
                                - <?php echo $request->qty[$i]; ?> * <?php echo $request->price[$i]; ?>
                                = <?php echo $request->qty[$i] * $request->price[$i]; ?>
                            </p>
                            <?php
                            $total = $total + ($request->qty[$i] * $request->price[$i]);
                        }
                    }
                }
                ?>
                <p><b>Shipping Price : </b><?php echo $request->ship_price; ?> EGP</p>
                <p><b>Total : </b><?php echo $total + $request->ship_price; ?> EGP</p>
                <p><b>Order Note : </b><?php echo $request->order_note; ?></p>

            </div>
        </div>
        <?php
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
        $order = SellOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        foreach ($order->items as $item) {
            $totals = $totals + ($item->qty * $item->price);
        }
        return view('admin.pages.selling_order.show')->with(['order' => $order, 'statuss' => $statuss, 'totals' => $totals]);
    }

    public function invoice($id)
    {
        $totals = 0;
        $orders = SellOrder::findorfail($id);
        if ($orders->hide == 1) {
            return abort(404);
        }
        $statuss = OrderStatus::where('hide', '=', 0)->get();
        foreach ($orders->items as $item) {
            $totals = $totals + ($item->qty * $item->price);
        }
        $orders->total_price = $totals;
        $orders->save();
        return view('admin.pages.selling_order.invoice')->with(['orders' => $orders, 'statuss' => $statuss, 'totals' => $totals, 'store_invoice' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders')) {
            abort('404');
        }
        $order = SellOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $products = Product::where('hide', '=', 0)->orderBy('title')->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();

        $tags_ids = SellOrderTag::where('order_id', $id)->pluck('tag_id')->toArray();
        $all_tags = implode(',', OrderTag::whereIn('id', $tags_ids)->pluck('title')->toArray());
        $tags = OrderTag::get();
        $admins = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();


        return view('admin.pages.selling_order.edit')->with(['order' => $order, 'products' => $products, 'cities' => $cities,
            'all_tags' => $all_tags, 'tags' => $tags, 'tag_ides' => $tags_ids, 'admins' => $admins]);
    }


    public function selling_reorder($id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $order = SellOrder::findorfail($id);
        if ($order->hide == 1) {
            return abort(404);
        }
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        $cities = City::where('hide', '=', 0)->orderBy('title')->get();
        $reorders = SellOrder::where('order_id', '=', $id)->where('hide', '=', 0)->get();
        $order_number = "Re";
        if ($reorders->count() == 0) {
            $order_number .= "";
        } else {
            $order_number .= $reorders->count() + 1;
        }
        $order_number .= "-" . $order->order_number;

        return view('admin.pages.selling_order.reorder')->with(['order' => $order, 'products' => $products, 'cities' => $cities, "reorders" => $reorders, "order_number" => $order_number]);
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
        if (!permission_checker(Auth::guard('admin')->user()->id, 'edit_selling_orders')) {
            abort('404');
        }
        $order = SellOrder::findorfail($id);
        $old_model_status = OrderStatus::findOrFail($order->status);

        if ($order->hide == 1) {
            return abort(404);
        }
        $arr_request = $request->all();
        $validator = Validator::make($request->all(), [
            'client' => 'required',
            'tag_id' => 'required',
            'payment_status' => 'required|in:paid,not_paid,partly_paid',
            'moderator' => 'nullable|exists:admins,id',

        ],
            [
                'client.required' => 'Please Find This Order Client',
                'tag_id.required' => 'Please Choose Order Tags'
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
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }
            $validator = Validator::make($request->all(), [
                'location' => 'url|nullable',
                'address' => 'required',
                'city' => 'required',
                'product' => 'required',
                'qty.*' => 'required|integer',
                'color.*' => 'required',
                'size.*' => 'required',
                'price.*' => 'required|numeric|min:0',
                'ship_price' => 'required|numeric|min:0'
            ],
                [
                    'location.url' => 'Please Enter Valid Location',
                    'address.required' => 'Please Enter Order Address',
                    'city.required' => 'Please Enter Order City',
                    'product.required' => 'Please Choose All Order Items',
                    'qty.*.required' => 'Enter Each Item Quantity',
                    'qty.*.integer' => 'Item Quantity Must Be Integer',
                    'color.*.required' => 'You Must Choose Color For Each Product Has Colors',
                    'size.*.required' => 'You Must Choose Size For Each Product Has Sizes',
                    'price.*.required' => 'You Must Enter Price For Each Product',
                    'price.*.numeric' => 'Product Price Must Be Number',
                    'price.*.min' => 'Product Price Min. Is 0',
                    'ship_price.required' => 'You Must Enter Shipping Price',
                    'ship_price.numeric' => 'Shipping Price Must Be Number',
                    'ship_price.min' => 'Shipping Price Min. Is 0'
                ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            } else if (count($request->order_item) != count($request->product)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Product In Each Order Item']);
            } else if (count($request->order_item) > 0 && (!$request->has('color') || !$request->has('size'))) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Product Color & Size In Each Order Item']);
            } else if (count($request->order_item) != count($request->color)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Color For Each Product Has Colors']);
            } else if (count($request->order_item) != count($request->size)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Size For Each Product Has Sizes']);
            }
            if ($request->client > 0) {
                $client_id = $request->client;
                $client = Client::find($request->client);

                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();
            } else {
                $client = new Client;
                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();
                $client_id = $client->id;
            }
            // $order = SellOrder::where('id', '!=', $id)->where('order_number', $request->order_number)->first();
            // if($order !== NULL)
            // {
             //     return response()->json(['success' => false, 'errors'=>'Another Order Exists With Same Order Number']);
            // }

            if (permission_checker(Auth::guard('admin')->user()->id, 'update_moderator')) {
                if ($request->moderator) {
                    $order->added_by = $request->moderator;
                }
            }

            $city_info = City::findorfail($request->city);
            $order->order_number = $request->order_number;
            $order->shipping_number = $request->shipping_number;
            if ($request->shipping_date != '') {
                $order->created_at = $request->shipping_date . " " . now()->toTimeString();
            }
            $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));
            $order->client = $client->id;
            $order->total_price = 0;
            $order->city = $request->city;
            $order->address = $request->address;
            $order->note = $request->order_note;
            $order->shipping_fees = $request->ship_price;
            $order->location = $request->location;
            $order->save();
            $total = 0;

            for ($i = 0; $i < count($request->order_item); $i++) {
                $total = $total + ($request->qty[$i] * $request->price[$i]);
            }
            $order->total_price = $total;
            $order->save();

            $changed = false;
            if (in_array('0', $request->order_item)) {
                $changed = true;
            } else if (count($request->order_item_id) != count($order->items)) {
                $changed = true;
            } else {
                $iop = 0;
                $itemasd = SellOrderItem::where('order', $order->id)->get();
                foreach ($itemasd as $item) {
                    if ($item->product != $request->product[$iop]) {
                        $changed = true;
                    } else if ($item->qty != $request->qty[$iop]) {
                        $changed = true;
                    } else if ($item->color != $request->color[$iop]) {
                        $changed = true;
                    } else if ($item->size != $request->size[$iop]) {
                        $changed = true;
                    }
                    $iop++;
                }
            }

            if ($changed) {
                $ffx = Fulfillment::where('order', $id)->get();
                foreach ($ffx as $ff) {
                    $ff->delete();
                }
                $old_status_num_x = $order->status;
                $order->status = 1;
                $order->save();

                $xx = array();
                $xqty = array();

                $allOldItem = SellOrderItem::where('order', $order->id)->get();

                foreach ($allOldItem as $oldItem) {
                    update_sold_inventory_after_update($oldItem->product, $oldItem->color, $oldItem->size, $oldItem->qty, $old_model_status);

                }

                $itemsasd = SellOrderItem::where('order', $order->id)->whereNotIn('id', $request->order_item_id)->get();
                foreach ($itemsasd as $item) {
                    $item->delete();
                    // get_product_qty($item->product, $item->color, $item->size);

                    // if($old_status_num_x != 0 && $old_status_num_x != 1 && $old_status_num_x != 8)
                    // {
                    //     $time_line = new ProductTimeline;
                    //     $time_line->product = $item->product;
                    //     $time_line->color = $item->color;
                    //     $time_line->size = $item->size;
                    //     $time_line->admin = Auth::guard('admin')->user()->id;
                    //     $time_line->order = $order->id;
                    //     $time_line->order_type = 1;
                    //     $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
                    //     $time_line->text = " Removed (".$item->qty.") From Selling Order";
                    //     $time_line->save();
                    // }
                }
                $total = 0;
                for ($i = 0; $i < count($request->order_item); $i++) {
                    $product = Product::find($request->product[$i]);
                    if ($product !== NULL && $request->order_item_id[$i] == 0) {
                        $item = new SellOrderItem;
                        $item->order = $order->id;
                        $item->product = $request->product[$i];
                        $item->color = $request->color[$i];
                        $item->size = $request->size[$i];
                        $item->note = $request->note[$i];
                        $item->qty = $request->qty[$i];
                        $item->price = $request->price[$i];
                        $item->save();
                        // get_product_qty($item->product, $item->color, $item->size);
                        $total = $total + ($item->qty * $item->price);
                    } elseif ($product !== NULL && $product->hide == 0 && $request->order_item_id[$i] > 0) {
                        $item = SellOrderItem::findorfail($request->order_item_id[$i]);
                        $item->order = $order->id;
                        $item->product = $request->product[$i];
                        $item->color = $request->color[$i];
                        $item->size = $request->size[$i];
                        $item->note = $request->note[$i];
                        $item->qty = $request->qty[$i];
                        $item->price = $request->price[$i];
                        $item->save();

                        $total = $total + ($item->qty * $item->price);
                    }

                    update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'add', null, null);

                }
                $order->total_price = $total;
                $order->save();
                if ($order->order_number == '') {
                    $order->order_number = $order->id;
                    $order->save();
                }
            } else {
                $total = 0;
                for ($i = 0; $i < count($request->order_item); $i++) {
                    $item = SellOrderItem::findorfail($request->order_item_id[$i]);
                    $item->order = $order->id;
                    $item->product = $request->product[$i];
                    $item->color = $request->color[$i];
                    $item->size = $request->size[$i];
                    $item->note = $request->note[$i];
                    $item->qty = $request->qty[$i];
                    $item->price = $request->price[$i];
                    $item->save();


                    $total = $total + ($item->qty * $item->price);
                }
                $order->total_price = $total;
                $order->save();
                if ($order->order_number == '') {
                    $order->order_number = $order->id;
                    $order->save();
                }
            }
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Updated Selling Order";
            $event->from_status = $old_model_status->id;
            $event->to_status = $order->status;
            $event->save();

            SellOrderTag::where('order_id', $order->id)->delete();

            if (count($request->tag_id) > 0) {
                for ($i = 0; $i < count($request->tag_id); $i++) {

                    $oo = new SellOrderTag;
                    $oo->order_id = $order->id;
                    $oo->tag_id = $request->tag_id[$i];
                    $oo->save();
                }
            }


            $payment_amount = 0;
            $payment_status = $request->payment_status;
            if ($payment_status == 'paid') {
                $payment_amount = $order->total_price + $order->shipping_fees;
            } elseif ($payment_status == 'partly_paid') {
                $payment_amount = $request->payment_amount;
            } else {
            }

            $order->payment_amount = $payment_amount;
            $order->payment_status = $payment_status;
            $order->save();

            return response()->json(['success' => true, 'message' => "Order Has Been Updated Successfully"]);
        }
    }

    public function save_selling_reorder(Request $request, $id)
    {
        if (!permission_checker(Auth::guard('admin')->user()->id, 'add_selling_order')) {
            abort('404');
        }
        $oldorder = SellOrder::findorfail($id);
        $order = new SellOrder;
        if ($oldorder->hide == 1) {
            return abort(404);
        }
        $arr_request = $request->all();
        $validator = Validator::make($request->all(), [
            'client' => 'required',
            'payment_status' => 'required|in:paid,not_paid,partly_paid',

        ],
            [
                'client.required' => 'Please Find This Order Client'
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
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => ['nullable', 'email', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) use ($request) {
                        $query->where('hide', '=', 0)->where('id', '!=', $request->client);
                    })],
                    'address' => 'required',
                    'city' => 'required'
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'email.unique' => 'This Email Address Is Registered To Another Client',
                        'email.email' => 'Please Enter Correct Email Address',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                        'address.required' => 'Please Enter Address',
                        'city.required' => 'Please Choose City'
                    ]);
                if ($validator->fails()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
                }
            }
            $validator = Validator::make($request->all(), [
                'location' => 'url|nullable',
                'address' => 'required',
                'city' => 'required',
                'product' => 'required',
                'qty.*' => 'required|integer',
                'color.*' => 'required',
                'size.*' => 'required',
                'price.*' => 'required|numeric|min:0',
                'ship_price' => 'required|numeric|min:0'
            ],
                [
                    'location.url' => 'Please Enter Valid Location',
                    'address.required' => 'Please Enter Order Address',
                    'city.required' => 'Please Enter Order City',
                    'product.required' => 'Please Choose All Order Items',
                    'qty.*.required' => 'Enter Each Item Quantity',
                    'qty.*.integer' => 'Item Quantity Must Be Integer',
                    'color.*.required' => 'You Must Choose Color For Each Product Has Colors',
                    'size.*.required' => 'You Must Choose Size For Each Product Has Sizes',
                    'price.*.required' => 'You Must Enter Price For Each Product',
                    'price.*.numeric' => 'Product Price Must Be Number',
                    'price.*.min' => 'Product Price Min. Is 0',
                    'ship_price.required' => 'You Must Enter Shipping Price',
                    'ship_price.numeric' => 'Shipping Price Must Be Number',
                    'ship_price.min' => 'Shipping Price Min. Is 0'
                ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            } else if (count($request->order_item) != count($request->product)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Product In Each Order Item']);
            } else if (count($request->order_item) > 0 && (!$request->has('color') || !$request->has('size'))) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Product Color & Size In Each Order Item']);
            } else if (count($request->order_item) != count($request->color)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Color For Each Product Has Colors']);
            } else if (count($request->order_item) != count($request->size)) {
                return response()->json(['success' => false, 'errors' => 'You Must Choose Size For Each Product Has Sizes']);
            }
            if ($request->client > 0) {
                $client_id = $request->client;
                $client = Client::find($request->client);

                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();
            } else {
                $client = new Client;
                $client->name = $request->name;
                $client->phone = $request->phone;
                $client->email = $request->email;
                $client->address = $request->address;
                $client->city = $request->city;
                $client->location = $request->location;
                $client->save();
                $client_id = $client->id;
            }
            $city_info = City::findorfail($request->city);
            $order->order_number = $request->order_number;

            if ($request->shipping_date != '') {
                $order->created_at = $request->shipping_date . " 00:00:00";
            }
            $order->shipping_date = date('Y-m-d', strtotime('tomorrow'));

            $order->client = $client->id;
            $order->total_price = 0;
            $order->city = $request->city;
            $order->address = $request->address;
            $order->note = $request->order_note;
            $order->shipping_fees = $request->ship_price;
            $order->location = $request->location;
            $order->order_id = $oldorder->id;
            $order->status = 1;
            $order->added_by = $oldorder->added_by;
            $order->save();
            $total = 0;
            for ($i = 0; $i < count($request->order_item); $i++) {
                $product = Product::find($request->product[$i]);
                if ($product !== NULL && $product->hide == 0) {
                    $item = new SellOrderItem;
                    $item->order = $order->id;
                    $item->product = $request->product[$i];
                    $item->color = $request->color[$i];
                    $item->size = $request->size[$i];
                    $item->note = $request->note[$i];
                    $item->qty = $request->qty[$i];
                    $item->price = $request->price[$i];
                    $item->save();
//                    get_product_qty($item->product, $item->color, $item->size);
                    $total = $total + ($item->qty * $item->price);
                    update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'add', null, null);

                }
            }
            $order->total_price = $total;
            $order->save();
            // if($order->order_number == '')
            // {
            //     $order->order_number = $order->id;
            //     $order->save();
            // }
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Created Selling Order As Reorder From " . $oldorder->order_number;
            $event->save();

            $payment_amount = 0;
            $payment_status = $request->payment_status;
            if ($payment_status == 'paid') {
                $payment_amount = $order->total_price + $order->shipping_fees;
            } elseif ($payment_status == 'partly_paid') {
                $payment_amount = $request->payment_amount;
            } else {
            }

            $order->payment_amount = $payment_amount;
            $order->payment_status = $payment_status;
            $order->save();

            return response()->json(['success' => true, 'message' => "Order Has Been Created Successfully"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {

        if (!permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order')) {
            abort('404');
        }
        $order = SellOrder::findorfail($id);

        $new_model_status = OrderStatus::findorfail(12);
        $old_model_status = OrderStatus::findorfail($order->status);

        $order->hide = 1;
        $order->save();
        $ffx = Fulfillment::where('order', $id)->delete();

        foreach ($order->items as $item) {

            $item->hide = 1;
            $item->save();
            // get_product_qty($item->product, $item->color, $item->size);

            if ($new_model_status->id != $old_model_status->id) {
                update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'convert', $old_model_status, $new_model_status);
            }


        }
        $event = new TimeLine;
        $event->admin = Auth::guard('admin')->user()->id;
        $event->order = $order->id;
        $event->order_type = 1;
        $event->text = " Has Deleted Selling Order";
        $event->from_status = $order->status;
        $event->to_status = $order->status;
        $event->save();

        if ($request->type)
            return response()->json(['status' => true]);

        return redirect()->back();
    }

    public function selling_order_collect_date(Request $request)
    {
        $order = SellOrder::findorfail($request->num);
        $order->collected_date = $request->status . " 00:00:00";
        $order->save();
    }

    public function selling_order_status(Request $request)
    {
        $order = SellOrder::findorfail($request->num);
        $new_model_status = OrderStatus::findorfail($request->status);
        $old_model_status = OrderStatus::findorfail($order->status);
        $old_status_title = $order->status;
        $old_status_id = $order->status;
        if ($order->status == 0) {
            $old_status = "Pending";
        } else {
            $old_status = OrderStatus::findorfail($order->status);
            $old_status = $old_status->title;
        }
        $new_status_i = OrderStatus::findorfail($request->status);
        $new_status = $new_status_i->title;

        $order->status = $request->status;
        if ($order->status == 6) {
            $order->collected_date = date('Y-m-d H:i:s A');
        } else if ($old_status_title == 6) {
            $order->collected_date = NULL;
        }
        $order->save();
        $nn = array(8, 10, 12);
        foreach ($order->itemsq as $item) {
            // get_product_qty($item->product, $item->color, $item->size);
            // $time_line = new ProductTimeline;
            // $time_line->product = $item->product;
            // $time_line->color = $item->color;
            // $time_line->size = $item->size;
            // $time_line->admin = Auth::guard('admin')->user()->id;
            // $time_line->order = $order->id;
            // $time_line->order_type = 1;
            // $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
            // $time_line->text = " Changed Order Status From (".$old_status.") To (".$new_status.")";
            // $time_line->save();


            if ($old_status != $new_status) {

                update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'convert', $old_model_status, $new_model_status);

            }


        }
        if ($old_status != $new_status) {
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From " . $old_status . " To " . $new_status;
            $event->from_status = $old_model_status->id;
            $event->to_status = $new_model_status->id;
            $event->save();
            if ($new_status_i->mylerz == 1 && optional($order->city_info)->mylerz_shipping == 1 && $order->mylerz_barcode == '') {
                // $this->init_order($order);
                create_mylerz_order($order->id);
            }
        }
        return redirect()->back();
    }

    public function delivered_order($id)
    {
        $order = SellOrder::findorfail($id);
        $old_status_id = $order->status;
        $old_status_title = $order->status;
        if ($order->status == 0) {
            $old_status = "Pending";
            $old_status_model = OrderStatus::findorfail(1);
        } else {
            $old_status = OrderStatus::findorfail($order->status);
            $old_status = $old_status->title;
            $old_status_model = OrderStatus::findorfail($order->status);

        }
        $new_status_i = OrderStatus::findorfail(5);
        $new_status = $new_status_i->title;
        $new_status_model = OrderStatus::findorfail(5);


        $order->status = $new_status_i->id;
        $order->save();

        $nn = array(0, 1, 8);
//        foreach ($order->itemsq as $item)
//        {
//            get_product_qty($item->product, $item->color, $item->size);
//            $time_line = new ProductTimeline;
//            $time_line->product = $item->product;
//            $time_line->color = $item->color;
//            $time_line->size = $item->size;
//            $time_line->admin = Auth::guard('admin')->user()->id;
//            $time_line->order = $order->id;
//            $time_line->order_type = 1;
//            $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
//            // $time_line->text = " Returned (".$item->qty.") Pieces To Inventory From Selling Order";
//            $time_line->text = " Changed Order Status From (".$old_status.") To (".$new_status.")";
//            $time_line->save();
//
//        }

        if ($old_status != $new_status) {
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From " . $old_status . " To " . $new_status;
            $event->from_status = $old_status_model->id;
            $event->to_status = $new_status_model->id;
            $event->save();
        }
        return redirect()->back();
    }

    public function rejected_order($id)
    {
        $order = SellOrder::findorfail($id);
        $old_status_id = $order->status;
        $old_status_title = $order->status;
        if ($order->status == 0) {
            $old_status = "Pending";
            $old_status_model = OrderStatus::findorfail(1);

        } else {
            $old_status = OrderStatus::findorfail($order->status);
            $old_status = $old_status->title;
            $old_status_model = OrderStatus::findorfail($order->status);

        }
        $new_status_i = OrderStatus::findorfail(7);
        $new_status = $new_status_i->title;
        $new_status_model = OrderStatus::findorfail(7);

        $order->status = $new_status_i->id;
        $order->collected_date = NULL;
        $order->save();

        $nn = array(0, 1, 8);
//        foreach ($order->itemsq as $item)
//        {
//            get_product_qty($item->product, $item->color, $item->size);
//            $time_line = new ProductTimeline;
//            $time_line->product = $item->product;
//            $time_line->color = $item->color;
//            $time_line->size = $item->size;
//            $time_line->admin = Auth::guard('admin')->user()->id;
//            $time_line->order = $order->id;
//            $time_line->order_type = 1;
//            $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
//            // $time_line->text = " Returned (".$item->qty.") Pieces To Inventory From Selling Order";
//            $time_line->text = " Changed Order Status From (".$old_status.") To (".$new_status.")";
//            $time_line->save();
//        }

        if ($old_status != $new_status) {
            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Changed Selling Order Status From " . $old_status . " To " . $new_status;
            $event->from_status = $old_status_model->id;
            $event->to_status = $new_status_model->id;
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
            $orders = SellOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->orderBy('city')->get();
            return view('admin.pages.selling_order.shipping_info')->with(['orders' => $orders]);
        } elseif ($type == 'Print_Invoice') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = SellOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->get();
            foreach ($orders as $order) {
                $totals = 0;
                foreach ($order->items as $item) {
                    $totals = $totals + ($item->qty * $item->price);
                }
                $order->total_price = $totals;
                $order->save();
            }
            return view('admin.pages.selling_order.invoice')->with(['orders' => $orders, 'store_invoice' => true]);
        } elseif ($type == 'Print_Mylerz_Invoice') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = SellOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->get();
            foreach ($orders as $order) {
                $totals = 0;
                foreach ($order->items as $item) {
                    $totals = $totals + ($item->qty * $item->price);
                }
                $order->total_price = $totals;
                $order->save();
            }
            return view('admin.pages.selling_order.invoice')->with(['orders' => $orders, 'store_invoice' => false]);
        } elseif ($type == 'Shipping_Products') {
            $orders = Input::get('orders');
            $orders_arr = explode(',', $orders);
            $orders = SellOrder::where('hide', '=', 0)->whereIn('id', $orders_arr)->orderBy('city')->get();
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

            return view('admin.pages.selling_order.shipping_products')->with(['orders' => $orders, 'cities' => $cities
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

            // Test update
            // $itemTypes=array_values($request->items);
            // $order=SellOrder::whereIn('id', $itemTypes)
            // ->update([
            //     'status' => $request->status,
            // ]);

            for ($iorder = 0; $iorder < count($request->items); $iorder++) {
                $id = $request->items[$iorder];
                $order = SellOrder::findorfail($id);
                $old_status_id = $order->status;
                $old_status_title = $order->status;
                if ($order->status == 0) {
                    $old_status = "Pending";
                } else {
                    $old_status = OrderStatus::findorfail($order->status);
                    $old_status = $old_status->title;
                }
                $new_status_i = OrderStatus::findorfail($request->status);
                $new_status = $new_status_i->title;

                $new_model_status = OrderStatus::findorfail($request->status);
                $old_model_status = OrderStatus::findorfail($order->status);


                if ($old_status != $new_status) {
                    $event = new TimeLine;
                    $event->admin = Auth::guard('admin')->user()->id;
                    $event->order = $order->id;
                    $event->order_type = 1;
                    $event->text = " Has Changed Selling Order Status From " . $old_status . " To " . $new_status;
                    $event->from_status = $old_model_status->id;
                    $event->to_status = $new_model_status->id;
                    $event->save();
                    if ($new_status_i->mylerz == 1 && optional($order->city_info)->mylerz_shipping == 1 && $order->mylerz_barcode == '') {
                        // $this->init_order($order);
                        create_mylerz_order($order->id);
                    }
                }
                $order->status = $request->status;

                if ($order->status == 6) {
                    $order->collected_date = date('Y-m-d H:i:s A');
                } else if ($old_status_title == 6) {
                    $order->collected_date = NULL;
                }
                $order->save();

                $nn = array(0, 1, 8);
                foreach ($order->itemsq as $item) {
                    // get_product_qty($item->product, $item->color, $item->size);
                    // $time_line = new ProductTimeline;
                    // $time_line->product = $item->product;
                    // $time_line->color = $item->color;
                    // $time_line->size = $item->size;
                    // $time_line->admin = Auth::guard('admin')->user()->id;
                    // $time_line->order = $order->id;
                    // $time_line->order_type = 1;
                    // $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
                    // // $time_line->text = " Returned (".$item->qty.") Pieces To Inventory From Selling Order";
                    // $time_line->text = " Changed Order Status From (".$old_status.") To (".$new_status.")";
                    // $time_line->save();  


                    if ($old_status != $new_status) {
                        update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'convert', $old_model_status, $new_model_status);

                    }
                }
            }
            return response()->json(['success' => true, 'message' => 'Orders Status Changed']);
        } else if ($request->type == 'Change_Tags') {
            $validator = Validator::make($request->all(), [
                'items' => 'required',
                'tags' => 'required',
            ],
                [
                    'items.required' => 'Please Choose Orders',
                    'tags.required' => 'Please Choose Tags For This Orders'
                ]);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            }


            for ($iorder = 0; $iorder < count($request->items); $iorder++) {
                $id = $request->items[$iorder];
                $order = SellOrder::findorfail($id);
                for ($j = 0; $j < count($request->tags); $j++) {
                    $tt = SellOrderTag::where('order_id', $order->id)
                        ->where('tag_id', $request->tags[$j])->exists();
                    if (!$tt) {
                        $oo = new SellOrderTag;
                        $oo->order_id = $order->id;
                        $oo->tag_id = $request->tags[$j];
                        $oo->save();
                    }
                }
            }
            return response()->json(['success' => true, 'message' => 'Orders Tags Changed']);
        } elseif ($request->type == 'Delete') {
            if (!permission_checker(Auth::guard('admin')->user()->id, 'delete_selling_order')) {
                abort('404');
            }
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = SellOrder::findorfail($id);
                $new_model_status = OrderStatus::findorfail(12);
                $old_model_status = OrderStatus::findorfail($order->status);
                $order->hide = 1;
                $order->save();

                foreach ($order->itemsq as $item) {
                    if ($new_model_status->id != $old_model_status->id) {
                        update_sold_inventory($item->product, $item->color, $item->size, $item->qty, 'convert', $old_model_status, $new_model_status);
                    }
                    $item->hide = 1;
                    $item->save();
                    // get_product_qty($item->product, $item->color, $item->size);   

                    if ($order->status != 0 && $order->status != 1 && $order->status != 8 && $order->status != 10 && $order->status != 12) {
                        // $time_line = new ProductTimeline;
                        // $time_line->product = $item->product;
                        // $time_line->color = $item->color;
                        // $time_line->size = $item->size;
                        // $time_line->admin = Auth::guard('admin')->user()->id;
                        // $time_line->order = $order->id;
                        // $time_line->order_type = 1;
                        // $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
                        // $time_line->text = " Deleted Order";
                        // // $time_line->text = " Returned (".$item->qty.") Pieces To Inventory From Selling Order";
                        // $time_line->save();
                    }
                }
                $event = new TimeLine;
                $event->admin = Auth::guard('admin')->user()->id;
                $event->order = $order->id;
                $event->order_type = 1;
                $event->text = " Has Deleted Selling Order";
                $event->from_status = $order->status;
                $event->to_status = $order->status;

                $event->save();
            }
            return response()->json(['success' => true, 'message' => 'Orders Deleted']);
        } elseif ($request->type == 'Change_REP') {
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = SellOrder::findorfail($id);

                if ($order->delivered_by == 0) {
                    $eea = " From None";
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
                    $event->order_type = 1;
                    $event->text = " Has Changed Selling Order REP" . $eea;
                    $event->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Success']);
        } else if ($request->type == 'CalculateTotalAmount') {
            $total = 0;
            for ($i = 0; $i < count($request->items); $i++) {
                $id = $request->items[$i];
                $order = SellOrder::findorfail($id);
                //$total = $total + $order->shipping_fees;
                foreach ($order->itemsq as $item) {
                    $total = $total + ($item->qty * $item->price);
                }
            }
            echo "<p class='text-center'><b>Orders Count : </b>" . count($request->items) . " Orders</p>";
            echo "<p class='text-center'><b>Orders Cost : </b>" . $total . " EGP</p>";
        }
    }

    public function notes_viewer(Request $request)
    {
        $last_note = OrderNote::where('order', '=', $request->order)->orderBy('id', 'desc')->first();
        if ($last_note !== NULL) {
            $last_viewed = OrderNoteStatus::where('admin', '=', Auth::guard('admin')->user()->id)->where('order', '=', $request->order)->orderBy('note', 'desc')->first();
            if ($last_viewed === NULL) {
                $st = new OrderNoteStatus;
                $st->order = $request->order;
                $st->note = $last_note->id;
                $st->admin = Auth::guard('admin')->user()->id;
                $st->save();
            } else {
                if ($last_note->id != $last_viewed->note) {
                    $st = new OrderNoteStatus;
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
            $order = SellOrder::findorfail($id);
            $note = new OrderNote;
            $note->order = $id;
            $note->added_by = Auth::guard('admin')->user()->id;
            $note->note = $request->note;
            $note->save();
            if ($request->has('rep')) {
                for ($i = 0; $i < count($request->rep); $i++) {
                    $a = new OrderNoteRep;
                    $a->note = $note->id;
                    $a->rep = $request->rep[$i];
                    $a->save();
                }
            }
            if ($request->has('tag')) {
                for ($i = 0; $i < count($request->tag); $i++) {
                    $a = new OrderNoteTag;
                    $a->note = $note->id;
                    $a->tag = $request->tag[$i];
                    $a->save();
                }
            }
            $st = new OrderNoteStatus;
            $st->order = $note->order;
            $st->note = $note->id;
            $st->admin = Auth::guard('admin')->user()->id;
            $st->save();

            $event = new TimeLine;
            $event->admin = Auth::guard('admin')->user()->id;
            $event->order = $order->id;
            $event->order_type = 1;
            $event->text = " Has Created Selling Order Note [" . $note->note . "]";
            $event->save();

            return response()->json(['success' => true, 'message' => "Note Has Been Added Successfully"]);
        }
    }

    public function selling_order_notes_multi(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order' => 'required',
            'note' => 'required',
        ],
            [
                'order.required' => 'Please Selected Orders',
                'note.required' => 'Please Enter Your Note',
            ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else if ($request->order == '') {
            return response()->json(['success' => false, 'errors' => 'Please Selected Orders']);
        } else {
            $orders = explode(',', $request->order);
            for ($i = 0; $i < count($orders); $i++) {
                $order = SellOrder::findorfail($orders[$i]);
                $note = new OrderNote;
                $note->order = $order->id;
                $note->added_by = Auth::guard('admin')->user()->id;
                $note->note = $request->note;
                $note->save();
                if ($request->has('rep')) {
                    for ($i = 0; $i < count($request->rep); $i++) {
                        $a = new OrderNoteRep;
                        $a->note = $note->id;
                        $a->rep = $request->rep[$i];
                        $a->save();
                    }
                }
                if ($request->has('tag')) {
                    for ($i = 0; $i < count($request->tag); $i++) {
                        $a = new OrderNoteTag;
                        $a->note = $note->id;
                        $a->tag = $request->tag[$i];
                        $a->save();
                    }
                }
                $st = new OrderNoteStatus;
                $st->order = $note->order;
                $st->note = $note->id;
                $st->admin = Auth::guard('admin')->user()->id;
                $st->save();

                $event = new TimeLine;
                $event->admin = Auth::guard('admin')->user()->id;
                $event->order = $order->id;
                $event->order_type = 1;
                $event->text = " Has Created Selling Order Note [" . $note->note . "]";
                $event->save();
            }

            return response()->json(['success' => true, 'message' => "Note Has Been Added Successfully"]);
        }
    }

    public function all_notes()
    {
        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Order Notes') && Auth::guard('admin')->user()->position == 1) {
            abort(404);
        }
        $nteo = array();
        $result = array();
        if (permission_checker(Auth::guard('admin')->user()->id, 'all_order_notes')) {
            $all_notes = OrderNote::where('id', '>', 0);
        } else if (permission_checker(Auth::guard('admin')->user()->id, 'order_notes')) {
            $result = OrderNoteRep::where('rep', Auth::guard('admin')->user()->id)->pluck('note')->toArray();
            $all_notes = OrderNote::where('id', '>', 0);
        }

        $selected_admin[] = "All";
        $selected_tags[] = "All";
        $from_date = "";
        $to_date = "";

        if (Input::get('from_date')) {
            $from_date = Input::get('from_date');
        }
        if (Input::get('to_date')) {
            $to_date = Input::get('to_date');
        }
        if ($from_date != '' && $to_date != '') {
            $all_notes = $all_notes->whereBetween('created_at', array($from_date . " 00:00:00", $to_date . " 23:59:59"));
        } else if ($from_date == '' && $to_date != '') {
            $all_notes = $all_notes->whereDate('created_at', '<=', $to_date);
        } else if ($from_date != '' && $to_date == '') {
            $all_notes = $all_notes->whereDate('created_at', '>=', $from_date);
        }

        $status_filter = 'All';

        if (Input::get('status')) {
            $status = Input::get('status');
            if ($status == 'Completed') {
                $status_filter = 'Completed';
                $all_notes = $all_notes->where('status', '=', 1);
            } else if ($status == 'UnCompleted') {
                $status_filter = 'UnCompleted';
                $all_notes = $all_notes->where('status', '=', 0);
            }
        }

        if (Input::get('admin')) {
            $selected_admin = Input::get('admin');
            if (is_array($selected_admin)) {
                $nteo = OrderNoteRep::where('id', '>', 0);
                for ($i = 0; $i < count($selected_admin); $i++) {
                    if ($i == 0) {
                        $nteo = $nteo->where('rep', $selected_admin[$i]);
                    } else {
                        $nteo = $nteo->orWhere('rep', $selected_admin[$i]);
                    }
                }
            }
            $nteo = $nteo->pluck('note')->toArray();
        } else {
            $nteo = array();
        }
        if (count($nteo) > 0) {
            $nteo = array_merge($result, $nteo);
        } else {
            $nteo = $result;
        }
        if (Input::get('tags')) {
            $selected_tags = Input::get('tags');
            if (is_array($selected_tags)) {
                $nteox = OrderNoteTag::wherein('tag', $selected_tags)->pluck('note')->toArray();
                if (count($nteox) > 0) {
                    $nteo = array_merge($nteox, $nteo);
                }
            }
        }
        if (count($nteo) > 0) {
            $all_notes = $all_notes->whereIn('id', $nteo);
        }

        $all_notes = $all_notes->with('order_info', 'admin_info', 'reps')->orderBy('status')->orderBy('order', 'desc')->paginate(50);

        $repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $tags = Tag::get();

        return view('admin.pages.selling_order.notes')->with(['all_notes' => $all_notes, 'repps' => $repps, 'selected_admin' => $selected_admin, 'from_date' => $from_date,
            'to_date' => $to_date, 'status_filter' => $status_filter, 'tags' => $tags, 'selected_tags' => $selected_tags]);
    }

    public function load_order_notes(Request $request)
    {
        if (!permission_group_checker(Auth::guard('admin')->user()->id, 'Order Notes') && Auth::guard('admin')->user()->position == 1) {
            return '';
        }

        if (permission_checker(Auth::guard('admin')->user()->id, 'all_order_notes')) {
            $all_notes = OrderNote::where('order', $request->order);
        } else if (permission_checker(Auth::guard('admin')->user()->id, 'order_notes')) {
            $nteo = OrderNoteRep::where('rep', Auth::guard('admin')->user()->id)->pluck('note');
            $all_notes = OrderNote::whereIn('id', $nteo)->where('order', $request->order)->whereIn('id', $nteo);
        }

        $all_notes = $all_notes->orderBy('created_at', 'desc')->get();
        $repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $tags = Tag::get();
        $abc = view('admin.pages.selling_order.more_notes')->with(['all_notes' => $all_notes, 'repps' => $repps,
            'tags' => $tags])->render();
        return $abc;
    }

    public function order_notes_checker(Request $request)
    {
        $note = OrderNote::findorfail($request->item);
        if ($note->status == 1) {
            $ss = 0;
        } else {
            $ss = 1;
        }
        $note->status = $ss;
        $note->save();
        return response()->json(['success' => true, 'message' => "Note Has Been Added Successfully"]);
    }

    function sell_order_price(Request $request)
    {
        $product = Product::findorfail($request->item);
        ?>
        <label>Price (EGP)</label>
        <input class="form-control sell_order_price" type="text" placeholder="Price" name="price[]"
               value="<?php echo $product->price; ?>"/>
        <?php
    }

    function delete_selling_order_item($id, Request $request)
    {
        $item = SellOrderItem::findorfail($id);

        $order = SellOrder::findorfail($item->order);

        $total = 0;

        $item_desc = $item->product_info;
        if ($item->color > 0) {
            $item_desc .= " - " . $item->color_info->title;
        }
        if ($item->size > 0) {
            $item_desc .= " - " . $item->size_info->title;
        }


        $event = new TimeLine;
        $event->admin = Auth::guard('admin')->user()->id;
        $event->order = $order->id;
        $event->order_type = 1;
        $event->text = " Has Deleted [" . $item_desc . "] From This Order";
        $event->save();

        $ffx = Fulfillment::where('item', $id)->get();
        foreach ($ffx as $ff) {
            $ff->delete();
        }
        $item->delete();
        get_product_qty($item->product, $item->color, $item->size);

        $time_line = new ProductTimeline;
        $time_line->product = $item->product;
        $time_line->color = $item->color;
        $time_line->size = $item->size;
        $time_line->admin = Auth::guard('admin')->user()->id;
        $time_line->order = $order->id;
        $time_line->order_type = 1;
        $time_line->qty = get_product_qty_alt($item->product, $item->color, $item->size);
        $time_line->text = " Has Updated Sell Order With Qty (" . $item->qty . ")";
        $time_line->save();


        foreach ($order->itemsq as $itexm) {
            $total = $total + ($itexm->qty * $itexm->price);
        }
        $order->total_price = $total;
        $order->save();
        return redirect()->back();
    }

    public function selling_order_client($id, Request $request)
    {
        $order = SellOrder::findorfail($id);
        if (!permission_checker(Auth::guard('admin')->user()->id, 'edit_client')) {
            abort(404);
        }
        $client = Client::findorfail($order->client_info->id);
        if ($client->hide == 0) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'phone' => 'required|min:11',
            ],
                [
                    'name.required' => 'Please Enter Name',
                    'phone.required' => 'Please Enter Phone Number',
                    'phone.min' => 'Phone Number Min. Length Is 11',
                ]);
            if ($client->phone != $request->phone) {
                $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'phone' => ['required', 'min:11', Rule::unique('clients')->where(function ($query) {
                        $query->where('hide', '=', 0);
                    })],
                ],
                    [
                        'name.required' => 'Please Enter Name',
                        'phone.required' => 'Please Enter Phone Number',
                        'phone.min' => 'Phone Number Min. Length Is 11',
                        'phone.unique' => 'This Phone Number Is Registered To Another Client',
                    ]);
            }
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
            }
            $client->name = $request->name;
            $client->phone = $request->phone;
            $client->save();
            return response()->json(['success' => true, 'message' => "Client Information Updated Successfully"]);
        }
    }

    public function delete_note($id, Request $request)
    {
        $note = OrderNote::findorfail($id);
        $note->delete();

        $event = new TimeLine;
        $event->admin = Auth::guard('admin')->user()->id;
        $event->order = $note->order_info->id;
        $event->order_type = 1;
        $event->text = " Has Deleted Selling Order Note [" . $note->note . "]";
        $event->save();

        $ddd = OrderNoteRep::where('note', $note->id)->get();
        foreach ($ddd as $r) {
            $r->delete();
        }

        return redirect()->back();
    }

    public function edit_note($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'required'
        ],
            [
                'note.required' => 'Please Enter Your Note'
            ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else {
            $note = OrderNote::findorfail($id);
            $order = SellOrder::findorfail($note->order);
            if ($request->note != $note->note) {
                $event = new TimeLine;
                $event->admin = Auth::guard('admin')->user()->id;
                $event->order = $order->id;
                $event->order_type = 1;
                $event->text = " Has Changed Selling Order Note From [" . $note->note . "] To [" . $request->note . "]";
                $event->save();
            }
            $note->order = $note->order;
            $note->note = $request->note;
            $note->save();
            OrderNoteRep::where('note', $note->id)->delete();
            if ($request->has('rep')) {
                for ($i = 0; $i < count($request->rep); $i++) {
                    $a = new OrderNoteRep;
                    $a->note = $note->id;
                    $a->rep = $request->rep[$i];
                    $a->save();
                }
            }
            OrderNoteTag::where('note', $note->id)->delete();
            if ($request->has('tag')) {
                for ($i = 0; $i < count($request->tag); $i++) {
                    $a = new OrderNoteTag;
                    $a->note = $note->id;
                    $a->tag = $request->tag[$i];
                    $a->save();
                }
            }


            $st = new OrderNoteStatus;
            $st->order = $note->order;
            $st->note = $note->id;
            $st->admin = Auth::guard('admin')->user()->id;
            $st->save();

            return response()->json(['success' => true, 'message' => "Note Has Been Updated Successfully"]);
        }
    }


    public function order_location(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'url|nullable'
        ],
            [
                'location.url' => 'Please Enter Valid URL'
            ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()]);
        } else {
            $order = SellOrder::findorfail($id);
            $order->location = $request->location;
            $order->save();

            $client = $order->client_info;
            $client->location = $request->location;
            $client->save();

            return response()->json(['success' => true, 'message' => "Order Location Updated Successfully"]);
        }
    }


    public function order_time_line($id)
    {
        $order = SellOrder::with(['time_lines'])->findOrFail($id);
        return view('admin.pages.selling_order.time_line', compact('order'));
    }

    public function order_notes($id)
    {
        $order = SellOrder::with(['notes_desc'])->findOrFail($id);
        $repps = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $tags = Tag::get();
        return view('admin.pages.selling_order.order_notes', compact('order', 'repps', 'tags'));
    }

    public function getOrdersTags(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('order_tags')->select('id', 'title as text')
                ->where('title', 'LIKE', '%' . $term . '%')
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
