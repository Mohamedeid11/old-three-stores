<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Product;
use  App\Inventory;
use App\RuinedItem;
use App\SellOrderItem;
use App\BuyOrderItem;
use App\BuyOrder;
use App\SellOrder;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\Support\Responsable;

class SiteController extends Controller  implements FromArray, Responsable
{
    use Exportable;
    
    public function array() : array
    {
        if(Request()->get('products')) 
        {
            $selected_products = explode(',' ,Request()->get('products'));
            if(!is_array($selected_products)) {$selected_products[] = Request()->get('products');}
        }
        else {$selected_products = array();}
        /*
        $products = Product::where('hide', 0)->get();
        foreach ($products as $product)
        {
            $inventory =  Inventory::where('product', $product->id)->first();
            if($inventory === NULL && count($product->colors) == 0 && count($product->sizes) == 0)
            {
                $n = new Inventory;
                $n->product = $product->id;
                $n->color = 0;
                $n->size = 0;
                $n->save();
            }
            else 
            {
                if(count($product->colors) > 0 && count($product->sizes) > 0)
                {
                    foreach ($product->colors as $color)
                    {
                        foreach ($product->sizes as $size)
                        {
                            $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', $size->size)->first();
                            if($inventory === NULL)
                            {
                                $n = new Inventory;
                                $n->product = $product->id;
                                $n->color = $color->color;
                                $n->size = $size->size;
                                $n->save();                            
                            }
                        }
                    }
                }
                else if(count($product->colors) > 0 && count($product->sizes) == 0)
                {
                    foreach ($product->colors as $color)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', $color->color)->where('size', 0)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = $color->color;
                            $n->size = 0;
                            $n->save();                            
                        }
                    }
                }
                else if(count($product->sizes) == 0 && count($product->colors) > 0)
                {
                    foreach ($product->sizes as $size)
                    {
                        $inventory = Inventory::where('product', $product->id)->where('color', 0)->where('size', $size->size)->first();
                        if($inventory === NULL)
                        {
                            $n = new Inventory;
                            $n->product = $product->id;
                            $n->color = 0;
                            $n->size = $size->size;
                            $n->save();                            
                        }
                    }
                }
                               
            }
        }
        */
            $sorders = SellOrder::where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11])->pluck('id');
            $borders = BuyOrder::where('hide', '=', 0)->pluck('id');

        if(count($selected_products) > 0)
        {
            $products = Product::select('products.id','products.title', 'colors.title as color', 'sizes.title as size',
            'colors.id as color_id', 'sizes.id as size_id', 'inventories.bought', 'inventories.sold')->whereIn('products.id', $selected_products)
            ->where('products.hide', 0)
            ->join('inventories', 'products.id', 'inventories.product')
            ->leftJoin('colors', 'inventories.color', 'colors.id')
            ->leftJoin('sizes', 'inventories.size', 'sizes.id')
            ->orderBy('products.title')->get()->toArray();
        }
        else
        {
        
            $products = Product::select('products.id','products.title', 'colors.title as color', 'sizes.title as size',
            'colors.id as color_id', 'sizes.id as size_id', 'inventories.bought', 'inventories.sold')->where('products.hide', 0)->where('products.discontinue', 0)
            ->join('inventories', 'products.id', 'inventories.product')
            ->leftJoin('colors', 'inventories.color', 'colors.id')
            ->leftJoin('sizes', 'inventories.size', 'sizes.id')
            ->orderBy('products.title')->get()->toArray();            
        }
          
            // dd($products);
        $abc = array();
        $abc[] = array('title', 'sku', 'variation', 'stock','open');
        $sorders = SellOrder::where('hide', '=', 0)->whereNotIn('status', [0, 1, 8, 11])->pluck('id');
        $borders = BuyOrder::where('hide', '=', 0)->pluck('id');

        for ($i = 0; $i < count($products); $i++)
        {
            $sold = $products[$i]['sold'];
            $bought = $products[$i]['bought'];
            $inventory=null;
            $open='shopify';
            
            if($products[$i]['color_id'] == 0 && $products[$i]['size_id'] == 0)
            {
                $inventory=Inventory::where('product',$products[$i]['id'])->where('color',0)->where('size',0)->first();
                 if ($inventory){
                     if ($inventory->open==1)
                     {
                         $open='0';
                     }
                 }
                $sku = "Three00".$products[$i]['id'];
                $variation = NULL;
                // $sold = SellOrderItem::
                // whereIn('sell_order_items.order', $sorders)
                // ->where('sell_order_items.product', $products[$i]['id'])
                // ->where('sell_order_items.color', 0)
                // ->where('sell_order_items.size', 0)
                // ->select('sell_order_items.*')
                // ->sum('qty');
                // $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $products[$i]['id'])->
                // where('color', 0)->where('size', 0)->sum('qty');
//                $cr = RuinedItem::where('product', '=', $products[$i]['id'])->where('color', '=', 0)
//                ->where('size', '=', 0)->first();
//                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                $stock = $bought - ($sold );
            }
            else if($products[$i]['color_id'] > 0 && $products[$i]['size_id'] == 0)
            {
                $inventory=Inventory::where('product',$products[$i]['id'])->where('color',$products[$i]['color_id'])->where('size',0)->first();
                if ($inventory){
                    if ($inventory->open==1)
                    {
                        $open='0';
                    }
                }
                $sku = "Three00".$products[$i]['id']."-00".$products[$i]['color_id']."-000";
                $variation = $products[$i]['color'];
                // $sold = SellOrderItem::
                // whereIn('sell_order_items.order', $sorders)
                // ->where('sell_order_items.product', $products[$i]['id'])
                // ->where('sell_order_items.color', $products[$i]['color_id'])
                // ->where('sell_order_items.size', 0)
                // ->select('sell_order_items.*')
                // ->sum('qty');
                // $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $products[$i]['id'])->
                // where('color', $products[$i]['color_id'])->where('size', 0)->sum('qty');
//                $cr = RuinedItem::where('product', '=', $products[$i]['id'])->where('color', '=', $products[$i]['color_id'])
//                ->where('size', '=', 0)->first();
//                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}

                $stock = $bought - ($sold );
            }
            else if($products[$i]['color_id'] == 0 && $products[$i]['size_id'] > 0)
            {
                $inventory=Inventory::where('product',$products[$i]['id'])->where('color',0)->where('size',$products[$i]['size_id'])->first();
                if ($inventory){
                    if ($inventory->open==1)
                    {
                        $open='0';
                    }
                }
                $sku = "Three00".$products[$i]['id']."-000-00".$products[$i]['size_id'];
                $variation = $products[$i]['size'];
                // $sold = SellOrderItem::
                // whereIn('sell_order_items.order', $sorders)
                // ->where('sell_order_items.product', $products[$i]['id'])
                // ->where('sell_order_items.color', 0)
                // ->where('sell_order_items.size', $products[$i]['size_id'])
                // ->select('sell_order_items.*')
                // ->sum('qty');
                // $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $products[$i]['id'])->
                // where('color', 0)->where('size',  $products[$i]['size_id'])->sum('qty');
//                $cr = RuinedItem::where('product', '=', $products[$i]['id'])
//                ->where('color', '=', 0)
//                ->where('size', '=', $products[$i]['size_id'])->first();
//                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                $stock = $bought - ($sold );
            }
            else if($products[$i]['color_id'] > 0 && $products[$i]['size_id'] > 0)
            {
                $inventory=Inventory::where('product',$products[$i]['id'])->where('color',$products[$i]['color_id'])->where('size',$products[$i]['size_id'])->first();
                if ($inventory){
                    if ($inventory->open==1)
                    {
                        $open='0';
                    }
                }
                $sku = "Three00".$products[$i]['id']."-00".$products[$i]['color_id']."-00".$products[$i]['size_id'];
                $variation = $products[$i]['color'].",".$products[$i]['size'];
                // $sold = SellOrderItem::
                // whereIn('sell_order_items.order', $sorders)
                // ->where('sell_order_items.product', $products[$i]['id'])
                // ->where('sell_order_items.color', $products[$i]['color'])
                // ->where('sell_order_items.size', $products[$i]['size_id'])
                // ->select('sell_order_items.*')
                // ->sum('qty');
                // $bought = BuyOrderItem::whereIn('order', $borders)->where('product', $products[$i]['id'])->
                // where('color', $products[$i]['color'])->where('size',  $products[$i]['size_id'])->sum('qty');
//                $cr = RuinedItem::where('product', '=', $products[$i]['id'])
//                ->where('color', '=', $products[$i]['color'])
//                ->where('size', '=', $products[$i]['size_id'])->first();
//                if($cr !== NULL) {$ruined = $cr->qty;}else{$ruined = 0;}
                $stock = $bought - ($sold );
            }

            $abc[] = array("title"=>$products[$i]['title'], "sku"=>$sku, "variation"=>$variation, "stock"=>$stock,'open'=>$open);
        }
        return $abc;
    }

    public function inventory_test ($id)
    {
        $product = Product::findorfail($id);
        $inventory = Inventory::where('product', $id)->get();
        return view('inventory_report', compact(['product', 'inventory']));
    }

    public function export_products ()
    {
        return (new SiteController)->download('inventory - '.date('Y-m-d h:i:s A').'.csv');
    }
}
