<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTimeline extends Model
{
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

    public function created_by()
    {
        return $this->belongsTo('App\Admin', 'admin');
    }    
}
