<?php

namespace App\Http\Controllers\Dashboard;

use App\BuyOrderItem;
use App\DetailsFile;
use App\FileGard;
use App\Imports\InventoryImport;
use App\Inventory;
use App\Product;
use App\RuinedItem;
use App\RuinedItemAdmins;
use App\Traits\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InventoryExport;
class InventoriesController extends Controller
{
    use Chat;

    public function index(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_inventory')) {abort(404);}

        $custom_select = $request->input('custom_select', 20);
        $queryParameters = $request->query();
        $total_qty = null;
        $total_amount = null;
        $data = Inventory::orderBy('product', 'ASC')->where('hide',0);
        $total_qty_model = Inventory::whereRaw('(bought - sold) > 0')->where('hide',0);
        $total_amount_model = Inventory::whereRaw('(bought - sold) > 0')->where('hide',0);
        $open_array=[0,1];


        if ($request->has('search')) {


            if ($request->open){

                if ($request->open=='open'){
                    $open_array=[1];
                }
                elseif ($request->open=='not_open'){
                    $open_array=[0];
                }
                else
                {
                    $open_array=[0,1];

                }
                $data->whereIn('open', $open_array);
                $total_qty_model->whereIn('open', $open_array);
                $total_amount_model->whereIn('open', $open_array);
            }


            if ($request->filled('product_id')) {
                $productIdes = array_map('intval', $request->input('product_id'));

                $data->whereIn('product', $productIdes);
                $total_qty_model->whereIn('product', $productIdes);
                $total_amount_model->whereIn('product', $productIdes);
            }

            if ($request->has('tag_id')) {
                $tagIds = array_map('intval', $request->input('tag_id'));

                $data->whereHas('product_info.tags', function ($query) use ($tagIds) {
                    $query->whereIn('tag_groups.id', $tagIds);
                });

                $total_qty_model->whereHas('product_info.tags', function ($query) use ($tagIds) {
                    $query->whereIn('tag_groups.id', $tagIds);
                });

                $total_amount_model->whereHas('product_info.tags', function ($query) use ($tagIds) {
                    $query->whereIn('tag_groups.id', $tagIds);
                });
            }

            $rows = $data->paginate(50);
            $total_qty = $total_qty_model->sum(DB::raw('bought - sold'));
            $total_amount = $total_amount_model->sum(DB::raw('(bought - sold) * last_cost'));
        } else {
            $total_qty = Inventory::whereRaw('(bought - sold) > 0')->where('hide',0)->sum(DB::raw('bought - sold'));
            $total_amount = Inventory::whereRaw('(bought - sold) > 0')->where('hide',0)->sum(DB::raw('(bought - sold) * last_cost'));
            $rows = collect();
        }

        return view('admin.pages.inventories.index', compact('rows', 'queryParameters', 'request', 'total_qty', 'total_amount'));
    }

    public function ruinedItemFromInventory($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'ruined_item')) {abort(404);}

        $inventory = Inventory::findOrfail($id);
        $ruined_item = RuinedItem::with(['destroyer.admin'])->where('product', $inventory->product)->where('color', $inventory->color)->where('size', $inventory->size)->first();
        if (!$ruined_item) {
            $ruined_item = RuinedItem::create([
                'product' => $inventory->product,
                'color' => $inventory->color,
                'size' => $inventory->size,
                'qty' => 0,
            ]);
        }
        return view('admin.pages.inventories.parts.ruined', compact('ruined_item', 'inventory'));

    }

    public function store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}


        $data = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'qty' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ]);

        $inventory = Inventory::findOrfail($request->inventory_id);
        $ruined_item = RuinedItem::where('product', $inventory->product)->where('color', $inventory->color)->where('size', $inventory->size)->first();

        if (!$ruined_item) {
            $ruined_item = RuinedItem::create([
                'product' => $inventory->product,
                'color' => $inventory->color,
                'size' => $inventory->size,
                'qty' => 0,
            ]);
        }
        if ($request->qty > 0) {
            $old_qty = $ruined_item->qty;
            $ruined_item->qty = $old_qty + $request->qty;
            $ruined_item->save();

            update_ruined($inventory->product, $inventory->color, $inventory->size, $request->qty);

            RuinedItemAdmins::create([
                'added_by' => Auth::guard('admin')->user()->id,
                'ruined_item_id' => $ruined_item->id,
            ]);


        }
        $inventory = Inventory::findOrfail($request->inventory_id);


        $row = view('admin.pages.inventories.parts.row', ['row' => $inventory])->render();

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!',
                'row' => $row,

            ]);


    }


    public function inventoryGetTotalQty()
    {

        $total_qty = Inventory::whereRaw('(bought - sold) > 0')->sum(DB::raw('bought - sold'));

        return response()->json(['status' => true, 'total_qty' => $total_qty]);


    }

    public function inventoryGetTotalAmount()
    {

        $total_amount = 0;
        $total_amount = Inventory::whereRaw('(bought - sold) > 0')
            ->orderBy('product', 'ASC')
            ->sum(DB::raw('(bought - sold) * last_cost'));

        return response()->json(['status' => true, 'total_amount' => $total_amount]);

    }


    public function export_inventories(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}

        $data = $request;

        if ($data["tag_id"] !== null) {
            $data["tag_id"] = array_map('intval', explode(',', $data["tag_id"]));
        }
        if ($data["product_id"] !== null) {
            $data["product_id"] = array_map('intval', explode(',', $data["product_id"]));
        }



        return Excel::download(new InventoryExport($data), 'inventories.xlsx');

    }

    public function import_inventory()
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}

        return view('admin.pages.inventories.parts.import');

    }

    public function import_inventory_store(Request $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}


        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');

        $file_gard=FileGard::create([
            'admin_id'=>Auth::guard('admin')->user()->id,
            'date'=>date('Y-m-d'),
        ]);

        Excel::import(new InventoryImport($file_gard->id), $file);
        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح',

            ]);

    }

    public function changeInventoryOpen(Request $request){
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}

        $inventory=Inventory::findOrFail($request->inventory_id);
        $inventory->open=$request->open;
        $inventory->save();
        return response()->json(['status'=>true]);
    }



    public function changeInventoryQty(Request $request){
        if(!permission_checker(Auth::guard('admin')->user()->id, 'edit_inventory')) {abort(404);}
        $inventory=Inventory::findOrFail($request->inventory_id);
        $new_sum=$inventory->bought-$inventory->sold-$request->qty;
        $new_sold=$inventory->sold+$new_sum;
        $inventory->sold=$new_sold;
        $inventory->save();
        $qty=$inventory->bought-$inventory->sold;
        Chat::notify($qty,$inventory->product,$inventory->color,$inventory->size);

        $file_gard=  FileGard::where('admin_id',Auth::guard('admin')->user()->id)->where('date',date('Y-m-d'))->where('type','inventory')->first();
        if (!$file_gard) {
            $file_gard = FileGard::create([
                'admin_id' => Auth::guard('admin')->user()->id,
                'date' => date('Y-m-d'),
                'type'=>'inventory',
            ]);
        }
        DetailsFile::create([
            'inventory_id' => $inventory->id,
            'file_gard_id' => $file_gard->id,
            'price' => $inventory->last_cost,
            'qty' => $new_sum,
        ]);
        return response()->json(['status'=>true]);

    }



}
