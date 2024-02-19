<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuyOrderItem extends Model
{
    public function order_info()
    {
        return $this->belongsTo('App\BuyOrder', 'order');
    }
    
    public function product_info()
    {
        return $this->belongsTo('App\Product', 'product');
    }

    public function color_info()
    {
        return $this->belongsTo('App\Color', 'color');
    }

    public function size_info()
    {
        return $this->belongsTo('App\Size', 'size');
    }
    
    public function similar_items($item)
    {
        $order = $this->order;
        $product = $this->product;
        return BuyOrderItem::where('order', '=', $order)->where('product', '=', $product)->get();
    }
}
