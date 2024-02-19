<?php

namespace App\Http\Controllers\Dashboard;

use App\DetailsFile;
use App\FileGard;
use App\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FileGardController extends Controller
{
    //
    public function index(Request  $request)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_file_gard')) {abort(404);}

        $checkDate=false;
        $data = FileGard::query()->with(['admin',])->where('hide', 0)->orderBy('id', 'DESC');
        if ($request->fromDate){
            $checkDate=true;
           $data->where('date','>=',$request->fromDate);
        }
        if ($request->toDate){
            $data->where('date','<=',$request->toDate);
            $checkDate=true;
        }
        if (!$checkDate){
            $currentDate = new \DateTime();
            $sevenDaysAgo = clone $currentDate;
            $sevenDaysAgo->modify('-7 days');
            $data->where('date','>=',$sevenDaysAgo->format('Y-m-d'));
            $request['fromDate']=$sevenDaysAgo->format('Y-m-d');
        }

        if ($request->product_id){
            $productIds = array_map('intval', $request->input('product_id'));

            $data->whereHas('detailsFiles.inventory.product_info', function ($query) use ($productIds) {
                $query->whereIn('products.id', $productIds);
            });
        }



        $rows=$data->get();
        return view('admin.pages.file_gard.index', compact('rows','request'));
    }

    public function show($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'show_file_gard')) {abort(404);}

        $file_gard = FileGard::findOrFail($id);
        $file_details = DetailsFile::with(['inventory'])
            ->selectRaw('*, SUM(qty) as total_qty')
            ->where('hide', 0)
            ->where('file_gard_id', $id)
            ->groupBy('inventory_id')
            ->get();

        return view('admin.pages.file_gard.details', compact('file_gard', 'file_details'));

    }

    public function destroy($id)
    {
        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_file_gard')) {abort(404);}

        $file_gard = FileGard::findOrFail($id);
        foreach ($file_gard->detailsFiles as $row) {
            $inventory = Inventory::find($row->inventory_id);
            if ($inventory) {
                $sold = $inventory->sold;
                $inventory->update([
                    'sold' => $sold - $row->qty,
                ]);
            }
            $row->update([
                'hide'=>1,
            ]);
        }

        $file_gard->update(['hide'=>1]);

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);

    }

    public function detail_file_delete($id){

        if(!permission_checker(Auth::guard('admin')->user()->id, 'delete_file_gard')) {abort(404);}

        $row=DetailsFile::findOrFail($id);
        $inventory = Inventory::find($row->inventory_id);
        if ($inventory) {
            $sold = $inventory->sold;
            $inventory->update([
                'sold' => $sold - $row->qty,
            ]);
        }
        $row->update([
            'hide'=>1,
        ]);

        return response()->json(
            [
                'status' => 200,
                'message' => 'تمت العملية بنجاح!'
            ]);
    }
}
