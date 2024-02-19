<?php

namespace App\Imports;

use App\OrderNote;
use App\SellOrder;
use App\TimeLine;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SellOrdersImport implements ToModel, WithHeadingRow
{
    public function __construct()
    {
    }

    public function model(array $row)
    {
        $sell_order=null;
        if (isset($row['order'])) {
            $sell_order = SellOrder::where('order_number',$row['order'])->first();
        }

        if ($sell_order) {

            if (isset($row['shipping'])) {
                $sell_order->update([
                    'shipping_number'=>$row['shipping'],
                ]);
            }

            if (isset($row['note'])) {
                $sell_order = SellOrder::where('order_number',$row['order'])->first();
                          $note=   OrderNote::create([
                                 'order'=>$sell_order->id,
                                 'note'=>$row['note'],
                                 'added_by'=>Auth::guard('admin')->user()->id,
                             ]);

                $event = new TimeLine;
                $event->admin = Auth::guard('admin')->user()->id;
                $event->order = $sell_order->id;
                $event->order_type = 1;
                $event->text = " Has Created Selling Order Note [".$note->note."]";
                $event->save();

            }




        }


        return null; // Return null to skip adding a new model to the collection
    }
}
