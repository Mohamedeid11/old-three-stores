<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Product;
use App\ProductColor;
use App\ProductSize;

use App\SellOrder;
use App\SellOrderItem;
use App\BuyOrder;
use App\BuyOrderItem;
use App\RuinedItem;

class OrderController extends Controller
{
    public function add_order_item (Request $request)
    {
        $products = Product::where('discontinue', '=', 0)->where('hide', '=', 0)->orderBy('title')->get();
        if($request->type == 'sell')
        {
            ?>
            <li class="single_order_item_x" id="single_order_item_box_<?php echo $request->order_item; ?>">
                <input type="hidden" name="order_item[]" value="<?php echo $request->order_item; ?>" />
                <input type="hidden" name="order_item_id[]" value="0" />
                <div class="container-fluid">
                    <div class="row mb-1">
                        <div class="col-md-3">
                            <label class="d-none">Product</label>
                            <select class="order_product_item form-control" name="product[]" id="order_item_<?php echo $request->order_item; ?>"
                            options-url="<?php echo url('product_options'); ?>" item-id="<?php echo $request->order_item; ?>" price-url="<?php echo url('product_price'); ?>">
                                <option value="" disabled selected>Choose Order Item</option>
                                <?php
                                foreach ($products as $product)
                                {
                                    ?>
                                    <option value="<?php echo $product->id; ?>"><?php echo $product->title; ?></option>
                                    <?php
                                }
                                ?>
                                <!-- <option value="0">New Product</option> -->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="d-none">QTY</label>												
                            <input class="form-control sell_order_qty" type="number" placeholder="QTY" name="qty[]" id="sell_order_qty_<?php echo $request->order_item; ?>" 
                            item-id="<?php echo $request->order_item; ?>" price-url="<?php echo url('product_price'); ?>"/>
                        </div>
                        <div id="order_item_options_<?php echo $request->order_item; ?>" class="col-md-4">

                        </div>
                        <div id="order_item_price_<?php echo $request->order_item; ?>" class="col-md-2"></div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm btn-block delete_order_item" box="<?php echo $request->order_item; ?>"><i class="fas fa-trash-alt"></i></button>
                        </div>

                    </div>
                    
                    <div class="row">
                        <div class="col-md-11">
                            <textarea rows="1" class="form-control" placeholder="Note" name="note[]"></textarea>
                        </div>
                        <div class="col-md-1">
                        <div id="order_item_available_units_<?php echo $request->order_item; ?>"></div>
                        </div>
                    </div>
                </div>
            </li>
            <?php
        }
        else if($request->type == 'buy')
        {
            ?>
            <tr id="single_order_item_box_<?php echo $request->order_item; ?>">
                <td>
                  <button type="button" class="btn btn-danger btn-sm btn-block buy_delete_order_item" box="<?php echo $request->order_item; ?>"><i class="fas fa-trash-alt"></i></button>
                </td>
                <td>
                    <input type="hidden" name="order_item[]" value="2" />
                    <select class="order_product_item form-control" name="product[]" id="order_item_<?php echo $request->order_item; ?>"
                    options-url="<?php echo url('product_options'); ?>" item-id="<?php echo $request->order_item; ?>">
                        <option value="" disabled selected>Choose Order Item</option>
                        <?php
                        foreach ($products as $product)
                        {
                            ?>
                            <option value="<?php echo $product->id; ?>"><?php echo $product->title; ?></option>
                            <?php
                        }
                        ?>
                        <!-- <option value="0">New Product</option> -->
                    </select>
                    <input type="hidden" name="note[]" value="" />
                </td>
                <td id="color_order_item_<?php echo $request->order_item; ?>">

                </td>
                <td id="size_order_item_<?php echo $request->order_item; ?>">

                </td>
                <td>
                    <input type="number" step="1" id="buyorder_qty_<?php echo $request->order_item; ?>" name="qty[]" class="form-control buyorder_items_qty" data-url="<?php echo url('calculate_buyorder_qtys'); ?>"
                    item-id="<?php echo $request->order_item; ?>"/>
                </td>
                <td>
                    <input type="number" step="0.01" name="price[]" class="form-control buyorder_items_price" data-url="<?php echo url('calculate_buyorder_qtys'); ?>" id="buyorder_price_<?php echo $request->order_item; ?>"
                    item-id="<?php echo $request->order_item; ?>" />
                </td>
                <td id="buyorder_subtotal_<?php echo $request->order_item; ?>">

                </td>
            </tr>
            <?php
        }
    }


    public function product_options (Request $request)
    {
        $product = $request->item;
        $colors = ProductColor::where('product', '=', $product)->get();
        $sizes = ProductSize::where('product', '=', $product)->get();
        if($request->type == "sell")
        {
            if($colors->count() > 0 || $sizes->count() > 0)
            {
                ?>
                <div class="row">
                    <?php
                    if($colors->count() > 0)
                    { 
                        ?>
                        <div class="col-md-6">
                            <label class="d-none">Color</label>
                            <select class="form-control item_color_selector" name="color[]" 
                            id="item_color_selector<?php echo $request->item_id; ?>"
                            data-itemid="<?php echo $request->item_id; ?>" data-item="<?php echo $product; ?>"
                            available-url="<?php echo url('product_available_units'); ?>">
                                <option value="" disabled selected>Choose Color</option>
                                <?php
                                foreach ($colors as $color)
                                {
                                    ?>
                                    <option value="<?php echo $color->color_info->id; ?>"><?php echo $color->color_info->title; ?></option>
                                    <?php
                                }
                                ?>
                                <!-- <option value="0">New Product</option> -->
                            </select>
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <input type="hidden" name="color[]" class="item_color_selector" value="0" id="item_color_selector<?php echo $request->item_id; ?>"
                        data-itemid="<?php echo $request->item_id; ?>" data-item="<?php echo $product; ?>" />
                        <?php
                    }
                    if($sizes->count() > 0)
                    { 
                        ?>
                        <div class="col-md-6">
                            <label class="d-none">Size</label>
                            <select class="form-control item_size_selector" name="size[]"
                            id="item_size_selector<?php echo $request->item_id; ?>"
                            data-itemid="<?php echo $request->item_id; ?>" data-item="<?php echo $product; ?>"
                            available-url="<?php echo url('product_available_units'); ?>">
                                <option value="" disabled selected>Choose Size</option>
                                <?php
                                foreach ($sizes as $size)
                                {
                                    ?>
                                    <option value="<?php echo $size->size_info->id; ?>"><?php echo $size->size_info->title; ?></option>
                                    <?php
                                }
                                ?>
                                <!-- <option value="0">New Product</option> -->
                            </select>
                        </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <input type="hidden" name="size[]" class="item_size_selector" value="0" id="item_size_selector<?php echo $request->item_id; ?>"
                        data-itemid="<?php echo $request->item_id; ?>" data-item="<?php echo $product; ?>" />
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            else
            {
                ?>
                <input type="hidden" name="color[]" value="0" />
                <input type="hidden" name="size[]" value="0" />
                <?php
            }
        }
        else if($request->type == "buy")
        {
            if($request->has('column') && $request->column == 'color')
            {
                if($colors->count() > 0 )
                {
                    ?>
                    <select class="form-control item_color_selector" name="color[]" 
                    id="item_color_selector<?php echo $request->item_id; ?>">
                        <option value="" disabled selected>Choose Color</option>
                        <?php
                        foreach ($colors as $color)
                        {
                            ?>
                            <option value="<?php echo $color->color_info->id; ?>"><?php echo $color->color_info->title; ?></option>
                            <?php
                        }
                        ?>
                        <!-- <option value="0">New Product</option> -->
                    </select>
                    <?php
                }   
                else
                {
                    ?>
                    <input class="item_color_selector" type="hidden" name="color[]" value="0" />
                    <?php                    
                }                
            }
            if($request->has('column') && $request->column == 'size')
            {
                if($sizes->count() > 0)
                {
                    ?>
                    <select class="form-control item_size_selector" name="size[]" 
                    id="item_color_selector<?php echo $request->item_id; ?>">
                        <option value="" disabled selected>Choose Size</option>
                        <?php
                        foreach ($sizes as $size)
                        {
                            ?>
                            <option value="<?php echo $size->size_info->id; ?>"><?php echo $size->size_info->title; ?></option>
                            <?php
                        }
                        ?>
                        <!-- <option value="0">New Product</option> -->
                    </select>
                    <?php
                }   
                else
                {
                    ?>
                    <input class="item_size_selector" type="hidden" name="size[]" value="0" />
                    <?php                    
                }                
            }           
        }
    }

    public function product_available_units (Request $request)
    {
         $product = $request->item;
        $color  = $request->color;
        $size = $request->size;
        $items = qty_sold_inventory($product, $color, $size);
        echo "<span class='badge badge-warning font-weight-bold mt-2'>".$items."</span>";
    }

    public function calculate_buyorder_qtys (Request $request)
    {
        if($request->has('single_row'))
        {
            $subtotal = $request->qty * $request->price;
            return response()->json(['subtotal'=>$subtotal]);
        }
        else
        {
            $qty = 0;
            $total = 0;
            for ($i=0; $i < count($request->qty); $i++) { 
                $qty = $request->qty[$i] + $qty;
                $total = $total + ($request->qty[$i] * $request->price[$i]);
            }
            return response()->json(['qty'=>$qty, 'total'=>$total]);
        }
    }
}
