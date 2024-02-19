<?php

namespace App;

use App\Traits\Chat;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use Chat;
    protected $guarded=[];
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
    
    public function ruined_qty()
    {
        $items = RuinedItem::where('product', $this->product)->where('color', $this->color)->where('size', $this->size)->first();
        if($items === NULL)
        {
            return 0;
        }
        else
        {
            return $items->qty;
        }
    }





    protected static function booted()
    {
        static::updated(function ($inventory) {
            $inventory->notify(7, 7, 7, 7);
        });
    }
}
