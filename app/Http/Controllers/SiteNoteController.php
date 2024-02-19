<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;

use App\OrderNote;

class SiteNoteController extends Controller  implements FromArray, Responsable
{
    use Exportable;
    
    public function array() : array
    {
        if(Request()->get('orders')) 
        {
            $selected_orders = explode(',' ,Request()->get('orders'));
            if(!is_array($selected_orders)) {$selected_orders[] = Request()->get('orders');}
        }
        else {$selected_orders = array();}
        if(count($selected_orders) > 0)
        {
            $notes = OrderNote::whereIn('order', $selected_orders)->get();
        }
        else
        {
            $notes = OrderNote::whereIn('order', $selected_orders)->get();
        }
        $abc = array();
        $abc[] = array('Order ID', 'Shipping ID', 'Client', 'Note', 'Tags', 'Note Status');
        foreach ($notes as $note)
        {

            $tags = "";
            foreach ($note->tags as $tag)
            {
                if ($tags != '') {$tags .= ', ';}
                $tags .= $tag->tag_info->title;
            }
            if($note->admin_info) {$added_by = $note->admin_info->name;}
            else {$added_by = '';}
            $rep = '';
            foreach($note->reps as $ss)
            {
                if ($rep != '') {$rep .= ', ';}
               $rep .= $ss->rep_info->name;
            }
            $note_status = "Un-Completed";
            if($note->status == 1) {$note_status = "Completed";}
            $abc[] = array("order"=>$note->order_info->id, "shipping"=>$note->order_info->shipping_number, 
            'client'=>$note->order_info->client_info->name, "note"=>$note->note, "tags"=>$tags,
            'note_status'=>$note_status);
        }
        return $abc;
    }

    public function export_notes ()
    {
        return (new SiteNoteController)->download('orders_notes - '.date('Y-m-d h:i:s A').'.csv');
    }
}
