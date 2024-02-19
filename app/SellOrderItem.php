<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellOrderItem extends Model
{
    public function order_info()
    {
        return $this->belongsTo('App\SellOrder', 'order');
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
}
