<?php

namespace App\Http\Controllers\Dashboard;

use App\Admin;
use App\Imports\AdsImport;
use App\Imports\SellOrdersImport;
use App\OrderStatus;
use App\SellOrder;
use App\Tag;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class LogisticController extends Controller
{
    //
    public function index()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_logistic')) {abort(404);}

        $repps = Admin::where('position', '=', 2)->where('hide', '=', 0)->get();
        $moderators = Admin::where('position', '=', 1)->where('hide', '=', 0)->get();
        $tags = Tag::get();
        $statuss = OrderStatus::where('hide', '=', 0)->get();


        return view('admin.pages.logistics.index', compact('moderators', 'tags', 'statuss', 'repps'));
    }

    public function logistics_search(Request $request)
    {
        $data = SellOrder::query()->where('hide', 0)->with(['latestThreeNotes']);

        $data->whereExists(function ($query) use ($request) {
            $query->select(\DB::raw(1))
                ->from('time_lines')
                ->whereRaw('time_lines.order = sell_orders.id')
                ->whereBetween('time_lines.created_at', [
                    $request->from_date . ' 00::00::00',
                    $request->to_date . ' 23::59::59',
                ])
                ->when($request->moderator_id, function ($subQuery) use ($request) {
                    $moderators_ides = array_map('intval', $request->input('moderator_id'));
                    $subQuery->whereIn('time_lines.admin', $moderators_ides);
                })
                ->when($request->from_status, function ($subQuery) use ($request) {
                    $from_status = array_map('intval', $request->input('from_status'));
                    $subQuery->where('time_lines.id', function ($maxQuery) {
                        $maxQuery->select(\DB::raw('MAX(id)'))
                            ->from('time_lines')
                            ->whereRaw('time_lines.order = sell_orders.id')
                            ->whereNotNull('time_lines.from_status'); // Add this line to check for not null

                    })->whereIn('time_lines.from_status', $from_status);
                })
                ->when($request->last_status, function ($subQuery) use ($request) {
                    $last_status = array_map('intval', $request->input('last_status'));

                    $subQuery->where('time_lines.id', function ($maxQuery) {
                        $maxQuery->select(\DB::raw('MAX(id)'))
                            ->from('time_lines')
                            ->whereRaw('time_lines.order = sell_orders.id')
                            ->whereNotNull('time_lines.to_status'); // Add this line to check for not null

                    })->whereIn('time_lines.to_status', $last_status);
                });
        });

        $data->when($request->repp_id, function ($query) use ($request) {
            $repp_ides = array_map('intval', $request->input('repp_id'));
            $query->whereIn('delivered_by', $repp_ides);
        });


        $data->when($request->order_number, function ($query) use ($request) {

            $order_numbers = is_array($request->order_number) ? $request->order_number : explode(' ', $request->order_number);

            $query->where(function ($subQuery) use ($order_numbers) {
                $subQuery->whereIn('order_number', $order_numbers)
                    ->orWhereIn('shipping_number', $order_numbers);
            });


        });


        // Use get() to retrieve the results
         $orders = $data->select('sell_orders.*')->distinct()->get();

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


        return view('admin.pages.logistics.parts.table_search', compact('orders', 'add_selling_order', 'edit_selling_orders', 'delete_selling_order'));
    }

    public function import_sell_orders()
    {
        return view('admin.pages.logistics.parts.import');
    }

    public function import_sell_orders_store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        Excel::import(new SellOrdersImport, $file);

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }

    public function copyOrderNumber(Request $request)
    {
        $items = $request->input('items');

        // Fetch the order numbers from the database
        $sell_order_numbers = SellOrder::whereIn('id', $items)->pluck('order_number')->toArray();

        // Return the order numbers as JSON
        return response()->json(['order_numbers' => $sell_order_numbers]);
    }
}
